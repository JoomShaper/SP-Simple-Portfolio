<?php
/**
 * @package     SP Simple Portfolio
 * @subpackage  mod_spsimpleportfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Filesystem\File;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;


BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_spsimpleportfolio/models', 'SpsimpleportfolioModel');

JLoader::register('SpsimpleportfolioHelper', JPATH_SITE . '/components/com_spsimpleportfolio/helpers/helper.php');

class ModSpsimpleportfolioHelper {

	public static function getItems($params) {

		$model = BaseDatabaseModel::getInstance('Items', 'SpsimpleportfolioModel', array('ignore_request' => true));

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*, a.id AS spsimpleportfolio_item_id , a.tagids AS spsimpleportfolio_tag_id, a.created AS created_on')
		->from($db->quoteName('#__spsimpleportfolio_items', 'a'))
		->where($db->quoteName('a.published') . ' = 1');
		
		// Filter by a single or group of categories
		if ($params->get('category_id') != '') {
			$categoryId = $params->get('category_id');
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
		}

		// ordering
		$ordering = $params->get('ordering', 'ordering:ASC');
		list($order, $direction) = explode(':', $ordering);
		
		$query->where($db->quoteName('a.access')." IN (" . implode( ',', Factory::getUser()->getAuthorisedViewLevels() ) . ")")
			->order($db->quoteName('a.' . $order) . ' ' . $direction)
			->setLimit($params->get('limit', 6));

		$db->setQuery($query);

		$items = $db->loadObjectList();

		$i = 0;
		foreach ($items as $key => & $item) {
			$tags = $model->getItemTags($item->tagids);
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
			$square 	= strtolower($params->get('square', '600x600'));
			$tower 		= strtolower($params->get('tower', '600X800'));
			$rectangle 	= strtolower($params->get('rectangle', '600x400'));
			$tower 		= strtolower($params->get('tower', '600x800'));
			$sizes 		= array(
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

			// tower

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

			$item->url = Route::_('index.php?option=com_spsimpleportfolio&view=item&id='. $item->id . ':' . $item->alias . SpsimpleportfolioHelper::getItemid($item->catid));

			$i++;
			if($i==11) {
				$i = 0;
			}
		}

		return $items;
	}
}