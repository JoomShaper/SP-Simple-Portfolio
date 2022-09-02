<?php

/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('jquery.token');

$doc = Factory::getDocument();
$doc->addScript(Uri::root(true) . '/administrator/components/com_spsimpleportfolio/assets/js/script.js');
jimport('joomla.filesystem.file');
jimport( 'joomla.application.component.helper' );
$cParams = ComponentHelper::getParams('com_spsimpleportfolio');

$user		= Factory::getUser();
$userId		= $user->get('id');

$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder && !empty($this->items))
{
	if(JVERSION < 4)
	{
		$saveOrderingUrl = 'index.php?option=com_spsimpleportfolio&task=items.saveOrderAjax&tmpl=component';
		HTMLHelper::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	}
	else
	{
		$saveOrderingUrl = 'index.php?option=com_spsimpleportfolio&task=items.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
		HTMLHelper::_('draggablelist.draggable');
	}
}
?>

<form action="<?php echo Route::_('index.php?option=com_spsimpleportfolio&view=items'); ?>" method="post" id="adminForm" name="adminForm">
	<?php if (JVERSION < 4 && !empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>

		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="itemList">
				<thead>
				<tr>
					<th width="2%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder); ?>
					</th>
					<th width="2%" class="hidden-phone">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo Text::_('COM_SPSIMPLEPORTFOLIO_HEADING_IMAGE'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('grid.sort',  'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('grid.sort', 'COM_SPSIMPLEPORTFOLIO_HEADING_DATE_CREATED', 'a.created', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>

				<tfoot>
					<tr>
						<td colspan="12">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>

				<?php if(JVERSION < 4) :?>
				<tbody>
				<?php else: ?>
				<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
				<?php endif; ?>
					<?php if (!empty($this->items)) : ?>
						<?php foreach ($this->items as $i => $item) :
							$item->max_ordering = 0;
							$ordering   = ($listOrder == 'a.ordering');
							$canEdit    = $user->authorise('core.edit', 'com_spsimpleportfolio.item.' . $item->id) || ($user->authorise('core.edit.own',   'com_spsimpleportfolio.item.' . $item->id) && $item->created_by == $userId);
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
							$canChange  = $user->authorise('core.edit.state', 'com_spsimpleportfolio.item.' . $item->id) && $canCheckin;
							$link = Route::_('index.php?option=com_spsimpleportfolio&task=item.edit&id=' . $item->id);
						?>
							<?php if(JVERSION < 4) :?>
							<tr>
							<?php else: ?>
							<tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->catid; ?>">
							<?php endif; ?>
								<td class="order nowrap center hidden-phone">
									<?php
									$iconClass = '';
									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass ?>">
										<span class="icon-menu"></span>
									</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
									<?php endif; ?>
								</td>
								<td class="hidden-phone">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<td class="center">
									<?php
									$folder = JPATH_ROOT . '/images/spsimpleportfolio/' . $item->alias;
									$ext = File::getExt($item->image);
									$base_name = File::stripExt(basename($item->image));
									$thumb = $base_name . '_' .strtolower($cParams->get('square', '600x600')) . '.' . $ext;
									if(File::exists($folder . '/' . $thumb)) {
										?>
										<img src="<?php echo Uri::root() . 'images/spsimpleportfolio/' . $item->alias . '/' . $thumb; ?>" alt="" style="width: 64px; height: 64px; border: 1px solid #e5e5e5; background-color: #f5f5f5;">
										<?php
									} else {
										?>
										<img src="<?php echo Uri::root() . $item->image; ?>" alt="" style="width: 64px; height: 64px; border: 1px solid #e5e5e5; background-color: #f5f5f5;">
										<?php
									}
									?>
								</td>
								<td>
									<?php if ($item->checked_out) : ?>
										<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
									<?php endif; ?>

									<?php if ($canEdit) : ?>
										<a href="<?php echo Route::_('index.php?option=com_spsimpleportfolio&task=item.edit&id='.$item->id);?>">
											<?php echo $this->escape($item->title); ?>
										</a>
									<?php else : ?>
										<?php echo $this->escape($item->title); ?>
									<?php endif; ?>

									<span class="small break-word">
										<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
									</span>
									<?php if($item->catid) : ?>
									<div class="small">
										<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
									</div>
									<?php endif; ?>

									<?php if(isset($item->tags) && count($item->tags)) : ?>
										<div style="margin-top: 5px;">
											<?php echo Text::_('COM_SPSIMPLEPORTFOLIO_ITEMS_TAGS_LABEL'); ?>:
											<?php foreach ($item->tags as $key => $item->tag) : ?>
												<?php if ($canEdit) : ?>
												<a href="<?php echo Route::_('index.php?option=com_spsimpleportfolio&task=tag.edit&id='.$item->tag->id);?>"><small><?php echo $this->escape($item->tag->title); ?></small></a><?php if($key != (count($item->tags) - 1)) echo ","; ?>
												<?php else : ?>
													<small><?php echo $this->escape($item->tag->title); ?></small><?php if($key != (count($item->tags) - 1)) echo ","; ?>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>

									<?php if($canEdit) : ?>
										<?php if(PluginHelper::isEnabled('spsimpleportfolio', 'sppagebuilder')) : ?>
											<?php if($integration = SpsimpleportfolioHelper::isPageBuilderIntegrated($item)) : ?>
												<?php if($integration->url != '') : ?>
													<a class="btn btn-small btn-success" target="_blank" href="<?php echo $integration->url; ?>">
												<?php else : ?>
													<a class="btn btn-small btn-success action-edit-width-sppb" target="_blank" href="#" data-id="<?php echo $item->id; ?>" data-title="<?php echo $this->escape($item->title); ?>">
												<?php endif; ?>
													<?php echo Text::_('COM_SPSIMPLEPORTFOLIO_TITLE_EDIT_WITH_SPPAGEBUILDER'); ?>
												</a>
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
								</td>
								<td class="hidden-phone">
									<?php echo $this->escape($item->access_title); ?>
								</td>
								<td class="small hidden-phone">
										<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>" title="<?php echo Text::_('JAUTHOR'); ?>">
										<?php echo $this->escape($item->author_name); ?></a>
								</td>
								<td class="nowrap small hidden-phone">
									<?php
									echo $item->created > 0 ? HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')) : '-';
									?>
								</td>
								<td class="small nowrap hidden-phone">
									<?php if ($item->language == '*') : ?>
										<?php echo Text::alt('JALL', 'language'); ?>
									<?php else:?>
										<?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED'); ?>
									<?php endif;?>
								</td>

								<td class="center">
									<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'items.', $canChange);?>
								</td>

								<td align="center" class="hidden-phone">
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
