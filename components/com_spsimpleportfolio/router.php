<?php
/**
* @package com_spsimpleportfolio
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2020 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No Direct Access
defined ('_JEXEC') or die('Restricted Access');

class SpsimpleportfolioRouterBase {
	
	public static function buildRoute(&$query) {
		$params 	= JComponentHelper::getParams('com_spsimpleportfolio');
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		$noIDs 		= (bool) $params->get('sef_ids', 0);
		
		$segments 	= array();
		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid'])) {
			$menuItem = $menu->getActive();
			$menuItemGiven = false;
		} else {
			$menuItem = $menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}

		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_spsimpleportfolio') {
			$menuItemGiven = false;
			unset($query['Itemid']);
			unset($query['view']);
		}

		if (isset($query['view'])) {
			$view = $query['view'];
		} else {
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}

		// Are we dealing with an article or category that is attached to a menu item?
		if (($menuItem instanceof stdClass)
			&& $menuItem->query['view'] == $query['view']
			&& isset($query['id'])
			&& $menuItem->query['id'] == (int) $query['id']) {
			unset($query['view']);
			unset($query['id']);
			return $segments;
		}

		//Replace with menu
		$mview = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];

		//List view
		if ( $view == 'items' ) {
			if($mview != $view) {
				$segments[] = $view;
			}
			unset($query['view']);
		}

		// Single view
		if ( $noIDs && $view == 'item' ) {
			$segments[] = $view;
			//Remove ID
			$id_slug = explode(':', $query['id']);
			if(count($id_slug)>1) {
				$segments[] = $id_slug[1];
			} else {
				$segments[] = $query['id'];
			}
			unset($query['view']);
			unset($query['id']);
		} else {
			if (isset($query['view'])) {
				$segments[] = $query['view'];
				unset($query['view']);
			}
			if (isset($query['id'])) {
				$segments[] = $query['id'];
				unset($query['id']);
			}
		}

		$total = count($segments);
		for ($i = 0; $i < $total; $i++) {
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	public static function parseRoute(&$segments) {
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_spsimpleportfolio');
		$noIDs = (bool) $params->get('sef_ids', 0);
		$total = count((array) $segments);
		$vars = array();

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		if($total == 2)
		{
			if($noIDs) {
				$slug = preg_replace('/:/', '-', $segments[1]);
				$id = self::getItemId($slug);
			} else {
				list($id, $tmp) = explode(':', $segments[1], 2);
			}

			$vars['view'] 	= 'item';
			$vars['id']	= (int) $id;
		}

		return $vars;
	}

	public static function getItemId($slug) {
		$db = JFactory::getDbo();
		$dbQuery = $db->getQuery(true)
			->select( $db->quotename( 'id' ) )
			->from('#__spsimpleportfolio_items')
			->where( $db->quotename('alias') . '=' . $db->quote($slug));
		$db->setQuery($dbQuery);
		return $db->loadResult();
	}
}


if(JVERSION >= 4 ) {
	/**
	 * Routing class to support Joomla 4.0
	 *
	 */
	class SpsimpleportfolioRouter extends Joomla\CMS\Component\Router\RouterBase
	{
		public function build(&$query)
		{
			$segments = SpsimpleportfolioRouterBase::buildRoute($query);
			return $segments;
		}

		public function parse(&$segments)
		{
			$vars = SpsimpleportfolioRouterBase::parseRoute($segments);

			$segments = array();

			return $vars;
		}
	}
}

function SpsimpleportfolioBuildRoute(&$query)
{
	$segments = SpsimpleportfolioRouterBase::buildRoute($query);
	return $segments;
}

function SpsimpleportfolioParseRoute(&$segments)
{
	$vars = SpsimpleportfolioRouterBase::parseRoute($segments);
	return $vars;
}