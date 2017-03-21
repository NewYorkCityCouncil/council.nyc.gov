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
  Link exit notification
--------------------------------------------------*/
jQuery('a:not([href*="council.nyc/"]):not([href*="legistar.council.nyc.gov/"]):not([href*="www.nyc.gov/"]):not([href^="#"]):not([href^="/"])').filter(function() {
  return this.hostname && this.hostname !== location.hostname;
}).click(function(e) {
  if(!confirm("You are leaving the New York City Council's website. When following an external link, you are subject to the privacy, copyright, security, and information quality policies of that website. By providing links to other sites, The New York City Council does not guarantee, approve, or endorse the views they express, or products/services available on these sites.")) {
    // if user clicks 'no' then dont proceed to link.
    e.preventDefault();
  };
});


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
  When tabs load, refresh embedded content
--------------------------------------------------*/
jQuery('.tabs').on('change.zf.tabs', function() {
    jQuery('.tabs-panel').find('iframe, embed').prop('src', function(){
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


/*--------------------------------------------------
  URL query string to open modals
--------------------------------------------------*/
// openModal must be called after ajax sidebar loads
var openModal = function(){
  if ( window.location.href.indexOf("?modal=true") > -1 ) {
    jQuery('#onload-modal').foundation('open');
  }
  if ( window.location.href.indexOf("?contact=subscribe") > -1 ) {
    jQuery('#subscribe_form').foundation('open');
  }
  if ( window.location.href.indexOf("?contact=message") > -1 ) {
    jQuery('#contact_form').foundation('open');
  }
};


/*--------------------------------------------------
  Load Member sidebars on District pages
--------------------------------------------------*/
jQuery( document ).ready(function() {
  var memberSiteURL = jQuery('#district-sidebar').attr('data-membersite');
  if (typeof memberSiteURL !== 'undefined') {
    jQuery('#district-sidebar').load( memberSiteURL + ' #district-widgets', function() {
      jQuery('#district-sidebar .menu-item').removeClass('current-menu-item current-menu-ancestor current-menu-parent');
      jQuery('#district-sidebar').foundation();
      openModal();
    });
  } else {
    openModal();
  }
});
