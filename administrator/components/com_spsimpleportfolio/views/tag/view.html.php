<?php

/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class SpsimpleportfolioViewTag extends HtmlView {

	protected $form;
	protected $item;
	protected $canDo;
	protected $id;

	public function display($tpl = null) {
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->id = $this->item->id;

		$this->canDo = ContentHelper::getActions('com_spsimpleportfolio', 'tag', $this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode('<br />', $errors), 500);
			return false;
		}

		$this->addToolBar();
		parent::display($tpl);
	}

	protected function addToolBar() {
		$input = Factory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);
		ToolbarHelper::title(Text::_('COM_SPSIMPLEPORTFOLIO_MANAGER') .  ($isNew ? Text::_('COM_SPSIMPLEPORTFOLIO_TAG_NEW') : Text::_('COM_SPSIMPLEPORTFOLIO_TAG_EDIT')), 'pictures');

		if ($this->canDo->get('core.edit') || ($this->canDo->get('core.edit.own') && $this->item->created_by == $user->id)) {
			ToolbarHelper::apply('tag.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('tag.save', 'JTOOLBAR_SAVE');
		}

		ToolbarHelper::cancel('tag.cancel', 'JTOOLBAR_CLOSE');
	}
}
