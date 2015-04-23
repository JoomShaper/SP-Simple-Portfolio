<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die();

class SpsimpleportfolioControllerItems extends FOFController
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function onBeforeBrowse()
	{

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$this->getThisModel()->limit( $params->get('limit', 12) );
		$this->getThisModel()->limitstart($this->input->getInt('limitstart', 0));
	
		return true;
	}
}
