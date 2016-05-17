<!doctype html>

<html class="no-js"  <?php language_attributes(); ?>>

  <head>
    <meta charset="utf-8">

    <!-- Force IE to use the latest rendering engine available -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>

    <!-- Mapbox -->
    <script src='https://api.mapbox.com/mapbox.js/v2.3.0/mapbox.js'></script>
    <link href='https://api.mapbox.com/mapbox.js/v2.3.0/mapbox.css' rel='stylesheet' />

    <?php wp_head(); ?>

  </head>

  <body <?php body_class(); ?>>

    <?php if ( wp_get_theme()->get('Name') == 'NYCC District' ) {} else { ?>
    <div id="map-container" class="short">
      <div id="map"></div>
      <button class="map-toggler" id="map-toggler">Expand Map</button>
    </div>
    <?php } ?>

    <div id="sticky-wrapper">
      <div data-sticky-container>
        <header class="site-header sticky" role="banner" data-sticky data-margin-top="0" data-sticky-on="small" data-anchor="sticky-wrapper">
          <div class="top-bar">
            <div class="row">
              <div class="columns">
                <?php switch_to_blog(1); ?>
                <div class="top-bar-title">
                  <strong class="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/nycc-seal.png"><?php bloginfo('name'); ?></a></strong>
                </div>
                <span class="responsive-menu-toggle" data-responsive-toggle="responsive-menu" data-hide-for="large"><span class="menu-icon dark" data-toggle></span></span>
                <div id="responsive-menu">
                  <div class="top-bar-right">
                    <?php nycc_main_nav(); ?>
                  </div>
                </div>
                <?php restore_current_blog(); ?>
              </div>
            </div>
          </div>
        </header>
      </div>

      <div class="site-container">

        <?php get_template_part( 'district_header' ); ?>
