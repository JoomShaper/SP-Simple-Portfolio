<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class SpsimpleportfolioController extends BaseController {
	protected $default_view = 'items';

	private function getPortfolioItems() {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'alias', 'image')));
		$query->from($db->quoteName('#__spsimpleportfolio_items'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function resetThumbs() {
		$items = $this->getPortfolioItems();

		foreach ($items as $item) {
			$image = JPATH_ROOT . '/' . $item->image;
			$alias = $item->alias;
			$folder = JPATH_ROOT . '/images/spsimpleportfolio/' . $alias;
			$base_name = File::stripExt(basename($item->image));
			$ext = SpsimpleportfolioHelper::getExt($image);

			$params = ComponentHelper::getParams('com_spsimpleportfolio');
			$sizes = array();

			// Square
			$square = strtolower($params->get('square', '600x600'));
			$squareArray = explode('x', $square);
			$sizes[$base_name . '_' .$square] = array($squareArray[0], $squareArray[1]);

			// Rectangle
			$rectangle = strtolower($params->get('rectangle', '600x400'));
			$rectangleArray = explode('x', $rectangle);
			$sizes[$base_name . '_' .$rectangle] = array($rectangleArray[0], $rectangleArray[1]);

			// Tower
			$tower = strtolower($params->get('tower', '600x800'));
			$towerArray = explode('x', $tower);
			$sizes[$base_name . '_' .$tower] = array($towerArray[0], $towerArray[1]);

			if(file_exists($image)) {
				if(!is_dir($folder)) {
					Folder::create($folder, 0755);
				}
				
				SpsimpleportfolioHelper::createThumbs($image, $sizes, $folder, '', $ext);
			}
		}

		$this->setRedirect('index.php?option=com_config&view=component&component=com_spsimpleportfolio&return='. base64_encode('index.php?option=com_spsimpleportfolio'), 'Thumbnails generated.');
	}

}
