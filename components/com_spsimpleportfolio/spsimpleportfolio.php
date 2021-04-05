<?php

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2021 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die();

$controller = BaseController::getInstance('Spsimpleportfolio');

JLoader::register('SpsimpleportfolioHelper', __DIR__ . '/helpers/helper.php');

$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));
$controller->redirect();
