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
