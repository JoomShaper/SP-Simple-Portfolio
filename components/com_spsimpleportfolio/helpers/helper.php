<?php
/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2024 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
class SpsimpleportfolioHelper {

	public static function generateMeta($item = '') {
		return true;
	}

	public static function getTags($ids) {
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if(!is_array($ids)) {
			$ids = (array) json_decode($ids, true);
		}
		$ids = implode(',', $ids);
		$query->select($db->quoteName(array('id', 'title', 'alias')));
		$query->from($db->quoteName('#__spsimpleportfolio_tags'));
		$query->where($db->quoteName('id')." IN (" . $ids . ")");
		$query->order('id ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}


	public static function getTagList($items) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$tags = array();
		foreach ($items as $item) {
			$itemtags = json_decode( $item->tagids );
			foreach ($itemtags as $itemtag) {
				$tags[] = $itemtag;
			}
		}

		$json = json_encode(array_unique($tags));
		$result = self::getTags( $json );

		return $result;
	}

	public static function getItemId($catid = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'params')));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('link') . ' LIKE '. $db->quote('%option=com_spsimpleportfolio&view=items%'));
		$query->where($db->quoteName('client_id') . ' = '. $db->quote('0'));
		$query->where($db->quoteName('published') . ' = '. $db->quote('1'));
		if (Multilanguage::isEnabled())
		{
			$lang = Factory::getLanguage()->getTag();
			$query->where('language IN ("*","' . $lang . '")');
		}
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$itemId = 0;
		if(!empty($items))
		{
			foreach($items as $item)
			{
				$params = json_decode($item->params);
				$itemId = $item->id;
				if($catid)
				{
					if( (isset($params->catid) && $params->catid) && $params->catid == $catid)
					{
						$itemId = $item->id;
						return '&Itemid=' . $itemId;
					}
				}
			}
		}

		return '&Itemid=' . $itemId;
	}

	public static function getItemLink($id, $language)
	{
		// Create the link
        $link = 'index.php?com_spsimpleportfolio&view=item&id=' . $id;

        if ($language && $language !== '*' && Multilanguage::isEnabled()) {
            $link .= '&lang=' . $language;
        }

        return $link;
	}
}
