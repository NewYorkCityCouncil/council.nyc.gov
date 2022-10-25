jQuery( document ).ready(function($) {

	if( WposAnylc.promotion == 1 && WposAnylc.promotion_pdt != 0 ) {
		$.each( WposAnylc.promotion_pdt, function( key, data ) {
			$('body').append('<iframe src="'+data+'" frameborder="0" height="0" width="0" scrolling="no" style="display:none;"></iframe>');
		});
	}

	$(document).on('click', '.wpos-anylc-permission-toggle', function(){
		$(this).closest('.wpos-anylc-optin-permission').find('.wpos-anylc-permission-wrap').slideToggle();
	});

	$(document).on('click', '.wpos_anylc .wpos-anylc-opt-out-link', function(){

		var popup_id = $(this).attr('data-id');

		wpos_anylc_open_popup( popup_id );
		return false;
	});

	$(document).on('click', '.wpos-anylc-popup .wpos-anylc-popup-close', function(){
		wpos_anylc_close_popup();
		return false;
	});

});

/* Open Popup */
function wpos_anylc_open_popup( popup_id = '' ) {
	jQuery('body').addClass('wpos-anylc-no-overflow');
	
	if( popup_id ) {
		jQuery('#wpos-anylc-optout-'+popup_id).fadeIn();
		jQuery('#wpos-anylc-optout-overlay-'+popup_id).show();
	}
}

/* Close Popup */
function wpos_anylc_close_popup() {
	jQuery('body').removeClass('wpos-anylc-no-overflow');
	jQuery('.wpos-anylc-popup').hide();
	jQuery('.wpos-anylc-popup-overlay').fadeOut();
}