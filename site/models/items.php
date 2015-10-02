<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

class SpsimpleportfolioModelItems extends FOFModel {

	public function __construct($config = array())
	{
		parent::__construct($config);
	}


	public function buildQuery($overrideLimits = false)
	{
		
		$table = $this->getTable();
		$tableName = $table->getTableName();
		$tableKey = $table->getKeyName();
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		// Call the behaviors
		$this->modelDispatcher->trigger('onBeforeBuildQuery', array(&$this, &$query));

		$alias = $this->getTableAlias();

		if ($alias)
		{
			$alias = ' AS ' . $db->qn($alias);
		}
		else
		{
			$alias = '';
		}

		$select = $this->getTableAlias() ? $db->qn($this->getTableAlias()) . '.*' : $db->qn($tableName) . '.*';

		$query->select($select)->from($db->qn($tableName) . $alias);

		//Frontend
		if(FOFPlatform::getInstance()->isFrontend()) {
			
			// Get Params
			$app = JFactory::getApplication();
			$params   = $app->getMenu()->getActive()->params; // get the active item

			//has category
			if ($params->get('category_id') != '') {
				$query->where($db->qn('category_id')." = ".$db->quote( $params->get('category_id') ));
			}

			//Enabled
			$query->where($db->qn('enabled')." = ".$db->quote('1'));
			//Access
			$query->where($db->quoteName('access')." IN (" . implode( ',', JFactory::getUser()->getAuthorisedViewLevels() ) . ")");
		}

		if (!$overrideLimits)
		{

			if(FOFPlatform::getInstance()->isFrontend()) {
				$order = 'ordering';
			} else {
				$order = $this->getState('filter_order', null, 'cmd');				
			}

			if (!in_array($order, array_keys($table->getData())))
			{
				$order = $tableKey;
			}

			$order = $db->qn($order);

			if ($alias)
			{
				$order = $db->qn($this->getTableAlias()) . '.' . $order;
			}

			if(FOFPlatform::getInstance()->isFrontend()) {
				$dir = 'ASC';
			} else {
				$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');			
			}
			
			$query->order($order . ' ' . $dir);
		}

		// Call the behaviors
		$this->modelDispatcher->trigger('onAfterBuildQuery', array(&$this, &$query));

		return $query;

	}


}