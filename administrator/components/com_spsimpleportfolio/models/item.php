<?php

/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\String\StringHelper;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\MVC\Model\AdminModel;

class SpsimpleportfolioModelItem extends AdminModel {

	public function getTable($type = 'Item', $prefix = 'SpsimpleportfolioTable', $config = array()) {
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_spsimpleportfolio.item', 'item', array( 'control' => 'jform', 'load_data' => $loadData ) );

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData() {
		$data = Factory::getApplication()->getUserState( 'com_spsimpleportfolio.edit.item.data', array() );

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null) {
		if ($item = parent::getItem($pk)) {
			$item->tagids = $this->getTags($item->tagids);
		}

		return $item;
	}

	// Get Tags
	public function getTags($ids = '[]') {

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$ids = json_decode($ids);

		if(is_array($ids) && count($ids)) {
			$query
			->select('a.*')
			->from($db->quoteName('#__spsimpleportfolio_tags', 'a'))
			->where('(a.id IN ('. implode(',', $ids) .'))');

			$db->setQuery($query);
			$results = $db->loadObjectList();

			$tags = array();
			foreach ($results as $value) {
				$tags[] = $value->id;
			}
			$tags = array_unique($tags);
		} else {
			$tags = array();
		}

		return $tags;
	}

	public function save($data) {
		$input  = Factory::getApplication()->input;
		$filter = InputFilter::getInstance();

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save')) && (!isset($data['id']) || (int) $data['id'] == 0)) {
			if ($data['alias'] == null) {
				if (Factory::getConfig()->get('unicodeslugs') == 1) {
					$data['alias'] = OutputFilter::stringURLUnicodeSlug($data['title']);
				} else {
					$data['alias'] = OutputFilter::stringURLSafe($data['title']);
				}

				$table = Table::getInstance('Item', 'SpsimpleportfolioTable');

				while ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid']))) {
					$data['alias'] = StringHelper::increment($data['alias'], 'dash');
				}
			}
		}

		if (isset($data['tagids']) && is_array($data['tagids'])) {
			$data['tagids'] = $this->storeTags($data['tagids']);
		}

		if (parent::save($data)) {
			return true;
		}

		return false;
	}

	public function storeTags($tags = array()) {
		$itemTags = array();
		foreach ($tags as $tag) {
			if(strpos($tag, '#new#') !== false) {
				$title = str_replace('#new#', '', $tag);
				$alias = OutputFilter::stringURLSafe($title);

				// Insert New
				if(!$this->checkTag($alias)) {
					$object = new stdClass();
					$object->title = $title;
					$object->alias = $alias;
					$db = Factory::getDbo();
					$db->insertObject('#__spsimpleportfolio_tags', $object);
					$itemTags[] = $db->insertid();
				}
			} else {
				$itemTags[] = $tag;
			}
		}

		if(count($itemTags)) {
			return json_encode($itemTags);
		}

		return '[]';
	}

	private function checkTag($alias) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(alias)');
		$query->from($db->quoteName('#__spsimpleportfolio_tags'));
		$query->where($db->quoteName('alias') . ' = '. $db->quote($alias));
		$db->setQuery($query);
		return $db->loadResult();
	}

}
