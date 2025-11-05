<?php

/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\String\StringHelper;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\DatabaseInterface;

class SpsimpleportfolioModelItem extends AdminModel
{

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

		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$ids = json_decode(is_null($ids) ? '[]' : $ids);

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
		$db = Factory::getDbo();
		$itemTags = array();

		// Flatten any nested arrays
		$flat = $this->flattenTagScalars($tags);

		foreach ($flat as $tag) {
			if ($tag === null) {
				continue;
			}

			if (is_string($tag)) {
				$tag = trim($tag);
			}

			// Keep numeric IDs 
			if ($tag !== '' && is_numeric($tag)) {
				$itemTags[] = (int) $tag;
				continue;
			}

			// Create-or-use for '#new#Title'
			if (is_string($tag) && strpos($tag, '#new#') === 0) {
				$title = trim(substr($tag, 5));
				if ($title === '') {
					continue;
				}

				$alias = OutputFilter::stringURLSafe($title);

				// Insert new tag
				$object = new \stdClass();
				$object->title = $title;
				$object->alias = $alias;

				$db->insertObject('#__spsimpleportfolio_tags', $object);
				$itemTags[] = (int) $db->insertid();
			}
		}

		$itemTags = array_map('strval', $itemTags);

		return count($itemTags) ? json_encode($itemTags) : '[]';
	}

	/**
	 * Recursively flattens nested arrays to a list of scalar values.
	 */

	private function flattenTagScalars($input): array
	{
		$out = [];

		if (is_array($input)) {
			array_walk_recursive($input, function ($value) use (&$out) {
				if (is_scalar($value)) {
					$out[] = $value;
				}
			});
		} elseif (is_scalar($input)) {
			$out[] = $input;
		}

		return $out;
	}

}
