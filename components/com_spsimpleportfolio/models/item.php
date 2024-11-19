<?php
/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2024 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class SpsimpleportfolioModelItem extends ItemModel {

	protected $_context = 'com_spsimpleportfolio.item';

	protected function populateState()
	{
		$app = Factory::getApplication('site');
		$itemId = $app->input->getInt('id');
		$this->setState('item.id', $itemId);
		$this->setState('filter.language', Multilanguage::isEnabled());
	}

	public function getItem( $itemId = null )
	{
		PluginHelper::importPlugin('spsimpleportfolio');
		$params = Factory::getApplication('com_spsimpleportfolio')->getParams();
		$limitstart = 0;
		$user = Factory::getUser();

		$itemId = (!empty($itemId))? $itemId : (int)$this->getState('item.id');

		if ( $this->_item == null )
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$itemId]))
		{
			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select('a.*, a.tagids AS spsimpleportfolio_tag_id, a.created AS created_on')
					->from('#__spsimpleportfolio_items as a')
					->where('a.id = ' . (int) $itemId);

				$query->select('l.title AS language_title')
					->leftJoin( $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

				$query->select('ua.name AS author_name')
					->leftJoin('#__users AS ua ON ua.id = a.created_by');

				// Filter by published state.
				$query->where('a.published = 1');

				if ($this->getState('filter.language')) {
					$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query);
				$data = $db->loadObject();

				// Items Model
				BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_spsimpleportfolio/models');
				$itemsModel = BaseDatabaseModel::getInstance('Items', 'SpsimpleportfolioModel');

				if (isset($data->tagids) && $data->tagids) {
					$data->spsimpleportfolio_tag_id = json_decode($data->tagids, true);
					$data->tags = $itemsModel->getItemTags($data->tagids, true);
				}

				if (empty($data)) {
					throw new Exception(Text::_('COM_SPSIMPLEPORTFOLIO_ERROR_ITEM_NOT_FOUND'), 404);
				}

				$user = Factory::getUser();
				$groups = $user->getAuthorisedViewLevels();
				if(!in_array($data->access, $groups)) {
					throw new Exception(Text::_('COM_SPSIMPLEPORTFOLIO_ERROR_NOT_AUTHORISED'), 404);
				}

				// Event trigger
				Factory::getApplication()->triggerEvent('onSPPortfolioPrepareContent', array( 'com_spsimpleportfolio.item', &$data, &$params, $limitstart ));
				
				$this->_item[$itemId] = $data;
			}
			catch (Exception $e) {
				if ($e->getCode() == 404 ) {
					throw new Exception($e->getMessage(), 404);
				} else {
					$this->setError($e);
					$this->_item[$itemId] = false;
				}
			}
		}

		return $this->_item[$itemId];
	}
}
