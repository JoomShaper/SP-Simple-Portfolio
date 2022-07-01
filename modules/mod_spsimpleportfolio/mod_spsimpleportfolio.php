<?php
/**
 * @package     SP Simple Portfolio
 * @subpackage  mod_spsimpleportfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

require_once __DIR__ . '/helper.php';

HTMLHelper::_('jquery.framework');
BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_spsimpleportfolio/models');
require_once JPATH_BASE . '/components/com_spsimpleportfolio/helpers/helper.php';

$doc = Factory::getDocument();
$doc->addStylesheet( Uri::root(true) . '/components/com_spsimpleportfolio/assets/css/featherlight.min.css' );
$doc->addStylesheet( Uri::root(true) . '/components/com_spsimpleportfolio/assets/css/spsimpleportfolio.css' );
$doc->addScript( Uri::root(true) . '/components/com_spsimpleportfolio/assets/js/jquery.shuffle.modernizr.min.js' );
$doc->addScript( Uri::root(true) . '/components/com_spsimpleportfolio/assets/js/featherlight.min.js' );
$doc->addScript( Uri::root(true) . '/components/com_spsimpleportfolio/assets/js/spsimpleportfolio.js' );

$cParams      = ComponentHelper::getParams('com_spsimpleportfolio');

if($cParams) {
    $params->merge($cParams);
}

$items = ModSpsimpleportfolioHelper::getItems($params);
foreach ($items as $item) {
    // if thumb uploaded for listing
    $item->thumb = ( isset($item->thumbnail) && $item->thumbnail ) ? $item->thumbnail : $item->thumb;
}
$model = BaseDatabaseModel::getInstance('Items', 'SpsimpleportfolioModel');
$tagList = $model->getTagList($items);

$moduleclass_sfx = htmlspecialchars(is_null($params->get('moduleclass_sfx')) ? '' : $params->get('moduleclass_sfx'));

require ModuleHelper::getLayoutPath('mod_spsimpleportfolio', $params->get('layout', 'default'));
