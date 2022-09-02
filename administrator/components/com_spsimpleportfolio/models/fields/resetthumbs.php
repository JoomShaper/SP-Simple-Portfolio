<?php

/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
class JFormFieldResetthumbs extends FormField {

	protected $type = 'Resetthumbs';

	protected function getInput() {

		HTMLHelper::_('jquery.framework');
		$doc = Factory::getDocument();
		$doc->addScriptDeclaration('jQuery(function($) {
			$("#btn-reset-thumbs").on("click", function() {
				$(this).attr("disabled","disabled").text($(this).data("generating"));
			});
		});');

		$url = 'index.php?option=com_spsimpleportfolio&task=resetThumbs';

		return '<a id="btn-reset-thumbs" class="btn btn-primary" data-generating="'. Text::_('COM_SPPORTFOLIO_RESET_THUMBNAIL_TEXT_LOADING') .'" href="'. $url .'">'. Text::_('COM_SPPORTFOLIO_RESET_THUMBNAIL_TEXT') .'</a>';
	}
}
