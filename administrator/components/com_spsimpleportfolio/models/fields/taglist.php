<?php


/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2024 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

class JFormFieldTaglist extends JFormFieldList {

	public $type = 'Taglist';
	public $layout = 'joomla.form.field.list-fancy-select';

	protected function getOptions() {

		$doc = Factory::getDocument();
		$doc->addScript(Uri::base(true) . '/components/com_spsimpleportfolio/assets/js/tags.js');

		$tags = (array) $this->getTags();
		$options = [];

		foreach ($tags as $tag) {
			$options[] = HTMLHelper::_('select.option', $tag->value, $tag->text);
		}

		return array_merge(parent::getOptions(), $options);
	}

	private function getTags() {

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT a.id AS value, a.title AS text')
			->from('#__spsimpleportfolio_tags AS a');

		$query->order('a.id ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

}
