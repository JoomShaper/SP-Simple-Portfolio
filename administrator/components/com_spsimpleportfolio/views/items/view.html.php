<?php

/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class SpsimpleportfolioViewItems extends HtmlView {

	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;

	function display($tpl = null) {

		// Get data from the model
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->canDo = ContentHelper::getActions('com_spsimpleportfolio');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode('<br />', $errors), 500);
			return false;
		}

		$this->addToolBar();

		return parent::display($tpl);

	}

	protected function addToolBar() {
		ToolbarHelper::title(Text::_('COM_SPSIMPLEPORTFOLIO_MANAGER') .  Text::_('COM_SPSIMPLEPORTFOLIO_ITEMS'), 'pictures');

		if ($this->canDo->get('core.create')) {
			ToolbarHelper::addNew('item.add', 'JTOOLBAR_NEW');
		} if ($this->canDo->get('core.edit')) {
			ToolbarHelper::editList('item.edit', 'JTOOLBAR_EDIT');
		}

		if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
			ToolbarHelper::deleteList('', 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($this->canDo->get('core.edit.state')) {
			ToolbarHelper::trash('items.trash');
		}

		if ($this->canDo->get('core.admin')) {
			ToolbarHelper::divider();
			ToolbarHelper::preferences('com_spsimpleportfolio');
		}
	}

	/**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
            'a.title'    	=> Text::_('JGLOBAL_TITLE'),
            'a.access'		=> Text::_('JGRID_HEADING_ACCESS'),
            'a.created_by'	=> Text::_('JAUTHOR'),
            'a.created'		=> Text::_('COM_SPSIMPLEPORTFOLIO_HEADING_DATE_CREATED'),
            'a.language'	=> Text::_('JGRID_HEADING_LANGUAGE'),
            'a.published'	=> Text::_('JSTATUS'),
            'a.id'			=> Text::_('JGRID_HEADING_ID')
        );
    }
}
