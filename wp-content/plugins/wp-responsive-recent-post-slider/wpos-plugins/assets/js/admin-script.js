/*jslint browser:true */
(function ($) {
	"use strict";

	var timer;
	var timeOut = 300; /* delay after last keypress to execute filter */

	$( document ).ready(function() {

		/* Stop Submitting Search Form */
		$('.espbw-search-inp-js').submit(function( event ) {
			event.preventDefault();
		});

		$(document).on('keyup paste input', '.espbw-search-inp-js', function(event) {

			clearTimeout(timer); /* if we pressed the key, it will clear the previous timer and wait again */
			var curr_ele	= $(this);
			var cls_ele		= curr_ele.closest('.espbw-dashboard-wrap');
			var search_ele	= cls_ele.find('.espbw-plugin-list');

			cls_ele.find('.espbw-search-no-result').hide();
			cls_ele.find('.espbw-filter-link').removeClass('current');

			timer = setTimeout(function() {

				var search_value	= $.trim( curr_ele.val().toLowerCase() );
				var search_array	= search_value.split(" ");

				if( search_value == '' ) {
					cls_ele.find('.espbw-plugin-all .espbw-filter-link').addClass('current');
				}

				search_ele.find('.espbw-plugin-card-wrap').each(function(index) {

					var contents	= $(this).find('.espbw-plugin-name').text().toLowerCase();
					var tags		= $(this).attr('data-tags').toLowerCase();

					if ( contents.indexOf(search_value) !== -1 || tags.indexOf(search_value) !== -1 ) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});

				if( ! cls_ele.find('.espbw-plugin-card-wrap').is(":visible") ) {
					cls_ele.find('.espbw-search-no-result').show();
				}

			}, timeOut);
		});

		/* Filter Links */
		$(document).on('click', '.espbw-filter-link', function() {

			var curr_ele		= $(this);
			var cls_ele			= curr_ele.closest('.espbw-dashboard-wrap');
			var plugin_list_ele	= cls_ele.find('.espbw-plugin-list');
			var filter			= curr_ele.attr('data-filter');
			filter				= filter ? filter : '';

			cls_ele.find('.espbw-search-inp-js').val('');
			plugin_list_ele.find('.espbw-plugin-card-wrap').hide();
			cls_ele.find('.espbw-filter-link').removeClass('current');
			curr_ele.addClass('current');

			if( filter == '' ) {
				plugin_list_ele.find('.espbw-plugin-card-wrap').show();
			} else {
				plugin_list_ele.find('.espbw-'+filter).show();
			}
		});

	});

})(jQuery);