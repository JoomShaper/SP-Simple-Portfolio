<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

class SpsimpleportfolioTableTag extends FOFTable
{

	public function check() {

		$result = true;

		//Alias
		if(empty($this->alias)) {
			$this->alias = JFilterOutput::stringURLSafe($this->title);
		} else {
			$this->alias =JFilterOutput::stringURLSafe($this->alias);
		}

		$existingAlias = FOFModel::getTmpInstance('Tags','SpsimpleportfolioModel')
			->alias($this->alias)
			->getList(true);

		if(!empty($existingAlias)) {
			$count = 0;
			$k = $this->getKeyName();
			foreach($existingAlias as $item) {
				if($item->$k != $this->$k) $count++;
			}
			if($count) {
				$this->setError(JText::_('COM_SPSIMPLEPORTFOLIO_ALIAS_ERR_SLUGUNIQUE'));
				$result = false;
			}
		}

		return $result;
	}

}