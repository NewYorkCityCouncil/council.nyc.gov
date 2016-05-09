<?php

$districtNumber = get_option('council_district_number');

?>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/nyccouncil-districts.js"></script>
<script type="text/javascript">

    var popupData = new Object;

    <?php
    $sites = wp_get_sites();
    foreach ($sites as $site) {
      $ID = $site['blog_id'];
      $number = get_blog_option($ID,council_district_number);
      $thumbnail = get_blog_option($ID,council_member_thumbnail);
      $member = get_blog_option($ID,council_member_name);
      $link = get_site_url($ID);
      if ( $number ) { ?>
        popupData.District<?php echo $number ?> = '<?php echo $thumbnail ?>';
        popupData.Member<?php echo $number ?> = '<?php echo $member ?>';
        popupData.URI<?php echo $number ?> = '<?php echo $link ?>';
      <?php }
    }
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
            fillOpacity: getColor(feature.properties.number)
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
            popupThumbnail = popupData["District" + CounDist],
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
        if ( <?php echo $districtNumber ?> == layer.feature.properties.number ){
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
