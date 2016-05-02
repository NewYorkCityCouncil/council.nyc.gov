jQuery(document).foundation();

/*--------------------------------------------------
  Map Toggler
--------------------------------------------------*/
jQuery('#map-toggler').click(function() {
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
  List.js
--------------------------------------------------*/
var options = {
  valueNames: [ 'sort-district', 'sort-member', 'sort-borough', 'sort-party', 'sort-neighborhoods' ]
};
var userList = new List('districts-list', options);


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
