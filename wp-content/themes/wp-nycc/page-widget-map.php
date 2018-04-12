<?php /* Template Name: Widget (Map) */ ?><!doctype html>
<html class="no-js"  <?php language_attributes(); ?>>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cartodb-libs.global.ssl.fastly.net/cartodb.js/v3/3.15/themes/css/cartodb.css" />
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
    <?php wp_head(); ?>

    <style>

      html,
      body,
      #map {
        height: 100%;
        width: 100%;
      }
      #map-container {
        height: auto;
        width: auto;
        position: absolute;
        top: 4rem;
        right: 0;
        bottom: 0;
        left: 0;
      }
      .leaflet-top.leaflet-right {
        position: absolute;
        top: 15px;
        right: 10px;
        bottom: auto;
        left: 46px;
        padding-right: 0;
      }
      .leaflet-control.addresslookup,
      #addresslookup,
      .addresslookup input {
        margin: 0;
        width: 100%;
      }

      header.map-widget-header {
        padding: 0 1rem;
        height: 4rem;
        background-color: white;
        z-index: 2;
        position: relative;
        background-color: #2F56A6;
        box-shadow: 0 0.125rem 0 rgba(0,0,0,0.1);
      }
      h1.site-logo {
        font-size: 1rem;
        position: relative;
      }
      h1.site-logo a {
        display: block;
        line-height: 4rem;
        padding: 0;
        margin: 0;
        color: white;
        overflow: hidden;
      }
      .map-widget-title {
        position: absolute;
        top: 100%;
        left: 5.375em;
        margin-top: -1.5em;
        font-weight: bold;
        line-height: 1;
      }
      .map-widget-title em {
        font-size: 0.9rem;
      }

    </style>

  </head>
  <body <?php body_class(); ?>>

    <header class="map-widget-header">
      <h1 class="site-logo"><a href="<?php echo esc_url( home_url( '/districts/', 'http' ) ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-white.png">New York City Council <span class="map-widget-title sans-serif"><em>Find My District</em></span></a></h1>
    </header>

    <div id="map-container" aria-hidden="true"><div id="map"></div></div>

    <?php wp_footer(); ?>

    <?php get_template_part( 'map_scripts' ); ?>

    <script>
    $(document).ready(function(){
      $('a').attr('target', '_blank');
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
