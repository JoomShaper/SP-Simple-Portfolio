<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

class SpsimpleportfolioModelItems extends FOFModel
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function buildQuery($overrideLimits = false) {

		return parent::buildQuery();

	}
  
}
