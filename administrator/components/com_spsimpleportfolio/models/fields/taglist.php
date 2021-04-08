<?php


/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2021 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
class JFormFieldTaglist extends FormField {

	public $type = 'Taglist';

	protected function getInput() {

		$doc = Factory::getDocument();
		$doc->addScript(Uri::base(true) . '/components/com_spsimpleportfolio/assets/js/tags.js');

		$html = array();
		$attr = '';
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		$options = $this->getTags();

		$html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

		return implode($html);
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
