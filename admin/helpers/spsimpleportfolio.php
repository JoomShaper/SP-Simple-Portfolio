<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

/**
 * This class is required since Joomla will look for a file in helpers/ats.php with a class and method named
 * AtsHelper::addSubmenu
 */

class SpsimpleportfolioHelper extends JHelperContent{

    public static function addSubmenu($vName){
    	if ($view = 'categories') {
    		$categories_view = 'index.php?option=com_categories&view=categories&extension=com_spsimpleportfolio';
    		$items_view = 'index.php?option=com_spsimpleportfolio&view=items';
    		$tegs_view = 'index.php?option=com_spsimpleportfolio&view=tags';

        	JHtmlSidebar::addEntry(JText::_('COM_SPSIMPLEPORTFOLIO_CATEGORIES'), $categories_view, $vName == 'categories');
        	JHtmlSidebar::addEntry(JText::_('COM_SPSIMPLEPORTFOLIO_TITLE_ITEMS'), $items_view, $vName == 'items');
        	JHtmlSidebar::addEntry(JText::_('COM_SPSIMPLEPORTFOLIO_TITLE_TAGS'), $tegs_view, $vName == 'tags');
    	}

    	
    }
}