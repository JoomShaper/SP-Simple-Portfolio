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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\ApplicationHelper;

class SpsimpleportfolioTableItem extends Table {

	public function __construct(&$db) {
		parent::__construct('#__spsimpleportfolio_items', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

		if (!(int) $this->created) {
			$this->created = $date->toSql();
		}
		if (empty($this->created_by)) {
			$this->created_by = $user->get('id');
		}

		if (!(int) $this->modified) {
			$this->modified = $date->toSql();
		}
		if (empty($this->modified_by)) {
			$this->modified_by = $user->get('id');
		}

		if (!(int) $this->checked_out_time) {
			$this->checked_out_time = $date->toSql();
		}
		if (empty($this->checked_out)) {
			$this->checked_out = $user->get('id');
		}

		// Verify that the alias is unique
		$table = Table::getInstance('Item', 'SpsimpleportfolioTable');
		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0)){
			$this->setError(Text::_('COM_SPSIMPLEPORTFOLIO_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		return parent::store($updateNulls);
	}

	public function check() {
		// Check for valid name.
		if (trim($this->title) == '') {
			throw new UnexpectedValueException(sprintf('The title is empty'));
		}

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;

	}
}
