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
use Joomla\CMS\MVC\Model\AdminModel;
class SpsimpleportfolioModelTag extends AdminModel {

	public function getTable($type = 'Tag', $prefix = 'SpsimpleportfolioTable', $config = array()) {
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_spsimpleportfolio.tag', 'tag', array( 'control' => 'jform', 'load_data' => $loadData ) );

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData() {
		$data = Factory::getApplication()->getUserState( 'com_spsimpleportfolio.edit.tag.data', array() );

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
}
