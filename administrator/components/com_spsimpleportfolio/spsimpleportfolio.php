<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2020 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

if (!Factory::getUser()->authorise('core.manage', 'com_spsimpleportfolio')) {
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Require helper file
JLoader::register('SpsimpleportfolioHelper', JPATH_COMPONENT . '/helpers/spsimpleportfolio.php');
$controller = BaseController::getInstance('Spsimpleportfolio');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
