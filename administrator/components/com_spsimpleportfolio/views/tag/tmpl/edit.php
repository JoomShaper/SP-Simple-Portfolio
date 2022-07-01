<?php


/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

$doc = Factory::getDocument();
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select', null, array('disable_search_threshold' => 0 ));
?>

<form action="<?php echo Route::_('index.php?option=com_spsimpleportfolio&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
  <div class="form-horizontal">
    <div class="row-fluid">
      <div class="span9">
        <?php echo $this->form->renderFieldset('basic'); ?>
      </div>

      <div class="span3">
        <fieldset class="form-vertical">
          <?php echo $this->form->renderFieldset('sidebar'); ?>
        </fieldset>
      </div>
    </div>
  </div>

  <input type="hidden" name="task" value="item.edit" />
  <?php echo HTMLHelper::_('form.token'); ?>
</form>
