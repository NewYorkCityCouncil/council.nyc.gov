/* Define global Variable */
var wprpsp_next_arrow = '<span class="slick-next slick-arrow" data-role="none" tabindex="0" role="button"><svg fill="currentColor" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg"><title/><path d="M69.8437,43.3876,33.8422,13.3863a6.0035,6.0035,0,0,0-7.6878,9.223l30.47,25.39-30.47,25.39a6.0035,6.0035,0,0,0,7.6878,9.2231L69.8437,52.6106a6.0091,6.0091,0,0,0,0-9.223Z"/></svg></span>';
var wprpsp_prev_arrow = '<span class="slick-prev slick-arrow" data-role="none" tabindex="0" role="button"><svg fill="currentColor" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg"><title/><path d="M39.3756,48.0022l30.47-25.39a6.0035,6.0035,0,0,0-7.6878-9.223L26.1563,43.3906a6.0092,6.0092,0,0,0,0,9.2231L62.1578,82.615a6.0035,6.0035,0,0,0,7.6878-9.2231Z"/></svg></span>';

( function( $ ) {

	"use strict";

	/* Post Slider Initialize */
	wppsac_post_slider_init();

	/* Post Carousel Slider Initialize */
	wppsac_post_carousel_slider_init();

	/* Elementor Compatibility */
	/***** Elementor Compatibility Start *****/
	if( Wppsac.elementor_preview == 0 ) {

		$(window).on('elementor/frontend/init', function() {

			/* Tweak for Slick Slider */
			$('.wppsac-post-slider-init').each(function( index ) {

				/* Tweak for Vertical Tab */
				$(this).closest('.elementor-tabs-content-wrapper').addClass('wppsac-elementor-tab-wrap');

				var slider_id = $(this).attr('id');
				$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

				setTimeout(function() {
					if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
						$('#'+slider_id).slick( 'setPosition' );
						$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
					}
				}, 350);
			});
		});
	}

	$(document).on('click', '.elementor-tab-title', function() {

		var ele_control	= $(this).attr('aria-controls');
		var slider_wrap	= $('#'+ele_control).find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {
			var slider_id = $(this).attr('id');
			$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

			setTimeout(function() {
				if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
					$('#'+slider_id).slick( 'setPosition' );
					$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
				}
			}, 350);
		});
	});

	/* SiteOrigin Compatibility For Accordion Panel */
	$(document).on('click', '.sow-accordion-panel', function() {

		var ele_control	= $(this).attr('data-anchor');
		var slider_wrap	= $('#accordion-content-'+ele_control).find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {
			var slider_id = $(this).attr('id');

			if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
				$('#'+slider_id).slick( 'setPosition' );
			}
		});
	});

	/* SiteOrigin Compatibility for Tab Panel */
	$(document).on('click focus', '.sow-tabs-tab', function() {
		var sel_index	= $(this).index();
		var cls_ele		= $(this).closest('.sow-tabs');
		var tab_cnt		= cls_ele.find('.sow-tabs-panel').eq( sel_index );
		var slider_wrap	= tab_cnt.find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {
			var slider_id = $(this).attr('id');
			$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

			setTimeout(function() {
				if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
					$('#'+slider_id).slick( 'setPosition' );
					$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
				}
			}, 300);
		});
	});

	/* Beaver Builder Compatibility for Accordion and Tabs */
	$(document).on('click', '.fl-accordion-button, .fl-tabs-label', function() {

		var ele_control	= $(this).attr('aria-controls');
		var slider_wrap	= $('#'+ele_control).find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {
			var slider_id = $(this).attr('id');
			$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

			setTimeout(function() {
				if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
					$('#'+slider_id).slick( 'setPosition' );
					$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
				}
			}, 300);
		});
	});

	/* Divi Builder Compatibility for Accordion & Toggle */
	$(document).on('click', '.et_pb_toggle', function() {

		var acc_cont	= $(this).find('.et_pb_toggle_content');
		var slider_wrap	= acc_cont.find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {

			var slider_id = $(this).attr('id');

			if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
				$('#'+slider_id).slick( 'setPosition' );
			}
		});
	});

	/* Divi Builder Compatibility for Tabs */
	$('.et_pb_tabs_controls li a').on('click', function() {
		var cls_ele		= $(this).closest('.et_pb_tabs');
		var tab_cls		= $(this).closest('li').attr('class');
		var tab_cont	= cls_ele.find('.et_pb_all_tabs .'+tab_cls);
		var slider_wrap	= tab_cont.find('.wppsac-post-slider-init');

		setTimeout(function() {

			/* Tweak for slick slider */
			$( slider_wrap ).each(function( index ) {
				var slider_id = $(this).attr('id');
				$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

				if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
					$('#'+slider_id).slick( 'setPosition' );
					$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
				}
			});
		}, 550);
	});

	/* Fusion Builder Compatibility for Tabs */
	$(document).on('click', '.fusion-tabs li .tab-link', function() {
		var cls_ele		= $(this).closest('.fusion-tabs');
		var tab_id		= $(this).attr('href');
		var tab_cont	= cls_ele.find(tab_id);
		var slider_wrap	= tab_cont.find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {
			var slider_id = $(this).attr('id');
			$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

			setTimeout(function() {
				if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
					$('#'+slider_id).slick( 'setPosition' );
					$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
					$('#'+slider_id).slick( 'setPosition' );
				}
			}, 200);
		});
	});

	/* Fusion Builder Compatibility for Toggles */
	$(document).on('click', '.fusion-accordian .panel-heading a', function() {
		var cls_ele		= $(this).closest('.fusion-accordian');
		var tab_id		= $(this).attr('href');
		var tab_cont	= cls_ele.find(tab_id);
		var slider_wrap	= tab_cont.find('.wppsac-post-slider-init');

		/* Tweak for slick slider */
		$( slider_wrap ).each(function( index ) {
			var slider_id = $(this).attr('id');
			$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

			setTimeout(function() {
				if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
					$('#'+slider_id).slick( 'setPosition' );
					$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
					$('#'+slider_id).slick( 'setPosition' );
				}
			}, 200);
		});
	});

})( jQuery );

