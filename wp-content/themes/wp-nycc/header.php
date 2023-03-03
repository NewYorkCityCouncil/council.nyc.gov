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
    <!-- Google Tag Manager (noscript)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCN4XTT"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    End Google Tag Manager (noscript) -->

    <div id="sticky-wrapper"> <!-- closing tag on line 16 ./footer.php -->
      <div data-sticky-container>
        <header class="site-header sticky" role="header" data-sticky data-margin-top="0" data-sticky-on="small" data-anchor="sticky-wrapper">
          <div class="top-bar">
            <div class="row" data-equalizer>
              <div class="columns small-10 large-12" style="background-color: #FFFFFF; padding: .3rem 0" data-equalizer-watch>
                <div style="display: flex; align-items: center; max-width: 1350px; justify-content: center; margin: 0 auto;">
                  <div class="columns">
                    <strong class="site-logo">
                      <a href="/">
                        <img alt="NYC Council Seal" src="<?php echo get_template_directory_uri(); ?>/assets/images/nyc-seal-blue.png">
                        <div style="display:inline;">
                          New York City Council
                        </div>
                      </a>
                    </strong>
                  </div>
                  <div class="show-for-large columns nav-search-container" style="display: flex; justify-content: end; align-items: end;">
                    <?php get_search_form(); ?>  
                  </div>
                </div>
              </div>
              <div class="columns small-2 large-12" id="nav-menu">
              <hr style="border-bottom: 1px solid #58595B; margin: 0;" class="show-for-large">
                <div id="mobile-nav-col"  data-equalizer-watch>
                  <?php switch_to_blog(1); ?>
                  <span class="responsive-menu-toggle" data-responsive-toggle="responsive-menu" data-hide-for="large"><span class="menu-icon" data-toggle></span></span>
                  <div role="navigation" id="responsive-menu">
                    <div class="top-bar-center">
                      <!-- replace with hardcoded html-->
                      <ul id="menu-main-menu" class="vertical large-horizontal menu dropdown" style="text-align: -webkit-center;" data-disable-hover="true" data-click-open="true" data-dropdown-menu>
                        <li id="livestream-nav"><a href="/livestream/">Livestream</a></li>
                        <li id="budget-nav"><a href="/budget/">Budget</a></li>
                        <li id="committees-nav"><a href="/committees/">Committees</a></li>
                        <li id="land-use-nav"><a href="/land-use/">Land Use</a></li>
                        <li id="hearings-nav"><a href="https://legistar.council.nyc.gov/Calendar.aspx">Upcoming Hearings</a></li>
                        <li id="districts-nav"><a href="/districts/">Find My District</a></li>
                      </ul>                  
                      <script>
                        jQuery(document).ready(function(){
                          if(window.location.pathname.startsWith("/livestream/")){
                            document.getElementById("livestream-nav").className += "active-page";
                          } else if (window.location.pathname.startsWith("/budget/")){
                            document.getElementById("budget-nav").className += "active-page";
                          } else if (window.location.pathname.startsWith("/committees/")){
                            document.getElementById("committees-nav").className += "active-page";
                          } else if (window.location.pathname.startsWith("/land-use")){
                            document.getElementById("land-use-nav").className += "active-page";
                          } else if (window.location.pathname.startsWith("/districts/")){
                            document.getElementById("districts-nav").className += "active-page";
                          };
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
