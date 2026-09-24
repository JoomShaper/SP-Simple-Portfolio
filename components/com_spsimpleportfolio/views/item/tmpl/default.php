<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$doc = Factory::getDocument();
$doc->addStylesheet( Uri::root(true) . '/components/com_spsimpleportfolio/assets/css/spsimpleportfolio.css' );

//video
if($this->item->video) {
	$video = parse_url($this->item->video);

	switch($video['host']) {
		case 'youtu.be':
		$video_id 	= trim($video['path'],'/');
		$video_src 	= '//www.youtube.com/embed/' . $video_id;
		break;

		case 'www.youtube.com':
		case 'youtube.com':
		parse_str($video['query'], $query);
		$video_id 	= $query['v'];
		$video_src 	= '//www.youtube.com/embed/' . $video_id;
		break;

		case 'vimeo.com':
		case 'www.vimeo.com':
		$video_id 	= trim($video['path'],'/');
		$video_src 	= "//player.vimeo.com/video/" . $video_id;
	}
}

$client_title_condition = (isset($this->item->client) && $this->item->client);
$client_avatar_condition= (isset($this->item->client_avatar) && $this->item->client_avatar);
?>

<div id="sp-simpleportfolio" class="sp-simpleportfolio sp-simpleportfolio-view-item">
	<div class="sp-simpleportfolio-image">
		<?php if($this->item->video) : ?>
			<div class="sp-simpleportfolio-embed">
				<iframe src="<?php echo htmlspecialchars($video_src, ENT_QUOTES, 'UTF-8'); ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
			</div>
		<?php else: ?>
			<?php if($this->item->image): ?>
				<img class="sp-simpleportfolio-img" src="<?php echo htmlspecialchars($this->item->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>">
			<?php else: ?>
				<img class="sp-simpleportfolio-img" src="<?php echo htmlspecialchars($this->item->thumbnail, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?>">
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<div class="sp-simpleportfolio-details clearfix">
		<div class="sp-simpleportfolio-description">
			<h2><?php echo htmlspecialchars($this->item->title, ENT_QUOTES, 'UTF-8'); ?></h2>
			<?php echo HTMLHelper::_('content.prepare', $this->item->description); ?>
		</div>

		<div class="sp-simpleportfolio-meta">
			<?php if( $client_title_condition || $client_avatar_condition) : ?>
				<h4><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_PROJECT_CLIENT'); ?></h4>
				<div class="sp-simpleportfolio-client">
					<?php if( $client_avatar_condition ) : ?>
						<?php $client_avatar_alt = ($client_title_condition) ? $this->item->client : $this->item->title; ?>
						<div class="sp-simpleportfolio-client-avatar">
							<img src="<?php echo htmlspecialchars(Uri::root() . $this->item->client_avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($client_avatar_alt, ENT_QUOTES, 'UTF-8'); ?>">
						</div>
					<?php endif; ?>
					<?php if( $client_title_condition ) : ?>
						<div class="sp-simpleportfolio-client-title">
							<?php echo htmlspecialchars($this->item->client, ENT_QUOTES, 'UTF-8'); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="sp-simpleportfolio-created">
				<h4><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_PROJECT_DATE'); ?></h4>
				<?php echo HTMLHelper::_('date', $this->item->created_on, Text::_('DATE_FORMAT_LC3')); ?>
			</div>

			<div class="sp-simpleportfolio-tags">
				<h4><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_PROJECT_TAGS'); ?></h4>
				<?php echo implode(', ', $this->item->tags); ?>
			</div>

			<?php if ($this->item->url) : ?>
				<div class="sp-simpleportfolio-link">
					<a class="btn btn-primary" target="_blank" href="<?php echo htmlspecialchars($this->item->url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo Text::_('COM_SPSIMPLEPORTFOLIO_VIEW_PROJECT'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
