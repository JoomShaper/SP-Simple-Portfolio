<?php

/**
 * @package com_spsimpleportfolio
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No Direct Access
defined ('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\RouterViewConfiguration;

/**
 * Router class for com_spsimpleportfolio
 *
 * @since	1.0.0
 */
class SpsimpleportfolioRouter extends RouterView {

	protected $noIDs = false;

	/**
	 * The DB Object
	 *
	 * @var		DatabaseDriver
	 * @sine	4.0.0
	 */
	private $db = null;

	/**
	 * The query string generator.
	 *
	 * @var		object
	 * @since	4.0.0
	 */
	private $queryBuilder = null;

	/**
	 * SP Simple Portfolio Component router constructor
	 *
	 * @param   JApplicationCms  $app   The application object
	 * @param   JMenu            $menu  The menu object to work with
	 */

	public function __construct($app = null, $menu = null)
	{
		$params = ComponentHelper::getParams('com_spsimpleportfolio', true);
		$this->noIDs = (bool) $params->get('sef_ids');

		$this->db = Factory::getDbo();
		$this->queryBuilder = $this->db->getQuery(true);

		/**
		 * Registering the item(s) views (single, and list)
		 */
		$items = new RouterViewConfiguration('items');
		$this->registerView($items);
		$item = new RouterViewConfiguration('item');
		$item->setKey('id')->setParent($items);
		$this->registerView($item);

		parent::__construct($app, $menu);

		// $this->attachRule(new MenuRules($this)); // commented out for fixing the menu highlight issue for different category

		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
		
	}

	/**
	 * Get missing alias from the provided ID.
	 *
	 * @param	string		$id		The ID with or without the alias.
	 * @param	string		$table	The table name.
	 *
	 * @return	string		The alias string.
	 * @since	4.0.0
	 */
	private function getAlias(string $id, string $table) : string
	{
		try
		{
			$this->queryBuilder->clear();
			$this->queryBuilder->select('alias')
				->from($this->db->quoteName($table))
				->where($this->db->quoteName('id') . ' = ' . (int) $id);
			$this->db->setQuery($this->queryBuilder);

			return (string) $this->db->loadResult();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			return '';
		}
	}

	/**
	 * Get id from the alias.
	 *
	 * @param	string		$alias		The alias string.
	 * @param	string		$table		The table name.
	 *
	 * @return	int			The id.
	 * @since	4.0.0
	 */
	private function getId(string $alias, string $table) : int
	{
		try
		{
			$this->queryBuilder->clear();
			$this->queryBuilder->select('id')
				->from($this->db->quoteName($table))
				->where($this->db->quoteName('alias') . ' = ' . $this->db->quote($alias));
			$this->db->setQuery($this->queryBuilder);

			return (int) $this->db->loadResult();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			return 0;
		}
	}

	/**
	 * Get the view segment for the common views.
	 *
	 * @param	string	$id		The ID with or without alias.
	 * @param	string	$table	The table name.
	 *
	 * @return	array	The segment array.
	 * @since	4.0.0
	 */
	private function getViewSegment(string $id, string $table) : array
	{
		if (strpos($id, ':') === false)
		{
			$id .= ':' . $this->getAlias($id, $table);
		}

		if ($this->noIDs)
		{
			list ($key, $alias) = explode(':', $id, 2);

			return [$key => $alias];
		}

		return [(int) $id => $id];
	}

	/**
	 * get the view ID for the common pattern view.
	 *
	 * @param	string	$segment	The segment string.
	 * @param	string	$table		The table name.
	 *
	 * @return	int		The id.
	 * @since	4.0.0
	 */
	private function getViewId(string $segment, string $table) : int
	{
		return $this->noIDs
			? $this->getId($segment, $table)
			: (int) $segment;
	}

	/**
	 * Method to get the segment(s) for a item
	 *
	 * @param   string  $id     ID of the article to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getItemSegment($id, $query)
	{
		return $this->getViewSegment($id, '#__spsimpleportfolio_items');
	}

	/**
	 * Method to get the id for a item
	 *
	 * @param   string  $segment  Segment of the article to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getItemId($segment, $query)
	{
		return $this->getViewId($segment, '#__spsimpleportfolio_items');
	}
	
}

/**
 * Item router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function spsimpleportfolioBuildRoute(&$query)
{
	$app = Factory::getApplication();
	$router = new SpsimpleportfolioRouter($app, $app->getMenu());

	return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   3.3
 * @deprecated  4.0  Use Class based routers instead
 */
function spsimpleportfolioParseRoute($segments)
{
	$app = Factory::getApplication();
	$router = new SpsimpleportfolioRouter($app, $app->getMenu());

	return $router->parse($segments);
}
