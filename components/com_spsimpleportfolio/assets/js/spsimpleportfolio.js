/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2024 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

jQuery(window).on('load', function() {
	var $container 	= jQuery('.sp-simpleportfolio-items');
	var $sizer = $container.find('.shuffle__sizer');
	var $infoMaxHeight = 0;

	jQuery('.sp-simpleportfolio-items .sp-simpleportfolio-item').each(function(){
		var $currentHeight = jQuery(this).find('.sp-simpleportfolio-info').height();
		if ($currentHeight > $infoMaxHeight) {
			$infoMaxHeight = $currentHeight;
		}
	});

	jQuery('.sp-simpleportfolio-info').height($infoMaxHeight);

	$container.shuffle({
		itemSelector: '.sp-simpleportfolio-item',
		sequentialFadeDelay: 150,
		sizer: $sizer
	});

	// Filters
	jQuery('.sp-simpleportfolio-filter li a').on('click', function(event){
		event.preventDefault();
		var $self = jQuery(this);
		var $this = jQuery(this).parent();

		if($this.hasClass('active')) {
			return;
		}

		$self.closest('ul').children().removeClass('active');
		$self.parent().addClass('active');

		var $local = $self.closest('.sp-simpleportfolio').children('.sp-simpleportfolio-items');
		
		$local.shuffle( 'shuffle', $this.data('group') );
	});
});
