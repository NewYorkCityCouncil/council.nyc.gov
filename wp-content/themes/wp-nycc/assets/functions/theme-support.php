<?php

// Adding WP Functions & Theme Support
function nycc_theme_support() {

  // Enable the Excerpt meta box in Page edit screen
  add_post_type_support( 'page', 'excerpt' );

  // Add WP Thumbnail Support
  add_theme_support( 'post-thumbnails', array( 'page', 'nycc_caucus', 'nycc_committee', 'nycc_initiative', 'nycc_report', 'nycc_feature' ) );

  // Define image sizes
  add_image_size( 'small', 400, 400, false );
  add_image_size( 'medium', 700, 700, false );
  add_image_size( 'large', 1024, 1024, false );
  add_image_size( 'xlarge', 1300, 1300, false );

  // Add RSS Support
  add_theme_support( 'automatic-feed-links' );

  // Add Support for WP Controlled Title Tag
  // add_theme_support( 'title-tag' );

  // Add HTML5 Support
  add_theme_support( 'html5',
      array(
          'comment-list',
          'comment-form',
          'search-form',
      )
  );

  // Adding post format support
  add_theme_support( 'post-formats',
      array(
          // 'aside',   // title less blurb
          // 'gallery', // gallery of images
          // 'link',    // quick link to other site
          // 'image',   // an image
          // 'quote',   // a quick quote
          // 'status',  // a Facebook like status update
          // 'video',   // video
          // 'audio',   // audio
          // 'chat'     // chat transcript
      )
  );

  // Set the maximum allowed width for any content in the theme, like oEmbeds and images added to posts.
  $GLOBALS['content_width'] = apply_filters( '  _theme_support', 1200 );

}

add_action( 'after_setup_theme', 'nycc_theme_support' );
