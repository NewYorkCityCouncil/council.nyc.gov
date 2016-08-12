<?php

$current_page = get_the_permalink();
$pb_cycle_menu = get_post_custom_values( 'pb_cycle_menu' )[0];

$args = array(
    'post_type'  => 'page',
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
    'relation' => 'AND',
    'meta_query' => array(
        array(
            'key'     => '_wp_page_template',
            'value'   => array( 'page-pbdistricts.php', 'page-pbsidebar.php', 'page-pbresults.php' ),
            'compare' => 'IN',
        ),
        array(
            'key'     => 'pb_cycle_menu',
            'value'   => $pb_cycle_menu,
            'compare' => 'IN',
        ),
    ),
);
$pb_pages = new WP_Query( $args );
$the_count = $pb_pages->found_posts;
if ( ($pb_pages->have_posts()) && ($the_count > 1) ) {
    echo '<ul class="menu small">';
    while ( $pb_pages->have_posts() ) {
        $pb_pages->the_post();
        ?><li class="<?php if ( $current_page == get_the_permalink() ) { echo 'active'; } ?>"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></li><?php
    }
    echo '</ul>';
}
wp_reset_postdata();
