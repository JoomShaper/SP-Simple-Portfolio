<?php

/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2020 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die();

$controller = JControllerLegacy::getInstance('Spsimpleportfolio');

JLoader::register('SpsimpleportfolioHelper', __DIR__ . '/helpers/helper.php');

$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
$controller->redirect();
