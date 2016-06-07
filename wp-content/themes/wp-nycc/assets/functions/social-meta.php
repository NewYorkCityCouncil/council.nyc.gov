<?php

function insert_share_meta_in_head() {
  global $post;
  if ( !has_post_thumbnail() ) {
    $share_image = get_template_directory_uri() . '/assets/images/social-img-1024x512.jpg';
  }
  else{
    $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
    $share_image = esc_attr( $thumbnail_src[0] );
  }
  ?>

  <!-- Social Share Images -->
  <meta property="og:url"          content="<?php the_permalink(); ?>" />
  <meta property="og:type"         content="website" />
  <meta property="og:title"        content="<?php the_title(); ?>" />
  <meta property="og:site_name"    content="<?php bloginfo('name'); ?>"/>
  <meta property="og:description"  content="<?php echo get_the_excerpt(); ?>" />
  <meta property="og:image"        content="<?php echo $share_image ?>"/>

  <meta name="twitter:card"        content="summary_large_image" />
  <meta name="twitter:site"        content="@NYCCouncil" />
  <meta name="twitter:creator"     content="@NYCCouncil" />
  <meta name="twitter:title"       content="#NYCCLabs" />
  <meta name="twitter:description" content="<?php echo get_the_excerpt(); ?>" />
  <meta name="twitter:image"       content="<?php echo $share_image ?>" />

  <?php
}
add_action( 'wp_head', 'insert_share_meta_in_head', 5 );
