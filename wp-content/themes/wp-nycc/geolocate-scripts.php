<?php

$current_pb_cycle = get_post_custom_values( 'current_pb_cycle' )[0];

?>

<script type="text/javascript">
    let districtLookup = JSON.parse(jQuery.ajax({
        async: false,
        url: "https://council.nyc.gov/wp-content/themes/wp-nycc/assets/js/district_lookup.json",
        dataType: "json",
      }).responseText);
  /*--------------------------------------------------
      Geolocate District
    --------------------------------------------------*/
    jQuery('#geolocate-district').submit(function(e) {
        e.preventDefault();
        // get values from the form
        let myAddress = jQuery('#myAddress').val().replace(/[^\w\s]/gi, '');
        let houseNumber = myAddress.match(/^\d+/g)[0];
        let houseStreet = myAddress.replace(`${houseNumber} `,"");
        let myBorough = jQuery('#myBorough').val();

        let params = {
          "houseNumber": houseNumber,
          "street": houseStreet,
          "borough": myBorough,
          "app_key": "M94XSCA5WY7G5GEF9",
          "app_id": "nycc-website",
        };

        const errMessage = '<div class="callout alert text-small text-center">Please enter a valid street address and borough.</div>'

        jQuery.ajax({
          url: `https://maps.nyc.gov/geoclient/v2/address.json?${jQuery.param(params)}`
        })
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

            jQuery('#geolocate-district-result').html('') 
            console.log(CounDist)
            <?php if ( is_page_template( 'page-pbdistricts.php' ) ) { ?>
              if (
                false
                <?php
                  $sites = get_sites(array('number' => 1000000));
                  foreach ( $sites as $site ) {
                    $ID = $site->blog_id;
                    switch_to_blog($ID);
                    $number = get_blog_option($ID,'council_district_number');
                    if ( $number ) {
                      $cycle = term_exists($current_pb_cycle,'pbcycle');
                      if ( $cycle !== 0 && $cycle !== null ) {
                ?> 
                        || String(CounDist) == "<?php echo $number; ?>"
                <?php 
                      }
                    }
                    restore_current_blog();
                  }
                ?>
              ){
                  console.log("$cycle", "<?php echo $cycle !== 0 && $cycle !== null ?>", "<?php echo $cycle ?>" )
                  console.log("$number", "<?php echo $number; ?>")
                  console.log("CounDist", CounDist)
                  jQuery('#geolocate-district-result').append(`<p class="callout text-small text-center success">PBNYC is happening in <strong>District ${CounDist}</strong>!</p>`);
              } else {
                  jQuery('#geolocate-district-result').append(`<p class="callout text-small text-center alert"><strong>District ${CounDist}</strong> is not participating in PBNYC this year. Contact your Council Member for more information.</p>`);
              }
            <?php } ?>
          } else {
            jQuery('#geolocate-district-result').html(errMessage);
            console.log("Error message 1 from API Client: ", data.address.message)
            console.log("Error message 2 from API Client: ", data.address.message2)
          }
        })
        .fail((e) => { 
          jQuery('#geolocate-district-result').html(errMessage);
        });

    });

</script>
