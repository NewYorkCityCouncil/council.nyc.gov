jQuery(document).foundation();

/*--------------------------------------------------
  Map Toggler
--------------------------------------------------*/
jQuery('.map-toggler').click(function() {
  jQuery('#map-container').toggleClass('short');
  jQuery('.site-header').removeClass('sticky');
  map.invalidateSize(true);
});


/*--------------------------------------------------
  Responsive Embeds
--------------------------------------------------*/
jQuery('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]').each(function() {
  if ( jQuery(this).innerWidth() / jQuery(this).innerHeight() > 1.5 ) {
    jQuery(this).wrap("<div class='widescreen flex-video'/>");
  } else {
    jQuery(this).wrap("<div class='flex-video'/>");
  }
});


/*--------------------------------------------------
  URL query string to open modals
--------------------------------------------------*/
if ( window.location.href.indexOf("?modal=true") > -1 ) {
  jQuery('#onload-modal').foundation('open');
}

/*--------------------------------------------------
  URL hash to open tab
  TODO: look for this feature in future Foundation version
--------------------------------------------------*/
function tabDeepLink(selector) {
  jQuery(selector).each(function() {
    var $tabs = jQuery(this);

    // match page load anchor
    var anchor = window.location.hash;
    if (anchor.length && $tabs.find('[href="'+anchor+'"]').length) {
      $tabs.foundation('selectTab', jQuery(anchor));
      // don't scroll to anchor
      jQuery(window).load(function() {
        jQuery('html, body').animate({ scrollTop: 0 }, 1);
      });
    }

    // append the hash on click
    $tabs.on('change.zf.tabs', function() {
      var anchor = $tabs.find('.tabs-title.is-active a').attr('href');
      history.pushState({}, '', anchor);
    });
  });
}
tabDeepLink('.tabs');


/*--------------------------------------------------
  When tabs load, refresh their iframes
--------------------------------------------------*/
jQuery('.tabs').on('change.zf.tabs', function() {
    jQuery('.tabs-panel.is-active').find('iframe').prop('src', function(){
      return jQuery(this).attr('src');
    });
});


/*--------------------------------------------------
  List.js
--------------------------------------------------*/
if ( jQuery('#districts-list').length ) {
  var options = {
    valueNames: [ 'sort-district', 'sort-member', 'sort-borough', 'sort-party', 'sort-neighborhoods' ]
  };
  var userList = new List('districts-list', options);
}


/*--------------------------------------------------
  Filter Page Content
--------------------------------------------------*/
jQuery('#filter-nav a').click(function(e){
  e.preventDefault();
  var filter = jQuery(this).attr('id');
  if( filter == "show-all") {
    jQuery('.filter-item').show();
  }
  else {
    jQuery('.filter-item').show();
    jQuery('.filter-item:not(.' + filter + ')').hide();
  }
  jQuery('#filter-nav li').removeClass('active');
  jQuery(this).parent('li').addClass('active');
});


/*--------------------------------------------------
  Rotate the Google Translate language label
--------------------------------------------------*/
jQuery( document ).ready(function() {
  var langButton = jQuery('#translation-menu .button');
  jQuery('<span class="lang-label" id="lang-label-1">English</span>').appendTo(langButton); // English
  jQuery('<span class="lang-label" id="lang-label-2">Español</span>').appendTo(langButton).hide(); // Spanish
  jQuery('<span class="lang-label" id="lang-label-3">\u09AC\u09BE\u0982\u09B2\u09BE</span>').appendTo(langButton).hide(); // Bangla
  jQuery('<span class="lang-label" id="lang-label-4">\u0440\u0443\u0441\u0441\u043A\u0438\u0439</span>').appendTo(langButton).hide(); // Russian
  jQuery('<span class="lang-label" id="lang-label-5">\u4E2D\u6587</span>').appendTo(langButton).hide(); // Chinese

  var swapTime = 1500;
  function rotateLangLabel(){
      jQuery('#lang-label-1').delay(swapTime).fadeOut('fast', function(){
          jQuery('#lang-label-2').fadeIn('slow', function(){
              jQuery(this).delay(swapTime).fadeOut('fast', function(){
                  jQuery('#lang-label-3').fadeIn('slow', function(){
                      jQuery(this).delay(swapTime).fadeOut('fast', function(){
                          jQuery('#lang-label-4').fadeIn('slow', function(){
                              jQuery(this).delay(swapTime).fadeOut('fast', function(){
                                  jQuery('#lang-label-5').fadeIn('slow', function(){
                                      jQuery(this).delay(swapTime).fadeOut('fast', function(){
                                          jQuery('#lang-label-1').fadeIn('slow', rotateLangLabel);
                                      });
                                  });
                              });
                          });
                      });
                  });
              });
          });
      });
  }
  setTimeout(function(){ rotateLangLabel() }, 2000);
});
