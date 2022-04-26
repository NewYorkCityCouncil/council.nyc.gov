<?php

function insert_share_meta_in_head() {

  global $post;

  // Define the image
  if ( has_post_thumbnail() ) {
    // The Post has a Featured Image
    $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
    $share_image = esc_attr( $thumbnail_src[0] );
  } else if ( wp_get_theme()->get('Name') == 'NYCC Member' ) {
    // This is a Member's site, or using the District page template and switch_to_blog()
    $frontpage_id = get_option( 'page_on_front' );
    $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id($frontpage_id), 'medium' );
    $share_image = esc_attr( $thumbnail_src[0] );
  } else if ( wp_get_theme()->get('Name') == 'NYCC Press' ) {
    $share_image = 'https://council.nyc.gov/press/wp-content/uploads/sites/56/2020/04/nycc_press.jpg';
  } else {
    // There's no Featured Image, so use the default one from the theme
    $share_image = 'https://council.nyc.gov/press/wp-content/uploads/sites/56/2020/04/nycc_press.jpg';
    // $share_image = 'https://council.nyc.gov/jobs/wp-content/themes/wp-nycc/assets/images/social-img-1024x512.jpg';
  }

  // Define the the title
  if ( is_page() ) {
    $social_title = get_the_title();
  } else {
    $social_title = get_bloginfo('name');
  }

  // Define the description
  if ( !empty( $post->post_excerpt )) {
    $social_description = wp_kses_post( $post->post_excerpt );
  } else if ( !empty( $post->post_excerpt ) ) {
    $social_description = wp_kses_post( wp_trim_words( $post->post_content,'55','' ) );
  } else {
    $social_description = get_bloginfo('description');
  }

  ?>

  <!-- Web Crawler HTML meta tags -->
  <meta name="msvalidate.01" content="FEFA4B73A90D4F72594248A5798534A2" />
  <meta name="google-site-verification" content="0lcqA96XhYvLdVUgcqigEBTjX1mNTp9w4xuUOKqPCe8" />


  <!-- Social Share Images -->
  <meta property="og:url"          content="<?php the_permalink(); ?>" />
  <meta property="og:type"         content="website" />
  <meta property="og:title"        content="<?php the_title(); ?>" />
  <!-- <meta property="og:title"        content="<#?php echo $social_title ?>" /> -->
  <meta property="og:site_name"    content="<?php bloginfo('name'); ?>"/>
  <meta property="og:description"  content="<?php echo $social_description; ?>" />
  <meta property="og:image"        content="<?php echo $share_image; ?>"/>

  <meta name="twitter:card"        content="summary_large_image" />
  <meta name="twitter:site"        content="@NYCCouncil" />
  <meta name="twitter:creator"     content="@NYCCouncil" />
  <meta name="twitter:title"       content="<?php the_title(); ?>" />
  <meta name="twitter:description" content="<?php echo $social_description; ?>" />
  <meta name="twitter:image"       content="<?php echo $share_image; ?>" />

  <?php
}
add_action( 'wp_head', 'insert_share_meta_in_head', 5 );
