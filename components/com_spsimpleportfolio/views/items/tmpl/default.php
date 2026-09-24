<?php
    /**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

    defined('_JEXEC') or die();

    use Joomla\CMS\Factory;
    use Joomla\CMS\HTML\HTMLHelper;
    use Joomla\CMS\Language\Text;
    use Joomla\CMS\Uri\Uri;

    //Load the method jquery script.
    HTMLHelper::_('jquery.framework');
    $doc = Factory::getDocument();
    $doc->addStylesheet(Uri::root(true) . '/components/com_spsimpleportfolio/assets/css/featherlight.min.css');
    $doc->addStylesheet(Uri::root(true) . '/components/com_spsimpleportfolio/assets/css/spsimpleportfolio.css');
    $doc->addScript(Uri::root(true) . '/components/com_spsimpleportfolio/assets/js/jquery.shuffle.modernizr.min.js');
    $doc->addScript(Uri::root(true) . '/components/com_spsimpleportfolio/assets/js/featherlight.min.js');
    $doc->addScript(Uri::root(true) . '/components/com_spsimpleportfolio/assets/js/spsimpleportfolio.js');

if ($this->params->get('show_page_heading') && $this->params->get('page_heading')) {?>
			<h1 class='page-header'><?php echo htmlspecialchars($this->params->get('page_heading'), ENT_QUOTES, 'UTF-8'); ?></h1>
<?php }?>

<div id="sp-simpleportfolio" class="sp-simpleportfolio sp-simpleportfolio-view-items layout-<?php echo $this->layout_type; ?>">
	<?php if ($this->params->get('show_filter', 1)): ?>
		<div class="sp-simpleportfolio-filter">
			<ul>
				<li class="active" data-group="all"><a href="#"><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_SHOW_ALL'); ?></a></li>
				<?php foreach ($this->tagList as $filter): ?>
				<li data-group="<?php echo htmlspecialchars($filter->alias, ENT_QUOTES, 'UTF-8'); ?>"><a href="#"><?php echo htmlspecialchars($filter->title, ENT_QUOTES, 'UTF-8'); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php
        //Videos
        foreach ($this->items as $key => $this->item) {
            if ($this->item->video) {
        $video = parse_url($this->item->video);

        switch ($video['host']) {
            case 'youtu.be':
                $video_id  = trim($video['path'], '/');
                $video_src = '//www.youtube.com/embed/' . $video_id;
                break;

            case 'www.youtube.com':
            case 'youtube.com':
                parse_str($video['query'], $query);
                $video_id  = $query['v'];
                $video_src = '//www.youtube.com/embed/' . $video_id;
                break;

            case 'vimeo.com':
            case 'www.vimeo.com':
                $video_id  = trim($video['path'], '/');
                $video_src = "//player.vimeo.com/video/" . $video_id;
        }

        echo '<iframe class="sp-simpleportfolio-lightbox" src="' . htmlspecialchars($video_src, ENT_QUOTES, 'UTF-8') . '" width="500" height="281" id="sp-simpleportfolio-video' . $this->item->id . '" style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            }
        }
    ?>
	<?php if (! empty($this->items)): ?>
		<div class="sp-simpleportfolio-items sp-simpleportfolio-columns-<?php echo $this->params->get('columns', 3); ?>">
			<?php foreach ($this->items as $this->item): ?>
				<div class="sp-simpleportfolio-item" data-groups='[<?php echo htmlspecialchars($this->item->groups, ENT_QUOTES, 'UTF-8'); ?>]'>
					<?php if ($this->layout_type == 'category-title-tag-list'): ?>
						<div class="sp-simpleportfolio-item-category-title"><?php echo $this->item->category_title; ?></div>
					<?php endif; ?>
					<div class="sp-simpleportfolio-overlay-wrapper clearfix">

						<?php if ($this->item->video): ?>
							<span class="sp-simpleportfolio-icon-video"></span>
						<?php endif; ?>

						<img class="sp-simpleportfolio-img" src="<?php echo htmlspecialchars($this->item->thumb, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>">

						<div class="sp-simpleportfolio-overlay">
							<div class="sp-vertical-middle">
								<div>
									<div class="sp-simpleportfolio-btns">
										<?php if ($this->item->video): ?>
											<a class="btn-zoom" href="#" data-featherlight="#sp-simpleportfolio-video<?php echo (int) $this->item->id; ?>"><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_WATCH'); ?></a>
										<?php else: ?>
											<a class="btn-zoom" href="<?php echo htmlspecialchars($this->item->popup_img_url, ENT_QUOTES, 'UTF-8'); ?>" data-featherlight="image"><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_ZOOM'); ?></a>
										<?php endif; ?>
										<a class="btn-view" href="<?php echo htmlspecialchars($this->item->url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_VIEW'); ?></a>
									</div>

									<?php if ($this->layout_type != 'default' && $this->layout_type != 'category-title-tag-list'): ?>
									<h3 class="sp-simpleportfolio-title">
										<a href="<?php echo htmlspecialchars($this->item->url, ENT_QUOTES, 'UTF-8'); ?>">
											<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>
										</a>
									</h3>
									<div class="sp-simpleportfolio-tags">
										<?php echo implode(', ', array_map(function ($t) {return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');}, $this->item->tags)); ?>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

					<?php if ($this->layout_type == 'default'): ?>
						<div class="sp-simpleportfolio-info">
						<h3 class="sp-simpleportfolio-title">
							<a href="<?php echo htmlspecialchars($this->item->url, ENT_QUOTES, 'UTF-8'); ?>">
								<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</h3>
						<div class="sp-simpleportfolio-tags">
							<?php echo implode(', ', array_map(function ($t) {return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');}, $this->item->tags)); ?>
						</div>
						</div>
					<?php endif; ?>

					<?php if ($this->layout_type == 'category-title-tag-list'): ?>
						<div class="sp-simpleportfolio-info">
						<h3 class="sp-simpleportfolio-title">
							<a href="<?php echo htmlspecialchars($this->item->url, ENT_QUOTES, 'UTF-8'); ?>">
								<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</h3>
						<div class="sp-simpleportfolio-tags">
							<ul class="sp-simpleportfolio-tags-list">
								<?php foreach ($this->item->tags as $tag): ?>
									<li class="sp-simpleportfolio-tags-list-item"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<div class="alert alert-danger"><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_ERROR_ITEM_NOT_FOUND_IN_CATEGORY'); ?></div>
	<?php endif; ?>

	<?php if ($this->pagination->pagesTotal > 1): ?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>
