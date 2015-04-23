<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT . '/helpers/helper.php';
jimport( 'joomla.filesystem.file' );
$layout_type = $this->params->get('layout_type', 'default');


//Load the method jquery script.
JHtml::_('jquery.framework');

//Params
$params 	= JComponentHelper::getParams('com_spsimpleportfolio');
$square 	= strtolower( $params->get('square', '600x600') );
$rectangle 	= strtolower( $params->get('rectangle', '600x400') );
$tower 		= strtolower( $params->get('tower', '600x800') );

//Add js and css files
$doc = JFactory::getDocument();
$doc->addStylesheet( JURI::root(true) . '/components/com_spsimpleportfolio/assets/css/featherlight.min.css' );
$doc->addStylesheet( JURI::root(true) . '/components/com_spsimpleportfolio/assets/css/spsimpleportfolio.css' );
$doc->addScript( JURI::root(true) . '/components/com_spsimpleportfolio/assets/js/jquery.shuffle.modernizr.min.js' );
$doc->addScript( JURI::root(true) . '/components/com_spsimpleportfolio/assets/js/featherlight.min.js' );
$doc->addScript( JURI::root(true) . '/components/com_spsimpleportfolio/assets/js/spsimpleportfolio.js' );

$menu 	= JFactory::getApplication()->getMenu();
$itemId = '';
if(is_object($menu->getActive())) {
	$active = $menu->getActive();
	$itemId = '&Itemid=' . $active->id;
}

if( $this->params->get('show_page_heading') && $this->params->get( 'page_heading' ) ) {
	echo "<h1 class='page-header'>" . $this->params->get( 'page_heading' ) . "</h1>";
}

$i = 0;
//Sizes
$sizes = array(
	$rectangle,
	$tower,
	$square,

	$tower,
	$rectangle,
	$square,

	$square,
	$rectangle,
	$tower,

	$square,
	$tower,
	$rectangle
	);

?>

<div id="sp-simpleportfolio" class="sp-simpleportfolio sp-simpleportfolio-view-items layout-<?php echo str_replace('_', '-', $layout_type); ?>">


	<?php if($this->params->get('show_filter', 1)) { ?>
		<div class="sp-simpleportfolio-filter">
			<ul>
				<li class="active" data-group="all"><a href="#"><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_SHOW_ALL'); ?></a></li>
				<?php
					$filters = SpsimpleportfolioHelper::getTagList( $this->items );
					foreach ($filters as $filter) {
						?>
							<li data-group="<?php echo $filter->alias; ?>"><a href="#"><?php echo $filter->title; ?></a></li>
						<?php
					}	
				?>
			</ul>
		</div>
	<?php } ?>

	<?php
		//Videos
		foreach ($this->items as $key => $this->item) {

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

				echo '<iframe class="sp-simpleportfolio-lightbox" src="'. $video_src .'" width="500" height="281" id="sp-simpleportfolio-video'.$this->item->spsimpleportfolio_item_id.'" style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			}
		}
	?>

	<div class="sp-simpleportfolio-items sp-simpleportfolio-columns-<?php echo $this->params->get('columns', 3); ?>">
		<?php foreach ($this->items as $this->item) { ?>
			
			<?php
			$tags = SpsimpleportfolioHelper::getTags( $this->item->spsimpleportfolio_tag_id );
			$newtags = array();
			$filter = '';
			$groups = array();
			foreach ($tags as $tag) {
				$newtags[] 	 = $tag->title;
				$filter 	.= ' ' . $tag->alias;
				$groups[] 	.= '"' . $tag->alias . '"';
			}

			$groups = implode(',', $groups);

			?>

			<div class="sp-simpleportfolio-item" data-groups='[<?php echo $groups; ?>]'>
				<?php $this->item->url = JRoute::_('index.php?option=com_spsimpleportfolio&view=item&id='.$this->item->spsimpleportfolio_item_id.':'.$this->item->alias . $itemId); ?>
				
				<div class="sp-simpleportfolio-overlay-wrapper clearfix">
					
					<?php if($this->item->video) { ?>
						<span class="sp-simpleportfolio-icon-video"></span>
					<?php } ?>

					<?php if($this->params->get('thumbnail_type', 'masonry') == 'masonry') { ?>
						<img class="sp-simpleportfolio-img" src="<?php echo JURI::base(true) . '/images/spsimpleportfolio/' . $this->item->alias . '/' . JFile::stripExt(JFile::getName($this->item->image)) . '_' . $sizes[$i] . '.' . JFile::getExt($this->item->image); ?>" alt="<?php echo $this->item->title; ?>">
					<?php } else if($this->params->get('thumbnail_type', 'masonry') == 'rectangular') { ?>
						<img class="sp-simpleportfolio-img" src="<?php echo JURI::base(true) . '/images/spsimpleportfolio/' . $this->item->alias . '/' . JFile::stripExt(JFile::getName($this->item->image)) . '_'. $rectangle .'.' . JFile::getExt($this->item->image); ?>" alt="<?php echo $this->item->title; ?>">
					<?php } else { ?>
						<img class="sp-simpleportfolio-img" src="<?php echo JURI::base(true) . '/images/spsimpleportfolio/' . $this->item->alias . '/' . JFile::stripExt(JFile::getName($this->item->image)) . '_'. $square .'.' . JFile::getExt($this->item->image); ?>" alt="<?php echo $this->item->title; ?>">
					<?php } ?>

					<div class="sp-simpleportfolio-overlay">
						<div class="sp-vertical-middle">
							<div>
								<div class="sp-simpleportfolio-btns">
									<?php if( $this->item->video ) { ?>
										<a class="btn-zoom" href="#" data-featherlight="#sp-simpleportfolio-video<?php echo $this->item->spsimpleportfolio_item_id; ?>"><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_WATCH'); ?></a>
									<?php } else { ?>
										<a class="btn-zoom" href="<?php echo JURI::base(true) . '/images/spsimpleportfolio/' . $this->item->alias . '/' . JFile::stripExt(JFile::getName($this->item->image)) . '_'. $rectangle .'.' . JFile::getExt($this->item->image); ?>" data-featherlight="image"><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_ZOOM'); ?></a>
									<?php } ?>
									<a class="btn-view" href="<?php echo $this->item->url; ?>"><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_VIEW'); ?></a>
								</div>
								<?php if($layout_type!='default') { ?>
								<h3 class="sp-simpleportfolio-title">
									<a href="<?php echo $this->item->url; ?>">
										<?php echo $this->item->title; ?>
									</a>
								</h3>
								<div class="sp-simpleportfolio-tags">
									<?php echo implode(', ', $newtags); ?>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				
				<?php if($layout_type=='default') { ?>
					<div class="sp-simpleportfolio-info">
						<h3 class="sp-simpleportfolio-title">
							<a href="<?php echo $this->item->url; ?>">
								<?php echo $this->item->title; ?>
							</a>
						</h3>
						<div class="sp-simpleportfolio-tags">
							<?php echo implode(', ', $newtags); ?>
						</div>
					</div>
				<?php } ?>

			</div>
			
			<?php
			$i++;
			if($i==11) {
				$i = 0;
			}
			?>

		<?php } ?>
	</div>

	<?php if ($this->pagination->get('pages.total') >1) { ?>
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>
</div>




