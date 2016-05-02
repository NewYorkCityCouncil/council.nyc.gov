<?php

$args = array(
  'post_type'  => 'page',
  'orderby'    => 'menu_order',
  'order'      => 'ASC',
  'meta_query' => array(
    array(
      'key'     => '_wp_page_template',
      'value'   => array( 'page-pbdistricts.php', 'page-pbsidebar.php' ),
      'compare' => 'IN',
    ),
  ),
);
$pb_pages = new WP_Query( $args );
if ( $pb_pages->have_posts() ) {
  echo '<ul class="menu simple">';
  while ( $pb_pages->have_posts() ) {
    $pb_pages->the_post();
    echo '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
  }
  echo '</ul>';
}
wp_reset_postdata();
