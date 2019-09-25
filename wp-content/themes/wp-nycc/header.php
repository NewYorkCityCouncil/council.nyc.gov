<!doctype html>

<html class="no-js"  <?php language_attributes(); ?>>

  <head>
    <meta charset="utf-8">

    <!-- Force IE to use the latest rendering engine available -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
      function console_log( $data ) {
        $output  = "<script>console.log( 'PHP debugger: ";
        $output .= json_encode(print_r($data, true));
        $output .= "' );</script>";
        echo $output;
      }
    ?>

    <title><?php the_title();?> - <?php bloginfo( $show = 'name' )?></title>

    <script>
      if (document.getElementsByTagName("title").length > 1){
        extraTitle = document.getElementsByTagName("title")[1];
        extraTitle.parentNode.removeChild(extraTitle);
      };
    </script>
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
    <div id="map-container" aria-hidden="true" class="short">
      <div id="map"></div>
      <button class="map-toggler" id="map-toggler">Expand Map</button>
    </div>
    <?php } ?>

    <div id="sticky-wrapper">
      <div data-sticky-container>
        <header class="site-header sticky" role="header" data-sticky data-margin-top="0" data-sticky-on="small" data-anchor="sticky-wrapper">
          <div class="top-bar">
            <div class="row">
              <div class="columns">
                <?php switch_to_blog(1); ?>
                <div class="top-bar-title">
                  <strong class="site-logo"><a href="<?php echo esc_url( home_url( '/', 'http' ) ); ?>"><img alt="NYC Council Seal" src="<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-blue.png"><h1 class="site-logo" style="display:inline;"><?php bloginfo('name'); ?></h1></a></strong>
                </div>
                <span class="responsive-menu-toggle" data-responsive-toggle="responsive-menu" data-hide-for="large"><span class="menu-icon dark" data-toggle></span></span>
                <div role="navigation" id="responsive-menu">
                  <div class="top-bar-right">
                    <!-- replace with hardcoded html-->
                    <ul id="menu-main-menu" class="vertical large-horizontal menu dropdown" data-responsive-menu="accordion large-dropdown" role="menubar" data-dropdown-menu="xrgjtw-dropdown-menu" data-mutate="99wzrm-responsive-menu" data-events="mutate">
                      <li class="custom-dropdown-hover menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1582 is-dropdown-submenu-parent opens-left" role="menuitem" aria-haspopup="true" aria-label="About" data-is-click="false">
                        <a href="#" aria-expanded="false">About</a>
                        <ul class="menu submenu is-dropdown-submenu first-sub vertical" data-submenu="" role="menu">
                          <li id="menu-item-1100" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1100 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/visit-the-council/">Visit the Council</a></li>
                          <li id="menu-item-48" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-48 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/about/">What we do</a></li>
                          <li id="menu-item-16" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-16 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/committees/">Committees</a></li>
                          <li id="menu-item-15" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-15 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/caucuses/">Caucuses</a></li>
                        </ul>
                      </li>
                      <!-- <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-45" role="menuitem">
                        <a href="https://council.nyc.gov/events/">Events</a>
                      </li> -->
                      <li class="custom-dropdown-hover menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-1583 is-dropdown-submenu-parent opens-left" role="menuitem" aria-haspopup="true" aria-label="Districts" data-is-click="false">
                        <a href="#" aria-expanded="false">Districts</a>
                        <ul class="menu submenu is-dropdown-submenu first-sub vertical" data-submenu="" role="menu">
                          <li id="menu-item-1209" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1209 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/districts/">Council Members &amp; Districts</a></li>
                          <li id="menu-item-1208" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1208 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/district-info/">District Info</a></li>
                        </ul>
                      </li>
                      <li class="custom-dropdown-hover menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-44 is-dropdown-submenu-parent opens-left" role="menuitem" aria-haspopup="true" aria-label="Legislation" data-is-click="false">
                        <a href="#" aria-expanded="false">Legislation</a>
                        <ul class="menu submenu is-dropdown-submenu first-sub vertical" data-submenu="" role="menu">
                          <li id="menu-item-238" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-238 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/legislation/">Our legislative process</a></li>
                          <li id="menu-item-237" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-237 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="http://legistar.council.nyc.gov/Calendar.aspx">Hearings Calendar and Video Archive</a></li>
                          <li id="menu-item-861" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-861 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="http://legistar.council.nyc.gov/Legislation.aspx">Search legislation</a></li>
                          <li id="menu-item-239" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-239 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="http://laws.council.nyc.gov">Search legislation via Councilmatic (beta)</a></li>
                          <li id="menu-item-1601" class="menu-item menu-item-type-post_type menu-item-object-post menu-item-1601 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/news/2017/11/17/api/">Legislative API</a></li>
                          <li id="menu-item-291" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-291 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/live/">Live video</a></li>
                          <li id="menu-item-747" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-747 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/data/">Data</a></li>
                        </ul>
                      </li>
                      <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-45" role="menuitem">
                        <a href="https://council.nyc.gov/budget/">Budget</a>
                      </li>
                      <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-31" role="menuitem">
                        <a href="https://council.nyc.gov/land-use/">Land Use</a>
                      </li>
                      <li class="custom-dropdown-hover menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-46 is-dropdown-submenu-parent opens-left" role="menuitem" aria-haspopup="true" aria-label="Press &amp; News" data-is-click="false">
                        <a href="#" aria-expanded="false">Press &amp; News</a>
                        <ul class="menu submenu is-dropdown-submenu first-sub vertical" data-submenu="" role="menu">
                          <li id="menu-item-304" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-304 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/press/">Press Releases</a></li>
                          <li id="menu-item-1317" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1317 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/news/category/reports/">Reports</a></li>
                          <li id="menu-item-302" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-302 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/press/press-photos/">Photos</a></li>
                          <li id="menu-item-303" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-303 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/news/tag/video/">Videos</a></li>
                          <li id="menu-item-1060" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-1060 is-submenu-item is-dropdown-submenu-item" role="menuitem"><a href="https://council.nyc.gov/press/audio/">Audio</a></li>
                        </ul>
                      </li>
                    </ul>                  
                    <!--<#?php nycc_main_nav(); ?>-->
                    <script>
                      jQuery(".custom-dropdown-hover").mouseenter(function(){
                        jQuery(this).children().first().attr("aria-expanded", "true");
                      }).focusin(function(){
                        jQuery(this).children().first().attr("aria-expanded", "true");
                      });
                      jQuery(".custom-dropdown-hover").mouseleave(function(){
                        jQuery(this).children().first().attr("aria-expanded", "false");
                      }).focusout(function(){
                        jQuery(this).children().first().attr("aria-expanded", "false");
                      });
                      jQuery(document).ready(function(){
                        $("#translation-menu-dropdown").css("visibility","visible").hide();
                        jQuery("#translation-button").click(function(){
                          if(jQuery("#translation-menu-dropdown").css("display") === "none"){
                            jQuery("#translation-menu-dropdown").show();
                          } else{
                            jQuery("#translation-menu-dropdown").hide();
                          }
                        });
                        jQuery("#close-menu").click(function(){jQuery("#translation-menu-dropdown").hide();});
                      });
                    </script>
                  </div>
                </div>
                <?php restore_current_blog(); ?>
              </div>
            </div>
          </div>
        </header>
      </div>
      <main>
        <div class="site-container">
          <?php if ( wp_get_theme()->get('Name') == 'NYCC Member' ) { $is_member_site = true; } else { $is_member_site = false; } ?>
          <div id="translation-menu" aria-hidden="true" class="row column text-right<?php if ( $is_member_site == true ) { echo ' member'; } ?>">
            <button id="translation-button" class="button dashicons-before dashicons-translation">
              <span class="show-for-sr">Translate this page</span>
            </button>
            <div class="dropdown-pane" id="translation-menu-dropdown">
              <div id="close-menu" style="position:absolute; top:-5px; right:3px; cursor:pointer;">×</div>
              <div id="google_translate_element"><span class="show-for-sr">Google Translate</span></div>
              <script type="text/javascript">function googleTranslateElementInit() {new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.FloatPosition.BOTTOM_RIGHT}, 'google_translate_element');}</script>
              <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
            </div>
          </div>
          <?php if ( $is_member_site == true ) { get_template_part( 'district_header' ); } ?>
          <!-- START OF MAIN CONTENT -->
