<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2024 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView;

class SpsimpleportfolioViewItem extends HtmlView {

	protected $item;
	protected $params;

	function display($tpl = null) {
		// Assign data to the view
		$this->item = $this->get('Item');

		$app = Factory::getApplication();
		$this->params = $app->getParams();
		$menus = Factory::getApplication()->getMenu();
		$menu = $menus->getActive();

		if($menu) {
			$this->params->merge($menu->getParams());
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
			return false;
		}

		$this->_prepareDocument($this->item);
		parent::display($tpl);
	}

	protected function _prepareDocument($item) {
		$app   = Factory::getApplication();
		$title = null;

		// Because the application sets a default page title,
		$this->params->def('page_heading', $item->title);
		$title = $item->title;

		if (empty($title)) {
			$title = $app->get('sitename');
		} elseif ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} elseif ($app->get('sitename_pagetitles', 0) == 2) {
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);
		$this->document->addCustomTag('<meta content="'. $title .'" property="og:title" />');
		$this->document->addCustomTag('<meta content="website" property="og:type"/>');
		$this->document->addCustomTag('<meta content="'.Uri::current().'" property="og:url" />');
		$this->document->addCustomTag('<meta content="'. Uri::root().$item->image.'" property="og:image" />');

		if (isset($item->description) && $item->description) {
			$meta_desc = HTMLHelper::_('string.truncate', $item->description, 155, false, false);
			$this->document->setDescription($meta_desc);
			$this->document->addCustomTag('<meta content="'. $meta_desc .'" property="og:description" />');
		}

	}
}
