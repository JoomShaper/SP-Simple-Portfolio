<?php


/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\AdminController;
class SpsimpleportfolioControllerTags extends AdminController {
	public function getModel($name = 'Tag', $prefix = 'SpsimpleportfolioModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
