<?php /* Template Name: Widget Map (New) */ ?><!doctype html>
<html class="no-js"  <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>District Map Widget - New York City Council</title>
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.3.0/turf.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/assets/js/district-coordinates.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
        <?php wp_head(); ?>
        <style>
            body {
                padding: 20px;
            }
            #suggestions {
                list-style-type: none;
                padding: 0;
                margin: 0;
                position: absolute;
                background-color: white;
                border: 1px solid #ccc;
                z-index: 1000;
            }
            #suggestions li {
                padding: 8px;
                cursor: pointer;
            }
            #suggestions li:hover {
                background-color: #eee;
            }
            #map {
                height: 80vh;
            }
            .thumbnail-container{
                float: left;
                margin-right: 10px;
            }
            .thumbnail-container img{
                width: 50px;
            }
            .district-tooltip {
                background: none !important;
                border: none !important;
                box-shadow: none !important;
                font-weight: bold;
                font-size: 16px;
                color: rgb(23, 118, 207);
            }
        </style>
    </head>
    <body <?php body_class(); ?>>

        <div class="container">
            <div class="form-group">
                <input
                    type="text"
                    class="form-control"
                    id="address-input"
                    placeholder="Type your address..." />
                <ul id="suggestions"></ul>
            </div>
            <div id="map"></div>
        </div>

        <?php wp_footer(); ?>

        <script>
            jQuery(document).ready(function(){

            });
        </script>
        <script>
            jQuery(document).ready(function () {
                // Add the council district boundaries GeoJSON layer from NYC Dept. of Planning
                // https://www.nyc.gov/site/planning/data-maps/open-data/districts-download-metadata.page
                const council_geojson_url ='https://services5.arcgis.com/GfwWNkhOj9bNBqoJ/arcgis/rest/services/NYC_City_Council_Districts/FeatureServer/0/query?where=1%3D1&outFields=*&outSR=4326&f=geojson';
                // Fetch council district information from OpenData Portal
                // https://data.cityofnewyork.us/City-Government/City-Council-Districts/yusd-j4xi
                const soda_url ='https://data.cityofnewyork.us/resource/q9fc-2e5x.json';
                const popupData = new Object;
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
                        $link = network_site_url() . 'district-' . $number . '/';?>
                        popupData.URI<?php echo $number ?> = '<?php echo $link ?>';
                <?php   if ($current_member_site) {
                            // Switch to the current Member's site
                            switch_to_blog($current_member_site);?>
                            // Set properties for popupData Object
                            popupData.Thumb<?php echo $number ?> = '<?php echo get_blog_option($current_member_site,'council_member_thumbnail' ) ?>';
                            popupData.Member<?php echo $number ?> = '<?php echo get_blog_option($current_member_site,'council_member_name' ) ?>';
                <?php       restore_current_blog();
                            wp_reset_postdata();
                        } else {?>
                            // Fallback properties for vacant Districs
                            popupData.Thumb<?php echo $number ?> = '<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-blue.png';
                            popupData.Member<?php echo $number ?> = 'Vacant';
                <?php   }

                        endwhile;
                        wp_reset_postdata();

                    }

                    restore_current_blog();
                    wp_reset_postdata();

                ?>

                const displayDistrictInfo = (districtProps) => {

                    let CounDist = parseInt(districtProps.council_district,10),
                        popupThumbnail = popupData['Thumb' + CounDist],
                        popupMember = popupData['Member' + CounDist],
                        popupLink = popupData['URI' + CounDist];

                    let popupInfo = `
                        <div class="media-object">
                            <div class="media-object-section">
                                <div class="thumbnail">
                                    <a href="${popupLink}" <?php if ( is_page_template( 'page-widget-map.php' ) ){ ?> target="_blank"<?php } ?>>
                                        <img src="${popupThumbnail}">
                                    </a>
                                </div>
                            </div>
                            <div class="media-object-section">
                                <div class="info-container">
                                    <h4>
                                        <a href="${popupLink}" <?php if ( is_page_template( 'page-widget-map.php' ) ){ ?> target="_blank"<?php } ?>>
                                            <strong>District ${districtProps.council_district}</strong>
                                        </a>
                                    </h4>
                                    <p><strong>${popupMember}</strong></p>
                                </div>
                            </div>
                        </div>
                    `

                    return popupInfo;
                };

                const findAndShowCM = (properties, lat, lon) => {
                    jQuery.getJSON(soda_url, function (soda_data) {
                        const point = {
                            type: 'Point',
                            coordinates: [lon, lat],
                        };

                        let council_district = 'N/A';
                        soda_data.forEach(function (feature) {
                            const geom = feature.the_geom;
                            if (turf.booleanPointInPolygon(point, geom)) {
                                council_district = feature.coun_dist;
                            }
                        });

                        properties.council_district = council_district;
                        console.log("properties", properties)
                        if (properties.council_district){
                            // Get the CM data
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            map.setView([lat, lon], 12);
                            marker = L.marker([lat, lon])
                                .addTo(map)
                                .bindPopup(displayDistrictInfo(properties))
                                .openPopup();
                        };
                    });
                }

                let map = L.map('map').setView([40.7128, -74.006], 11);
                L.tileLayer(
                    'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
                    {
                        attribution:
                            '&copy; <a href="https://carto.com/attributions">CARTO</a>',
                        subdomains: 'abcd',
                        maxZoom: 19,
                    }
                ).addTo(map);

                jQuery('a').attr('target', '_blank');

                jQuery.getJSON(council_geojson_url, function (data) {
                    L.geoJSON(data, {
                        style: function (feature) {
                            return {
                                color: '#00008B',
                                weight: 2,
                                opacity: 0.2,
                                fillOpacity: 0,
                            };
                        },
                        let manualCoordinates = districtLabelCoordinates[districtNumber];
                        let districtNumber = feature.properties.CounDist;

                        if (manualCoordinates) {
                            L.marker([manualCoordinates[0], manualCoordinates[1]], {
                                icon: L.divIcon({
                                    className: 'district-tooltip',
                                    html: `<div>${districtNumber}</div>`,
                                }),
                            }).addTo(map);
                        },
                    }).addTo(map);
                });

                let marker;

                // Event handler for typing into the search bar from NYC Dept. of Planning
                // https://geosearch.planninglabs.nyc/
                jQuery('#address-input').on('input', function () {
                    let query = jQuery(this).val();
                    if (query.length >= 3) {
                        jQuery.getJSON(
                            `https://geosearch.planninglabs.nyc/v2/autocomplete?text=${query}`,
                            function (data) {
                                jQuery('#suggestions').empty();
                                data.features.forEach(function (item) {
                                    jQuery('#suggestions').append(`<li>${item.properties.label}</li>`);
                                });
                            }
                        );
                    } else {
                        jQuery('#suggestions').empty();
                    }
                });

                // Event handler for clicking on a address suggestion from NYC Dept. of Planning
                // https://geosearch.planninglabs.nyc/
                jQuery('#suggestions').on('click', 'li', function () {
                    let address = jQuery(this).text();
                    jQuery('#address-input').val(address);
                    jQuery('#suggestions').empty();
                    jQuery.getJSON(
                        `https://geosearch.planninglabs.nyc/v2/search?text=${address}`,
                        function (data) {
                            if (data.features.length) {
                                const feature = data.features[0];
                                const lat = feature.geometry.coordinates[1];
                                const lon = feature.geometry.coordinates[0];
                                const properties = feature.properties;
                                findAndShowCM(properties, lat, lon);
                            }
                        }
                    );
                });

                map.on('click', function(e) {
                    jQuery('#suggestions').empty();
                    let lat = e.latlng["lat"]
                    let lon = e.latlng["lng"]
                    const properties = {}
                    findAndShowCM(properties, lat, lon);
                });
            });
        </script>
        <!-- Google Analytics -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-68577323-2', 'auto');
            ga('send', 'pageview');
        </script>
    </body>
</html>
