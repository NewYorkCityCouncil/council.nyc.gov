<?php /* Template Name: Full Page No Header/Footer */ ?>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  </head>
  <body <?php body_class(); ?>>
    <a style="position: absolute; top:0; left: -10000px;" id="skip-link-a" href="#main">Skip to main content</a>
    <!-- Google Tag Manager (noscript)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCN4XTT"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    End Google Tag Manager (noscript) -->

    <div id="sticky-wrapper"> <!-- closing tag on line 16 ./footer.php -->
      <div data-sticky-container>
        <main id="main">
            <div class="site-container" style="padding: 0;"> <!-- closing tag on line 1 ./footer.php -->
            <div>
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

                    <?php the_content(); ?>
                    <?php wp_link_pages(); ?>

                    </article>
                <?php endwhile; endif; ?>
            </div>