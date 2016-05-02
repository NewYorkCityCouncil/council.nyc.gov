<?php ?>

<script type="text/javascript">

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

    <?php
    $sites = wp_get_sites();
    foreach ($sites as $site) {
        $ID = $site['blog_id'];
        $geometry = get_blog_option($ID,council_district_geometry);
        if ( $geometry ) { ?>
            var district<?php echo $ID ?> = L.polygon(  [
                <?php echo $geometry; ?>
            ], {
                weight: 1,
                opacity: 1,
                color: '#2f56a6',
                <?php if ( $ID == get_current_blog_id() ) {
                  ?>fillOpacity: 0.25<?php
                } else {
                  ?>fillOpacity: 0<?php
                }?>
            }).addTo(map).bindPopup(
                '<div class="media-object">' +
                  '<div class="media-object-section">' +
                    '<div class="thumbnail">' +
                      '<a href="<?php echo get_site_url($ID); ?>"><img src= "<?php echo get_blog_option($ID,council_member_thumbnail) ?>"></a>' +
                    '</div>' +
                  '</div>' +
                  '<div class="media-object-section">' +
                    '<h4><a href="<?php echo get_site_url($ID); ?>"><strong>District <?php echo get_blog_option($ID,council_district_number) ?></strong></a></h4>' +
                    '<p><strong><?php echo get_blog_option($ID,council_member_name) ?></strong></p>' +
                  '</div>' +
                '</div>'
            ).on({
                mousemove: mousemove,
                mouseout: mouseout
            });
            <?php if ( $ID == get_current_blog_id() ) { ?>
                map.fitBounds(district<?php echo $ID ?>.getBounds(),{animate: false});
            <?php }
        }
    }
    ?>

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
