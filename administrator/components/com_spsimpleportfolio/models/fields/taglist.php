<?php


/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Database\DatabaseInterface;

class JFormFieldTaglist extends ListField
{
	public $type = 'Taglist';

	public $layout = 'joomla.form.field.list-fancy-select';

	protected $allowAdd = false;
	protected $customPrefix = '#new#';

	public function setup(\SimpleXMLElement $element, $value = null, $group = null)
	{
		$ok = parent::setup($element, $value, $group);

		if ($ok) {
			$this->allowAdd     = isset($this->element['allowAdd']) ? (bool) $this->element['allowAdd'] : false;
			$this->customPrefix = (string) ($this->element['customPrefix'] ?? '#new#');
		}

		return $ok;
	}

	protected function getOptions()
	{
		$doc = Factory::getDocument();
		$doc->addScript(Uri::base(true) . '/components/com_spsimpleportfolio/assets/js/tags.js');

		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('DISTINCT a.id AS value, a.title AS text')
			->from('#__spsimpleportfolio_tags AS a')
			->order('a.id ASC');

		$db->setQuery($query);
		$rows = (array) $db->loadObjectList();

		$options = [];
		foreach ($rows as $row) {
			$options[] = HTMLHelper::_('select.option', $row->value, $row->text);
		}

		return array_merge(parent::getOptions(), $options);
	}

	protected function getInput()
	{
		$data                 = $this->getLayoutData();
		$data['options']      = $this->getOptions();
		$data['allowCustom']  = $this->allowAdd;
		$data['customPrefix'] = $this->customPrefix;

		$renderer = $this->getRenderer($this->layout);
		$renderer->setComponent('com_spsimpleportfolio');
		$renderer->setClient(1);

		return $renderer->render($data);
	}
}
