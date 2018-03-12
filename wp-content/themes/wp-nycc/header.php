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
    <!-- Google Tag Manager
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TCN4XTT');</script>
    End Google Tag Manager -->

    <!-- Carto -->
    <link rel="stylesheet" href="https://cartodb-libs.global.ssl.fastly.net/cartodb.js/v3/3.15/themes/css/cartodb.css" />

    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />

    <?php wp_head(); ?>
    <script>jQuery(document).ready(function(){jQuery('#archive .dismiss span').on('click',function(){jQuery('#archive').animate({'opacity': 0}, 300)})});</script>
  </head>

  <body <?php body_class(); ?>>
    <!-- Google Tag Manager (noscript)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCN4XTT"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    End Google Tag Manager (noscript) -->
    <?php if ( wp_get_theme()->get('Name') == 'NYCC Member' ) {} else { ?>
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
                  <strong class="site-logo"><a href="<?php echo esc_url( home_url( '/', 'http' ) ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-blue.png"><?php bloginfo('name'); ?></a></strong>
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

        <?php
        if ( wp_get_theme()->get('Name') == 'NYCC Member' ) {
          $is_member_site = true;
        } else {
          $is_member_site = false;
        }
        ?>

        <div id="translation-menu" class="row column text-right<?php if ( $is_member_site == true ) { echo ' member'; } ?>">
          <button data-toggle="translation-menu-dropdown" class="button dashicons-before dashicons-translation"><span class="show-for-sr">Translate this page</span></button>
          <div class="dropdown-pane" id="translation-menu-dropdown" data-dropdown data-hover="true" data-hover-pane="true">
            <div id="google_translate_element"><span class="show-for-sr">Google Translate</span></div>
            <script type="text/javascript">
              function googleTranslateElementInit() {
                new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.FloatPosition.BOTTOM_RIGHT}, 'google_translate_element');
              }
            </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
          </div>
        </div>

        <?php
        if ( $is_member_site == true ) {
          get_template_part( 'district_header' );
        }
        ?>