/* Function to initialize post slider */
function wppsac_post_slider_init() {

	/* For Slider */
	jQuery( '.wppsac-post-slider' ).each(function( index ) {

		if( jQuery(this).hasClass('slick-initialized') ) {
			return;
		}

		/* flex Condition */
		if(Wppsac.is_avada == 1) {
			jQuery(this).closest('.fusion-flex-container').addClass('wppsac-fusion-flex');
		}

		var slider_id   	= jQuery(this).attr('id');
		var slider_conf		= JSON.parse( jQuery(this).closest('.wppsac-slick-slider-wrp').attr('data-conf'));

		if( typeof(slider_id) != 'undefined' && slider_id != '' ) {
			jQuery('#'+slider_id).slick({
				slidesToShow	: 1,
				slidesToScroll	: 1,
				infinite		: true,
				lazyLoad        : slider_conf.lazyload,
				speed			: parseInt( slider_conf.speed ),
				autoplaySpeed	: parseInt( slider_conf.autoplay_interval ),
				dots			: ( slider_conf.dots == "true" )		? true : false,
				arrows			: ( slider_conf.arrows == "true" )		? true : false,
				autoplay		: ( slider_conf.autoplay == "true" )	? true : false,
				rtl             : ( slider_conf.rtl == "true" )			? true : false,
				nextArrow		: wprpsp_next_arrow,
				prevArrow		: wprpsp_prev_arrow,
			});
		}
	});
}

/* Function to initialize post slider */
function wppsac_post_carousel_slider_init() {

	/* For Carousel */
	jQuery( '.wppsac-post-carousel' ).each(function( index ) {

		if( jQuery(this).hasClass('slick-initialized') ) {
			return;
		}

		/* flex Condition */
		if(Wppsac.is_avada == 1) {
			jQuery(this).closest('.fusion-flex-container').addClass('wppsac-fusion-flex');
		}

		var carousel_id   	= jQuery(this).attr('id');
		var carousel_conf	= JSON.parse( jQuery(this).closest('.wppsac-slick-carousel-wrp').attr('data-conf'));

		if( typeof(carousel_id) != 'undefined' && carousel_id != '' ) {
			jQuery('#'+carousel_id).slick({
				infinite		: true,
				lazyLoad		: carousel_conf.lazyload,
				speed			: parseInt( carousel_conf.speed ),
				autoplaySpeed	: parseInt( carousel_conf.autoplay_interval ),
				slidesToShow	: parseInt( carousel_conf.slides_to_show ),
				slidesToScroll	: parseInt( carousel_conf.slides_to_scroll ),
				dots			: ( carousel_conf.dots == "true" )		? true : false,
				arrows			: ( carousel_conf.arrows == "true" )	? true : false,
				autoplay		: ( carousel_conf.autoplay == "true" )	? true : false,
				rtl				: ( carousel_conf.rtl == "true" )		? true : false,
				nextArrow		: wprpsp_next_arrow,
				prevArrow		: wprpsp_prev_arrow,
				responsive 		: [{
									breakpoint	: 1023,
									settings	: {
													slidesToShow	: ( parseInt( carousel_conf.slides_to_show ) > 3 ) ? 3 : parseInt( carousel_conf.slides_to_show ),
													slidesToScroll	: 1,
												}
								},{
									breakpoint	: 767,
									settings	: {
													slidesToShow	: ( parseInt( carousel_conf.slides_to_show ) > 2 ) ? 2 : parseInt( carousel_conf.slides_to_show ),
													slidesToScroll	: 1,
												}
								},{
									breakpoint	: 639,
									settings	: {
													slidesToShow	: 1,
													slidesToScroll	: 1,
												}
								},{
									breakpoint	: 479,
									settings	: {
													slidesToShow	: 1,
													slidesToScroll	: 1,
												}
								},{
									breakpoint	: 319,
									settings	: {
													slidesToShow	: 1,
													slidesToScroll	: 1,
												}
								}]
			}); 
		}
	});
}