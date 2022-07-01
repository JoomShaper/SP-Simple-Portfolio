/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

jQuery(window).on('load', function() {
	var $container 	= jQuery('.sp-simpleportfolio-items');
	var $sizer = $container.find('.shuffle__sizer');

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
