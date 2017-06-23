<?php
// If a Council Member or District page currently being viewed, set a var.
$theme = wp_get_theme();
if ( 'NYCC Member' == $theme->name ) {
  $districtNumber = get_option('council_district_number');
}
if ( is_page_template( 'page-district.php' ) ) {
  global $wp_query;
  $districtNumber = $wp_query->post->menu_order;;
  wp_reset_query();
}
?>

<script src="https://cartodb-libs.global.ssl.fastly.net/cartodb.js/v3/3.15/cartodb.js"></script>
<script>

  /**
   * Popup Data
   */

  var popupData = new Object;

  <?php

  switch_to_blog(1);

    // Define the popup data
    // Get all the pages that use the District page template...
    $args = array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'orderby'    => 'menu_order',
      'order'      => 'ASC',
      'posts_per_page' => '-1',
      'meta_query' => array(
          array(
              'key' => '_wp_page_template',
              'value' => 'page-district.php',
          )
      )
    );
    $list_districts = new WP_Query( $args );

    // Loop through the District pages
    if ( $list_districts->have_posts() ) {
      while ( $list_districts->have_posts() ) : $list_districts->the_post();

      global $post;

      // Get the District meta
      $current_member_site = get_post_meta($post->ID, 'current_member_site', true);
      $number = $post->menu_order;
      $link = network_site_url() . 'district-' . $number . '/';

      ?>
      popupData.URI<?php echo $number ?> = '<?php echo $link ?>';
      <?php

      if ($current_member_site) {

        // Switch to the current Member's site
        switch_to_blog($current_member_site);

        // Set properties for popupData Object
        ?>
        popupData.Thumb<?php echo $number ?> = '<?php echo get_blog_option($current_member_site,'council_member_thumbnail' ) ?>';
        popupData.Member<?php echo $number ?> = '<?php echo get_blog_option($current_member_site,'council_member_name' ) ?>';
        <?php

        restore_current_blog();
        wp_reset_postdata();

      } else {

        // Fallback properties for vacant Districs
        ?>
        popupData.Thumb<?php echo $number ?> = '<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-blue.png';
        popupData.Member<?php echo $number ?> = 'Vacant';
        <?php
      }

      endwhile;
      wp_reset_postdata();

    }

  restore_current_blog();
  wp_reset_postdata();

  ?>

  // Get the right popup info from the popupData object
  function getPopupInfo(n) {

    var CounDist = n,
        CounDist = parseInt(CounDist, 10);
        popupThumbnail = popupData['Thumb' + CounDist],
        popupMember = popupData['Member' + CounDist],
        popupLink = popupData['URI' + CounDist];

    var popupInfo = '' +
    '<div class="media-object">' +
      '<div class="media-object-section">' +
        '<div class="thumbnail">' +
          '<a href="' + popupLink + '"<?php if ( is_page_template( 'page-widget-map.php' ) ){ ?> target="_blank"<?php } ?>><img src="' + popupThumbnail + '"></a>' +
        '</div>' +
      '</div>' +
      '<div class="media-object-section">' +
        '<h4><a href="' + popupLink + '"<?php if ( is_page_template( 'page-widget-map.php' ) ){ ?> target="_blank"<?php } ?>><strong>District ' + CounDist + '</strong></a></h4>' +
        '<p><strong>' + popupMember + '</strong></p>' +
      '</div>' +
    '</div>'

    return popupInfo;

  }


  /**
   * Carto Map
   */

  // Add the map
  var map = L.map('map', {
    scrollWheelZoom: false,
    minZoom: 9,
    maxZoom: 17,
    layers: [
        L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {
          minZoom: 13,
          maxZoom: 17
        }),
        L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_nolabels/{z}/{x}/{y}.png', {
          maxZoom: 12,
          minZoom: 9
        })
    ]
  }).setView([40.727760, -73.987218], <?php if ( is_page_template( 'page-widget-map.php' ) ){ ?>10<?php } else {?>11<?php } ?>);

  // Add the Districts layer
  var layerSource = {
    user_name: 'nyc-council',
    type: 'cartodb',
    sublayers: [
      {
        sql: "SELECT * FROM nyc_city_council_dist_cm",
        cartocss: "#nyc_city_council_dist_cm { polygon-fill: #2f56a6; polygon-opacity: 0; line-width: 1; line-color: #2f56a6; line-opacity: 0.5; polygon-comp-op: darken; } #layer::labels {text-name: [coundist]; text-face-name: 'Open Sans Bold'; text-size: 12; text-fill: #23417d; text-label-position-tolerance: 0; text-halo-radius: 2; text-halo-fill: #F9F9F9; text-dy: 0; text-allow-overlap: false; text-placement: point; text-placement-type: dummy; }",
      }
    ],
    cartodb_logo: false,
  }
  cartodb.createLayer(map, layerSource, {
    https: true
  })
  .addTo(map)
  .done(function(layer) {
      layer.setInteraction(false);
      layer.on('error', function(err) {
          console.log('error:' + err);
      });
  });

  // Set the popup var
  var popup = new L.Popup();

  <?php if ( isset($districtNumber) ) { ?>
  // We're on a District/Member page
  var sql = new cartodb.SQL({ user: 'nyc-council', format: 'geojson' });

  // Add a layer for the current District
  var currentDistrictLayer = L.geoJson().addTo(map);

  sql.execute("SELECT * FROM nyc_city_council_dist_cm WHERE dist=<?php echo $districtNumber ?>")
      .done(function(geojson) {

        currentDistrictLayer.addData(geojson);

        // style the District layer
        currentDistrictLayer.setStyle({
          color: '#23417d',
          weight: 1,
          opacity: 1,
          fillColor: '#2f56a6',
          fillOpacity: 0.15,
        });

        // zoom to the District
        map.fitBounds(currentDistrictLayer.getBounds(), {
          animate: false
        })

        currentDistrictLayer.bindPopup(getPopupInfo(<?php echo $districtNumber ?>));

      });

  <?php } ?>

  /**
   * Get popups info via Carto SQL for map clicks
   */

  map.on('click', function(e) {

    jQuery('#mapAddress').val('');

    var sql = new cartodb.SQL({ user: 'nyc-council' });

    sql.execute('SELECT * FROM nyc_city_council_dist_cm WHERE ST_Intersects(the_geom,CDB_' + e.latlng + ')')
        .done(function(data) {
          if ( data.rows.length != 0 ) {
            var CounDist = data.rows[0]['dist'];
            popup.setLatLng(e.latlng);
            popup.setContent(getPopupInfo(CounDist));
            map.openPopup(popup);
          }
        })
        .error(function(errors) {
          console.log("errors:" + errors);
        });

  });


  /**
   * Address Lookup
   * Use the NYC Geoclient API to get Council District info
   */

  // Add the Leaflet control
  var addresslookup = L.control({position: 'topright'});
  addresslookup.onAdd = function (map) {
    var div = L.DomUtil.create('div', 'addresslookup');
    L.DomEvent.disableClickPropagation(div);
    div.innerHTML = '<form id="addresslookup"><input id="mapAddress" type="text" placeholder="Street Address, Borough"><button type="submit" class="dashicons-before dashicons-search"><span class="show-for-sr">Search</span></button></form><div id="addresslookup-error" class="addresslookup-error"></div>';
    return div;
  };
  addresslookup.addTo(map);

  // Prevent the control from panning or zooming the map
  addresslookup.getContainer().addEventListener('mouseover', function () {
    map.dragging.disable();
    map.doubleClickZoom.disable();
  });
  addresslookup.getContainer().addEventListener('mouseout', function () {
    map.dragging.enable();
    map.doubleClickZoom.enable();
  });

  // When user submits the form...
  document.getElementById('addresslookup').addEventListener('submit', function(e){
    e.preventDefault();
    var mapAddress = jQuery('#mapAddress').val();
    ajaxGeoclient( mapAddress, true );
  }, false);

  // Talk to the Geoclient
  function ajaxGeoclient( terms, error ) {
    var apiKey = 'db87f7a57ab963b71d36c179ce32157c';
    var apiId = 'f208e50a';
    var apiQuery = 'https://api.cityofnewyork.us/geoclient/v1/search.json?input=' + terms + '&app_id=' + apiId + '&app_key=' + apiKey;

    jQuery.ajax({
      url: apiQuery,
      dataType: 'jsonp',
      success: function (data) {
        if ( data.status == 'OK' ) {
          for (var key in data.results) {
            if (data.results.hasOwnProperty(key)) {
              var theLatitude = data.results[key].response.latitude,
                  theLongitude = data.results[key].response.longitude,
                  latlngPoint = new L.LatLng( theLatitude, theLongitude ),
                  CounDist = data.results[key].response.cityCouncilDistrict;

              map.setZoom(17, { animate: false })

              map.panTo(latlngPoint, { animate: false })

              var popup = L.popup()
                  .setLatLng(latlngPoint)
                  .setContent(getPopupInfo(CounDist))
                  .openOn(map);

              jQuery('#addresslookup-error').html('');

<?php if ( is_page_template( 'page-listdistricts.php' ) ) { ?>
              CounDist = parseInt(CounDist, 10);
              var listMember = popupData['Member' + CounDist];
              // var listMember = CounDist;
              districtsList.search(listMember);
<?php } ?>
            }
          }
        } else {
          badAddress( terms, error );
        }
      },
      error: function(){
        badAddress( terms, error );
      }
    });
  }

  function badAddress( terms, error ) {
    if ( error == true ) {
      var errorMessage = '<div class="callout alert text-small text-center"><strong>Please enter a valid street address and borough</strong></div>';
      jQuery('#addresslookup-error').html(errorMessage);
    }<?php if ( is_page_template( 'page-listdistricts.php' ) ) { ?> else {
      districtsList.search(terms);
      if (districtsList.matchingItems.length == 0) {
        jQuery('#list-search-error').removeClass('hide');
      }
    }<?php } ?>
  }

<?php if ( is_page_template( 'page-listdistricts.php' ) ) { ?>
  /**
   * Districts list.js filter + address search
   */

  var listOptions = {
    valueNames: [ 'sort-district', 'sort-member', 'sort-borough', 'sort-party', 'sort-neighborhoods' ]
  };
  var districtsList = new List('districts-list', listOptions);

  // Handle form submit
  jQuery('#list-search').submit(function(e){
    e.preventDefault();
    var searchTerms = jQuery('#list-search-input').val();
    // First search the list
    districtsList.search(searchTerms);
    // If no results, use the Geoclient
    if (districtsList.matchingItems.length == 0) {
      ajaxGeoclient( searchTerms, false );
    }
  });

  // Clear search & close popup while typing
  jQuery('#list-search-input, #mapAddress').on('input', function() {
    districtsList.search();
    jQuery('#list-search-error').addClass('hide');
    map.closePopup();
    jQuery('#list-search-input, #mapAddress').not(this).val('');
  });

<?php } ?>
</script>
