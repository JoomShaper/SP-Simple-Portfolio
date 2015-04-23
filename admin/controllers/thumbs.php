<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.image.image' );

class SpsimpleportfolioControllerThumbs extends FOFController
{

	public function resetThumbs()
	{

		$items = self::getItems();
		
		//Get Params
		$params 	= JComponentHelper::getParams('com_spsimpleportfolio');
		$square 	= strtolower( $params->get('square', '600x600') );
		$rectangle 	= strtolower( $params->get('rectangle', '600x400') );
		$tower 		= strtolower( $params->get('tower', '600x800') );
		$cropratio 	= $params->get('cropratio', 4);

		if(count($items)) {

			//Removing old thumbs
			foreach ($items as $item) {
				$folder = JPATH_ROOT . '/images/spsimpleportfolio/' . $item->alias;

				if(JFolder::exists($folder)) {
					JFolder::delete($folder);
				}
			}

			//Creating Thumbs
			foreach ($items as $item) {
				
				$image = JPATH_ROOT . '/' . $item->image;
				$path  = JPATH_ROOT . '/images/spsimpleportfolio/' . $item->alias;

				if(!file_exists($path)) {
					JFolder::create( $path, 0755 );
				}

				$sizes = array($square, $rectangle, $tower);
				$image = new JImage($image);
				$image->createThumbs($sizes, $cropratio, $path);
			}

		}

		$this->setRedirect('index.php?option=com_config&view=component&component=com_spsimpleportfolio&path=&return='. base64_encode('index.php?option=com_spsimpleportfolio'), 'Thumbnails generated.');

	}

	private static function getItems() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('spsimpleportfolio_item_id', 'alias', 'image')));
		$query->from($db->quoteName('#__spsimpleportfolio_items'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

}
