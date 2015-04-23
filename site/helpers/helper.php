<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

class SpsimpleportfolioHelper {

	public static function generateMeta($item) {
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$title = null;

		$document->setTitle($item->title);
		$document->addCustomTag('<meta content="website" property="og:type"/>');
		$document->addCustomTag('<meta content="'.JURI::current().'" property="og:url" />');
		$document->setDescription( JHtml::_('string.truncate', $item->description, 155, false, false ) );
		$document->addCustomTag('<meta content="'. $item->title .'" property="og:title" />');
		$document->addCustomTag('<meta content="'. JURI::root().$item->image.'" property="og:image" />');
		$document->addCustomTag('<meta content="'. JHtml::_('string.truncate', $item->description, 155, false, false ) .'" property="og:description" />');
	
		return true;
	}

	public static function getTags($ids) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if(!is_array($ids)) {
			$ids = (array) json_decode($ids);
		}

		$ids = implode(',', $ids);

		$query->select($db->quoteName(array('spsimpleportfolio_tag_id', 'title', 'alias')));
		$query->from($db->quoteName('#__spsimpleportfolio_tags'));
		$query->where($db->quoteName('spsimpleportfolio_tag_id')." IN (" .$ids . ")");
		$query->where('language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');

		$db->setQuery($query);

		return $db->loadObjectList();
	}


	public static function getTagList($items) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$tags = array();

		foreach ($items as $item) {
			$itemtags = json_decode( $item->spsimpleportfolio_tag_id );
			foreach ($itemtags as $itemtag) {
				$tags[] = $itemtag;
			}
		}

		$json = json_encode(array_unique($tags));

		$result = self::getTags( $json );

		return $result;
	}

}
