<?php
/**
 * @package     SP Simple Portfolio
 * @subpackage  mod_spsimpleportfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

class ModSpsimpleportfolioHelper {

	public static function getItems($params) {
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
		->from($db->quoteName('#__spsimpleportfolio_items'))
		->where($db->quoteName('enabled') . ' = 1');
		//has category
		if ($params->get('category_id') != '') {
			$query->where($db->qn('category_id')." = ".$db->quote( $params->get('category_id') ));
		}
		$query->where($db->quoteName('access')." IN (" . implode( ',', JFactory::getUser()->getAuthorisedViewLevels() ) . ")")
		->order($db->quoteName('ordering') . ' ASC')
		->setLimit($params->get('limit', 6));
		
		$db->setQuery($query);

		$items = $db->loadObjectList();

		return $items;

	}

	public static function getItemid() {
		$db = JFactory::getDbo();
 
		$query = $db->getQuery(true); 
		$query->select($db->quoteName(array('id')));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('link') . ' LIKE '. $db->quote('%option=com_spsimpleportfolio&view=items%'));
		$query->where($db->quoteName('published') . ' = '. $db->quote('1'));
		$db->setQuery($query);
		$result = $db->loadResult();

		if(count($result)) {
			return '&Itemid=' . $result;
		}

		return;
	}

}
