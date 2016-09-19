<?php

$current_pb_cycle = get_post_custom_values( 'current_pb_cycle' )[0];

?>

<script type="text/javascript">

    /*--------------------------------------------------
      Geolocate District
    --------------------------------------------------*/
    jQuery('#geolocate-district').submit(function(e) {
        e.preventDefault();

        // get values from the form
        var myAddress = jQuery('#myAddress').val();
        var myBorough = jQuery('#myBorough').val();

        // talk to the NYC Geoclient API
        var apiKey = 'b06b5faa94200cd3a6ac32cb30fd76a8';
        var apiId = 'a9596ad7';
        var apiQuery = 'https://api.cityofnewyork.us/geoclient/v1/search.json?input=' + myAddress + ' ' + myBorough + '&app_id=' + apiId + '&app_key=' + apiKey;

        var errMessage = '<div class="callout alert text-small text-center">Please enter a valid street address and borough.</div>'

        jQuery.ajax({
            url: apiQuery,
            dataType: 'jsonp',
            success: function (data) {

                jQuery('#geolocate-district-result').html('');

                if ( data.status == 'OK' ) {
                    for (var key in data.results) {
                       if (data.results.hasOwnProperty(key)) {

                            var theRequest = data.results[key].request;
                                theRequest = theRequest.replace('intersection [crossStreetOne=','');
                                theRequest = theRequest.replace(', crossStreetTwo=',' & ');
                                theRequest = theRequest.replace(', compassDirection=null','');
                                theRequest = theRequest.replace('place [name=','');
                                theRequest = theRequest.replace('address [houseNumber=','');
                                theRequest = theRequest.replace(', street=',' ');
                                theRequest = theRequest.replace(', borough=null',' ');
                                theRequest = theRequest.replace(', borough=',' ');
                                theRequest = theRequest.replace(', zip=null',' ');
                                theRequest = theRequest.replace(', zip=',', ');
                                theRequest = theRequest.replace(']',' ');
                                theRequest = theRequest.replace('MANHATTAN','(Manhattan)');
                                theRequest = theRequest.replace('BRONX','(Bronx)');
                                theRequest = theRequest.replace('BROOKLYN','(Brooklyn)');
                                theRequest = theRequest.replace('QUEENS','(Queens)');
                                theRequest = theRequest.replace('STATEN ISLAND','(Staten Island)');

                            var districtNumber = data.results[key].response.cityCouncilDistrict;
                                districtNumber = districtNumber.replace(/^0+/, '');

                            jQuery('#geolocate-district-result').append(
                              '<p class="callout secondary text-small text-center no-margin">' + theRequest + 'is in District&nbsp;' + districtNumber + '.</p>'
                            );

                            if ( false<?php
                              $sites = wp_get_sites();
                              foreach ( $sites as $site ) {
                                $ID = $site['blog_id'];
                                switch_to_blog($ID);
                                $number = get_blog_option($ID,'council_district_number');
                                if ( $number ) {
                                  $cycle = term_exists($current_pb_cycle,'pbcycle');
                                  if ( $cycle !== 0 && $cycle !== null ) {
                                    ?> || districtNumber == "<?php echo $number; ?>"<?php
                                  }
                                }
                                restore_current_blog();
                              }
                              ?>) {
                                jQuery('#geolocate-district-result').append(
                                  '<p class="callout text-small text-center success ">PBNYC is happening in District&nbsp;' + districtNumber + '!</p>'
                                );
                            } else {
                                jQuery('#geolocate-district-result').append(
                                  '<p class="callout text-small text-center alert ">District&nbsp;' + districtNumber + ' is not participating in PBNYC this year. Contact your Council Member for more information.</p>'
                                );
                            }

                       }
                  }
              } else {
                jQuery('#geolocate-district-result').html(errMessage);
              }

            },
            error: function(){
              jQuery('#geolocate-district-result').html(errMessage);
            }
        });

    });

</script>
