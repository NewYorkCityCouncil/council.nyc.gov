<?php
// If a Council Member or District page currently being viewed, set a var.
$theme = wp_get_theme();
if ( 'NYCC Member' == $theme->name ) {
  $districtNumber = get_option('council_district_number');
}
if ( is_page_template( 'page-district.php' ) || is_page_template( 'page-speakerdistrict.php' )) {
  global $wp_query;
  $districtNumber = $wp_query->post->menu_order;;
  wp_reset_query();
}
?>

<script src="https://cartodb-libs.global.ssl.fastly.net/cartodb.js/v3/3.15/cartodb.js"></script>
<script>
<?php if ( is_page_template( 'page-listdistricts.php' ) ) { ?>
  jQuery(document).ready(function(){
    var urlParams = new URLSearchParams(window.location.search);
    var address = urlParams.get("address") || ""
    if (address){ ajaxGeoclient( address, true ); }
  })

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
        'relation' => 'OR',
        array(
          'key' => '_wp_page_template',
          'value' => 'page-district.php',
        ),
        array(
          'key' => '_wp_page_template',
          'value' => 'page-speakerdistrict.php',
        ),
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
    var urlParams = new URLSearchParams(window.location.search);
    var address = urlParams.get("address") || ""
    L.DomEvent.disableClickPropagation(div);
    div.innerHTML = `<form aria-hidden="true" aria-describedby="addresslookup-error" id="addresslookup"><input value="${address}" id="mapAddress" aria-label="Enter street address or borough" type="text" placeholder="Street Address, Borough" tabindex="-1"><button type="submit" class="dashicons-before dashicons-search" aria-label="Search" tabindex="-1"><span class="show-for-sr">Search</span></button><div aria-live="assertive" role="alert" id="addresslookup-error" class="addresslookup-error"></div></form>`;
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
    let params;
    try{
      let boro = terms.split(",")[1].replace(/[^\w\s]/gi, '');
      if (boro.toLowerCase().includes("manhattan") || boro.toLowerCase().includes("new york")){
        boro = "Manhattan"
      } else if (terms.toLowerCase().includes("brooklyn")){
        boro = "Brooklyn"
      } else if (terms.toLowerCase().includes("bronx")){
        boro = "Bronx"
      } else if (terms.toLowerCase().includes("queens")){
        boro = "Queens"
      } else if (terms.toLowerCase().includes("staten island")){
        boro = "Staten Island"
      };
      let sanitizedTerms = terms.split(",")[0].replace(/[^\w\s]/gi, '')
      let houseNum = sanitizedTerms.match(/^\d+/g)[0]
      params = {
        "houseNumber": houseNum,
        "street": sanitizedTerms.replace(houseNum, "").trim(),
        "borough": boro,
        "app_key": "M94XSCA5WY7G5GEF9",
        "app_id": "nycc-website",
      };
    } catch(e) {
      badAddress( terms, error );
      return(console.log(e))
    }
    let districtLookup = JSON.parse(jQuery.ajax({
      async: false,
      url: "https://council.nyc.gov/wp-content/themes/wp-nycc/assets/js/district_lookup.json",
      dataType: "json",
    }).responseText);

    $.ajax({
      url: "https://maps.nyc.gov/geoclient/v2/address.json?" + $.param(params) })
    .done((data) => {
      if ( (data.address.geosupportReturnCode === "00" || data.address.geosupportReturnCode === "01") &&
      (data.address.geosupportReturnCode2 === "00" || data.address.geosupportReturnCode2 === "01")) {
        let theLatitude = data.address.latitude,
          theLongitude = data.address.longitude,
          latlngPoint = new L.LatLng( theLatitude, theLongitude ),
          boroughCode = data.address.bblBoroughCode,
          censusTractAndSuffix2022 = data.address.censusTract2020,
          dynamicBlock = data.address.dynamicBlock,
          finalId = `${boroughCode}${censusTractAndSuffix2022}${dynamicBlock}`.replace(/\s/g,"0"),
          CounDist = districtLookup.filter(zone => String(zone.zero_padded_id) === String(finalId))[0].district;

        // use finalId to look up the csv for a district number
        map.setZoom(17, { animate: false })
        map.panTo(latlngPoint, { animate: false })
        var popup = L.popup()
          .setLatLng(latlngPoint)
          .setContent(getPopupInfo(CounDist))
          .openOn(map);
        $('#addresslookup-error').html('');
        <?php if ( is_page_template( 'page-listdistricts.php' ) ) { ?>
          CounDist = parseInt(CounDist, 10);
          var listMember = popupData['Member' + CounDist];
          districtsList.search(listMember);
        <?php } ?>
      } else {
        badAddress( terms, error );
        console.log("Error message 1 from API Client: ", data.address.message)
        console.log("Error message 2 from API Client: ", data.address.message2)
      }
    })
    .fail((e) => { 
      badAddress( terms, error );
      console.log("Error message from AJAX: ", e) 
    });
  }

  function badAddress( terms, error ) {
    if ( error == true ) {
      var errorMessage = '<div class="callout alert text-small text-center"><strong>Please enter a valid street address and borough separated by a comma.</strong></div>';
      // var errorMessage = '<div class="callout alert text-small text-center"><strong>This feature is currently down and is under maintenance. Please try again later. <br/>Alternatively, you may use <a href="https://zola.planning.nyc.gov/about?layer-groups=%5B%22nyc-council-districts%22%2C%22street-centerlines%22%5D" target="_blank">DCP\'s Zoning & Land Use Map</a> to search addresses.</strong></div>';
      jQuery('#addresslookup-error').html(errorMessage);
    }<?php if ( is_page_template( 'page-listdistricts.php' ) ) { ?> else {
      districtsList.search(terms);
      if (districtsList.matchingItems.length == 0) {
        jQuery('#list-search-error').removeClass('hide');
      }
    }<?php } ?>
  }

  /**
   * Districts list.js filter + address search
   */

  const listOptions = {
    valueNames: [ 'sort-district', 'sort-member', 'sort-borough', 'sort-party', 'sort-neighborhoods' ]
  };
  let districtsList = new List('districts-list', listOptions);

  // Handle form submit
  jQuery('#list-search').submit(function(e){
    e.preventDefault();
    let searchTerms = jQuery('#list-search-input').val();
    // First search the list
    districtsList.search(searchTerms);
    // If no results, use the Geoclient
    if (districtsList.matchingItems.length === 0) {
      ajaxGeoclient( searchTerms, false );
      jQuery("#assertive-message").html("No results found for your search.");
    } else {
      let cmList = "";
      let resultNum = "";
      districtsList.matchingItems.length > 1 ? resultNum = " Council Members match your search: " : resultNum = " Council Member matches your search: ";
      for(let i = 0; i < districtsList.matchingItems.length; i++){
        cmList += districtsList.matchingItems[i]._values['sort-member'].split("<strong>")[1].split("</strong>")[0];
        if (i < districtsList.matchingItems.length - 1){
          cmList += ", ";
        };
        if (i == districtsList.matchingItems.length - 2){
          cmList += "and ";
        };
      }
      jQuery("#assertive-message").html(districtsList.matchingItems.length + resultNum + cmList);
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
