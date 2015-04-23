<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

class SpsimpleportfolioTableItem extends FOFTable
{

	public function check() {

		$result = true;

		//Alias
		if(empty($this->alias)) {
			// Auto-fetch a alias
			$this->alias = JFilterOutput::stringURLSafe($this->title);
		} else {
			// Make sure nobody adds crap characters to the alias
			$this->alias =JFilterOutput::stringURLSafe($this->alias);
		}

		$existingAlias = FOFModel::getTmpInstance('Items','SpsimpleportfolioModel')
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

		//Tags
		if (is_array($this->spsimpleportfolio_tag_id))
		{
			if (!empty($this->spsimpleportfolio_tag_id))
			{
				$this->spsimpleportfolio_tag_id = json_encode($this->spsimpleportfolio_tag_id);
			}
		}
		if (is_null($this->spsimpleportfolio_tag_id) || empty($this->spsimpleportfolio_tag_id))
		{
			$this->spsimpleportfolio_tag_id = '';
		}

		//Generate Thumbnails
		if($result) {

			$params 	= JComponentHelper::getParams('com_spsimpleportfolio');
			$square 	= strtolower( $params->get('square', '600x600') );
			$rectangle 	= strtolower( $params->get('rectangle', '600x400') );
			$tower 		= strtolower( $params->get('tower', '600x800') );
			$cropratio 	= $params->get('cropratio', 4);

			if(!is_null($this->image)) {
				jimport( 'joomla.filesystem.file' );
				jimport( 'joomla.filesystem.folder' );
				jimport( 'joomla.image.image' );

				$image = JPATH_ROOT . '/' . $this->image;
				$path  = JPATH_ROOT . '/images/spsimpleportfolio/' . $this->alias;

				if(!file_exists($path)) {
					JFolder::create( $path, 0755 );
				}

				$sizes = array($square, $rectangle, $tower);
				$image = new JImage($image);
				$image->createThumbs($sizes, $cropratio, $path);
			}

		}

		return $result;
	}

	public function onAfterLoad(&$result) {

		if(!is_array($this->spsimpleportfolio_tag_id)) {
			if(!empty($this->spsimpleportfolio_tag_id)) {
				$this->spsimpleportfolio_tag_id = json_decode($this->spsimpleportfolio_tag_id, true);
			}
		}

		if(is_null($this->spsimpleportfolio_tag_id) || empty($this->spsimpleportfolio_tag_id)) {
			$this->spsimpleportfolio_tag_id = array();
		}

		return parent::onAfterLoad($result);
	}
}