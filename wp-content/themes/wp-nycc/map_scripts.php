<?php

// If a Council Member or District page currently being viewed, set a var.
$theme = wp_get_theme();
if ( 'NYCC Member' == $theme->name ) {
  $districtNumber = get_option('council_district_number');
}
if ( is_page_template( 'page-district.php' ) ) {
  $thispost = get_post($id);
  $districtNumber = $thispost->menu_order;
}

?>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/nyccouncil-districts.js"></script>
<script type="text/javascript">

    var popupData = new Object;

    <?php

    switch_to_blog(1);

      // Define the popup data for all the pages that use the District template
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

          ?>
          popupData.Thumb<?php echo $number ?> = '<?php echo get_blog_option($current_member_site,'council_member_thumbnail' ) ?>';
          popupData.Member<?php echo $number ?> = '<?php echo get_blog_option($current_member_site,'council_member_name' ) ?>';
          <?php

          restore_current_blog();
          wp_reset_postdata();
        } else {
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

    var southWest = L.latLng(40.25, -75.25),
        northEast = L.latLng(41.17, -72.75),
        bounds = L.latLngBounds(southWest, northEast);

    L.mapbox.accessToken = 'pk.eyJ1IjoibnljY291bmNpbGFuZHkiLCJhIjoiZWRiNzk5NTY3OGVkOTQxZTMxNDJmN2NhZTJmZjExMWIifQ.eKEKYrL_pyNJ3cP0XthU9Q';

    var map = L.mapbox.map('map', 'nyccouncilandy.3a808ec1', {
        scrollWheelZoom: false,
        maxBounds: bounds,
        maxZoom: 17,
        minZoom: 8
    }).setView([40.74, -73.89], 11).addControl(L.mapbox.geocoderControl('mapbox.places', {
        autocomplete: true
    }));

    var districtsLayer = L.geoJson(districtsData,  {
        style: defaultStyle,
        onEachFeature: onEachFeature
    }).addTo(map);

    function defaultStyle(feature) {
        return {
            weight: 1,
            opacity: 1,
            color: '#2f56a6',
            fillOpacity: getColor(feature.properties.CounDist)
        };
    }

    function getColor(n) {
      <?php if ( $districtNumber ) { ?>
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
        <?php if ( $districtNumber ) { ?>
        if ( <?php echo $districtNumber ?> == layer.feature.properties.CounDist ){
          map.fitBounds(layer.getBounds(),{animate: false});
        }
        <?php } ?>
    }

    function mousemove(e) {
        var layer = e.target;

        layer.setStyle({
            weight: 3,
            color: '#174299'
        });
    }

      function mouseout(e) {
          var layer = e.target;

          layer.setStyle({
              weight: 1,
              color: '#2f56a6'
          });
      }

</script>
