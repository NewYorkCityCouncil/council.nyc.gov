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
    <script>
      jQuery(document).ready(function(){jQuery('#archive .dismiss span').on('click',function(){jQuery('#archive').animate({'opacity': 0}, 300)})});
    </script>
  </head>
  
  <body <?php body_class(); ?>>
    <a style="position: absolute; top:0; left: -10000px;" id="skip-link-a" href="#main">Skip to main content</a>
    <!-- <script>jQuery("#skip-link-a").focus(function(){jQuery("#skip-link-li").css("position","initial")}).focusout(function(){jQuery("#skip-link-li").css("position","absolute")});</script> -->
    <!-- Google Tag Manager (noscript)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCN4XTT"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    End Google Tag Manager (noscript) -->
    <!-- <?php if ( wp_get_theme()->get('Name') == 'NYCC Member' ) {} else { ?>
    <div id="map-container" class="short" aria-hidden="true">
      <div id="map"></div>
      <button class="map-toggler" id="map-toggler">Expand Map</button>
    </div>
    <?php } ?> -->

    <div id="sticky-wrapper"> <!-- closing tag on line 16 ./footer.php -->
      <div data-sticky-container>
        <header class="site-header sticky" role="header" data-sticky data-margin-top="0" data-sticky-on="small" data-anchor="sticky-wrapper">
          <div class="top-bar">
            <div class="row" style="max-width: 100%" data-equalizer>
              <div class="columns small-8 large-12" style="background-color: #2F56A6; padding: .3rem 0" data-equalizer-watch>
                <div style="display: flex; align-items: center;">
                  <div class="columns medium-12 large-7">
                    <strong class="site-logo">
                      <a href="/">
                        <img alt="NYC Council Seal" src="<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-white.png">
                        <div class="site-logo" style="display:inline;">
                          New York City Council
                        </div>
                      </a>
                    </strong>
                  </div>
                  <div class="show-for-large columns large-5 nav-search-container" style="display: flex; justify-content: end; align-items: end;">
                    <?php get_search_form(); ?>  
                  </div>
                </div>
              </div>
              <div class="row" style="background-color: #23417D">
                <div class="columns small-4 large-12" id="mobile-nav-col"  data-equalizer-watch>
                  <?php switch_to_blog(1); ?>
                  <span class="responsive-menu-toggle" style="line-height: none" data-responsive-toggle="responsive-menu" data-hide-for="large"><span class="menu-icon" data-toggle></span></span>
                  <div role="navigation" id="responsive-menu">
                    <div class="top-bar-center">
                      <!-- replace with hardcoded html-->
                      <ul id="menu-main-menu" class="vertical large-horizontal menu dropdown" style="text-align: -webkit-center;" data-disable-hover="true" data-click-open="true" data-dropdown-menu>
                        <li><a href="/livestream/">Live Stream</a></li>
                        <li><a href="/budget/">Budget</a></li>
                        <li><a href="/committees/">Committees</a></li>
                        <li><a href="/land-use/">Land Use</a></li>
                        <li><a href="/#hearings">Upcoming Hearings</a></li>
                        <li><a href="/districts/">Find My District <i style="margin-left: 0.35rem;" class="fa fa-search"></i></a></li>
                      </ul>                  
                      <script>
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
          </div>
        </header>
      </div>
      <main id="main">
        <div class="site-container"> <!-- closing tag on line 1 ./footer.php -->
          <?php if ( wp_get_theme()->get('Name') == 'NYCC Member' ) { $is_member_site = true; } else { $is_member_site = false; } ?>
          <div id="translation-menu" class="row column text-right<?php if ( $is_member_site == true ) { echo ' member'; } ?>">
            <button id="translation-button" class="button dashicons-before dashicons-translation">
              <span class="show-for-sr">Translate this page</span>
            </button>
            <div class="dropdown-pane" id="translation-menu-dropdown">
              <button id="close-menu" style="position:absolute; top:3px; right:3px; cursor:pointer;">&#10005;</button>
              <div id="google_translate_element"><span class="show-for-sr">Google Translate</span></div>
              <script type="text/javascript">function googleTranslateElementInit() {new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.FloatPosition.BOTTOM_RIGHT}, 'google_translate_element');}</script>
              <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
            </div>
          </div>
          <?php if ( $is_member_site == true ) { get_template_part( 'district_header' ); } ?>
          <!-- START OF MAIN CONTENT -->
