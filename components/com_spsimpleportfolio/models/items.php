<?php

/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2024 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Filesystem\File;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;

class SpsimpleportfolioModelItems extends ListModel {

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'image', 'a.image',
				'thumbnail', 'a.thumbnail',
				'client', 'a.client'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {
		$app = Factory::getApplication();
		$params = new Registry;

		if ($menu = $app->getMenu()->getActive()) {
			$params->loadString($menu->getParams());

			$limit = $params->get('limit', 12);
			$this->setState('list.limit', $limit);

			$limitstart = $app->input->get('limitstart', 0, 'uint');
			$this->setState('list.start', $limitstart);

			$catid = $params->get('catid', 0);
			$this->setState('category.id', $catid);
		}
	}

	protected function getListQuery() {
		$app = Factory::getApplication();
		$user = Factory::getUser();
		if($app->getMenu()->getActive())
		{
			// Get Params
			$params  = $app->getMenu()->getActive()->getParams();
			// params item
			list($order, $direction) = explode(':', $params->get('ordering', 'ordering:ASC'));

		}
		else
		{
			$order = 'ordering';
			$direction = 'ASC';
		}
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*, a.id AS spsimpleportfolio_item_id , a.tagids AS spsimpleportfolio_tag_id');
		$query->from($db->quoteName('#__spsimpleportfolio_items', 'a'));

		// Join over the categories.
		$query->select('c.title AS category_title, c.alias AS category_alias')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		//Authorised
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('a.access IN (' . $groups . ')');

		// Filter by a single or group of categories
		$categoryId = $this->getState('category.id');
		if (is_numeric($categoryId) && $categoryId > 0)
		{
			// Add subcategory check
			$categoryEquals       = 'a.catid =' . (int) $categoryId;

			// Create a subquery for the subcategory list
			$subQuery = $db->getQuery(true)
				->select('sub.id')
				->from('#__categories as sub')
				->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt')
				->where('this.id = ' . (int) $categoryId);

			// Add the subquery to the main query
			$query->where('(' . $categoryEquals . ' OR a.catid IN (' . (string) $subQuery . '))');
		}
		elseif (is_array($categoryId) && (count($categoryId) > 0))
		{
			$categoryId = ArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);

			if (!empty($categoryId))
			{
				$query->where('a.catid IN (' . $categoryId . ')');
			}
		}

		// Filter by language
		$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		$query->where('a.published = 1');
		$query->order($db->quoteName('a.' . $order) . ' ' . $direction);

		return $query;
	}

	public function getItems() {
		$items = parent::getItems();

		$menus = Factory::getApplication()->getMenu();
		$menu = $menus->getActive();
		$app = Factory::getApplication();
		$params = $app->getParams();
		if($menu) {
			$params->merge($menu->getParams());
		}

		$i = 0;
		foreach ($items as $key => & $item) {
			$tags = $this->getItemTags($item->tagids);
			$newtags = array();
			$filter = '';
			$groups = array();

			foreach ($tags as $tag) {
				$newtags[] = $tag->title;
				$filter .= ' ' . $tag->alias;
				$groups[] .= '"' . $tag->alias . '"';
			}

			$item->groups = implode(',', $groups);
			$item->tags = $newtags;

			// Sizes
			$square = strtolower($params->get('square', '600x600'));
			$rectangle = strtolower($params->get('rectangle', '600x400'));
			$tower = strtolower($params->get('tower', '600x800'));
			$sizes = array(
				$rectangle,
				$tower,
				$square,
				$tower,
				$rectangle,
				$square,
				$square,
				$rectangle,
				$tower,
				$square,
				$tower,
				$rectangle
			);

			$thumb_type = $params->get('thumbnail_type', 'masonry');
			if($thumb_type == 'masonry') {
				$item->thumb = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_' . $sizes[$i] . '.' . File::getExt($item->image);
			} else if($thumb_type == 'rectangular') {
				$item->thumb = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_'. $rectangle .'.' . File::getExt($item->image);
			} else if($thumb_type == 'tower') {
				$item->thumb = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_'. $tower .'.' . File::getExt($item->image);
			} else {
				$item->thumb = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_'. $square .'.' . File::getExt($item->image);
			}

			$popup_image = $params->get('popup_image', 'default');
			if($popup_image == 'quare') {
				$item->popup_img_url = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_'. $square .'.' . File::getExt($item->image);
			} else if($popup_image == 'rectangle') {
				$item->popup_img_url = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_'. $rectangle .'.' . File::getExt($item->image);
			} else if($popup_image == 'tower') {
				$item->popup_img_url = Uri::base(true) . '/images/spsimpleportfolio/' . $item->alias . '/' . File::stripExt(basename($item->image)) . '_'. $tower .'.' . File::getExt($item->image);
			} else {
				$item->popup_img_url = Uri::base() . $item->image;
			}

			$url_string = Route::_('index.php?option=com_spsimpleportfolio&view=item&id='. $item->id . ':' . $item->alias . SpsimpleportfolioHelper::getItemid($item->catid));

			$item->url = Route::_($url_string);

			$i++;
			if($i==11) {
				$i = 0;
			}
		}

		return $items;
	}

	public function getTagList($items) {
		try {
			$db = Factory::getDbo();
			$query = $db->getQuery(true);

			$tags = array();

			foreach ($items as $item) {
				$itemtags = json_decode( $item->tagids );
				foreach ($itemtags as $itemtag) {
					$tags[] = $itemtag;
				}
			}

			$json = json_encode(array_unique($tags));
			$result = $this->getItemTags($json);
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
		

		return $result;
	}

	public function getItemTags($ids, $array = false) {

		try {
			$db = Factory::getDbo();
			$query = $db->getQuery(true);

			if(!is_array($ids)) {
				$ids = (array) json_decode($ids, true);
			}

			$ids = implode(',', $ids);
			$query->select($db->quoteName(array('id', 'title', 'alias')));
			$query->from($db->quoteName('#__spsimpleportfolio_tags'));
			if (!empty($ids))
			{
				$query->where($db->quoteName('id')." IN (" . $ids . ")");
			}
			$query->order('title ASC');
			$db->setQuery($query);

			$items = $db->loadObjectList();

			if($array == true) {
				$tags = array();
				foreach ($items as $item) {
					$tags[] = $item->title;
				}
				return $tags;
			} else {
				return $items;
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
		

		return array();
	}
}