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
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Call the behaviors
		$this->modelDispatcher->trigger('onBeforeBuildQuery', array(&$this, &$query));

		$query->select( array('a.*') );
	    $query->from($db->quoteName('#__spsimpleportfolio_items', 'a'));
	    $query->where($db->quoteName('a.enabled')." = ".$db->quote('1'));

	    //Language
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
	    //Access
		$query->where($db->quoteName('access')." IN (" . implode( ',', JFactory::getUser()->getAuthorisedViewLevels() ) . ")");

	    $query->order($db->quoteName('a.ordering') . ' DESC');

	    // Call the behaviors
		$this->modelDispatcher->trigger('onAfterBuildQuery', array(&$this, &$query));

	    return $query;
	}

}