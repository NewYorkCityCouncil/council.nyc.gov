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

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/nyccouncil-districts.min.js"></script>
<script src="http://libs.cartocdn.com/cartodb.js/v3/3.15/cartodb.js"></script>
<script>

function main() {

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

    // Add the Carto map ?>
    cartodb.createVis('map', 'http://nyc-council.carto.com/api/v2/viz/3162b988-05b4-11e7-aea7-0ee66e2c9693/viz.json', {
        shareable: false,
        // search: true,
        zoom: 11,
        cartodb_logo: false,
        loaderControl: false,
    })
    .done(function(vis, layers) {

        vis.map.set({
          minZoom: 9,
          maxZoom: 17,
        });

        map = vis.getNativeMap();

        map.setMaxBounds([[40.25,-75.25],[41.17,-72.75]]);

        // TODO: Default Carto zoom control isn't loading with createVis() - error: "zoom template is empty"
        L.control.zoom({ position: 'topleft' }).addTo(map);

        // Add the Districts GeoJSON as map features
        var districtsLayer = L.geoJson(districtsData,  {
            style: defaultStyle,
            onEachFeature: onEachFeature
        }).addTo(map);

        // Style the features
        function defaultStyle(feature) {
            return {
                weight: 2,
                opacity: 0,
                color: '#174299',
                fillOpacity: getOpacity(feature.properties.CounDist)
            };
        }

        // Set each District's fill opacity
        function getOpacity(n) {
          <?php if ( isset($districtNumber) ) { ?>
            if ( n == <?php echo $districtNumber ?> ) {
                return 0.25;
            } else {
                return 0;
            }
          <?php } else { ?>
            return 0;
          <?php } ?>
        }

        var popup = new L.Popup();

        // For each feature, set mouse events and bind the popup info
        function onEachFeature(feature, layer) {

            var CounDist = layer.feature.properties.CounDist,
                popupThumbnail = popupData["Thumb" + CounDist],
                popupMember = popupData["Member" + CounDist],
                popupLink = popupData["URI" + CounDist];

            layer.on({
                mousemove: mousemove,
                mouseout: mouseout
            }).bindPopup(
              '<div class="media-object">' +
                '<div class="media-object-section">' +
                  '<div class="thumbnail">' +
                    '<a href="' + popupLink + '"><img src="' + popupThumbnail + '"></a>' +
                  '</div>' +
                '</div>' +
                '<div class="media-object-section">' +
                  '<h4><a href="' + popupLink + '"><strong>District ' + layer.feature.properties.CounDist + '</strong></a></h4>' +
                  '<p><strong>' + popupMember + '</strong></p>' +
                '</div>' +
              '</div>'
            );

            <?php
            // If we're on a District/Member page, zoom the map to that District
            if ( isset($districtNumber) ) {
            ?>
            if ( <?php echo $districtNumber ?> == layer.feature.properties.CounDist ){
              map.fitBounds(layer.getBounds(),{animate: false});
            }
            <?php
            }
            ?>

        }

        function mousemove(e) {
            var layer = e.target;

            layer.setStyle({
                opacity: 1,
            });
        }

        function mouseout(e) {
            var layer = e.target;

            layer.setStyle({
                opacity: 0,
            });
        }

        // Add a Leaflet control for the address lookup
        var addresslookup = L.control({position: 'topright'});
        addresslookup.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'addresslookup');
            div.innerHTML = '<form id="addresslookup"><input id="mapAddress" type="text" placeholder="Street Address, Borough"><button type="submit" class="dashicons-before dashicons-search"><span class="show-for-sr">Search</span></button></form><div id="addresslookup-error" class="addresslookup-error"></div>';
            return div;
        };
        addresslookup.addTo(map);
        // ...prevent it from panning or zooming the map
        addresslookup.getContainer().addEventListener('mouseover', function () {
            map.dragging.disable();
            map.doubleClickZoom.disable();
        });
        addresslookup.getContainer().addEventListener('mouseout', function () {
            map.dragging.enable();
            map.doubleClickZoom.enable();
        });

        // When user submits address lookup form..
        document.getElementById('addresslookup').addEventListener('submit', function(e){
          e.preventDefault();

          // get values in the form
          var mapAddress = jQuery('#mapAddress').val();

          // set the form error message
          var badaddress = '<div class="callout alert text-small text-center"><strong>Please enter a valid street address and borough</strong></div>';

          // talk to the NYC Geoclient API
          var apiKey = 'db87f7a57ab963b71d36c179ce32157c';
          var apiId = 'f208e50a';
          var apiQuery = 'https://api.cityofnewyork.us/geoclient/v1/search.json?input=' + mapAddress + '&app_id=' + apiId + '&app_key=' + apiKey;

          jQuery.ajax({
              url: apiQuery,
              dataType: 'jsonp',
              success: function (data) {

                if ( data.status == 'OK' ) {
                    for (var key in data.results) {
                       if (data.results.hasOwnProperty(key)) {

                            var theLatitude = data.results[key].response.latitude;
                            var theLongitude = data.results[key].response.longitude;
                            var latlngPoint = new L.LatLng( theLatitude, theLongitude );

                            var CounDist = data.results[key].response.cityCouncilDistrict,
                                CounDist = parseInt(CounDist, 10);
                                popupThumbnail = popupData['Thumb' + CounDist],
                                popupMember = popupData['Member' + CounDist],
                                popupLink = popupData['URI' + CounDist];

                            var popup = L.popup()
                                .setLatLng(latlngPoint)
                                .setContent(
                                  '<div class="media-object">' +
                                    '<div class="media-object-section">' +
                                      '<div class="thumbnail">' +
                                        '<a href="' + popupLink + '"><img src="' + popupThumbnail + '"></a>' +
                                      '</div>' +
                                    '</div>' +
                                    '<div class="media-object-section">' +
                                      '<h4><a href="' + popupLink + '"><strong>District ' + CounDist + '</strong></a></h4>' +
                                      '<p><strong>' + popupMember + '</strong></p>' +
                                    '</div>' +
                                  '</div>'
                                )
                                .openOn(map);

                            map.setView(latlngPoint, 15, {})

                            jQuery('#addresslookup-error').html('');

                         }
                    }
                } else {
                  jQuery('#addresslookup-error').html(badaddress);
                }

              },
              error: function(){
                jQuery('#addresslookup-error').html(badaddress);
              }
          });

        }, false);

    })
    .error(function(err) {
       console.log(err);
    });

}
window.onload = main;

</script>
