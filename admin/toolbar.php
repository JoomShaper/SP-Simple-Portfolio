<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

class SpsimpleportfolioToolbar extends FOFToolbar{

	function onBrowse(){
		JToolBarHelper::preferences('com_spsimpleportfolio');

		$categories_view = 'index.php?option=com_categories&view=categories&extension=com_spsimpleportfolio';
		JHtmlSidebar::addEntry(JText::_('COM_SPSIMPLEPORTFOLIO_CATEGORIES'), $categories_view, 'categories');

		parent::onBrowse();
	}
}