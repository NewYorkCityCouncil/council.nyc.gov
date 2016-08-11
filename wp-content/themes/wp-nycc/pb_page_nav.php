<?php

$current_page = get_the_permalink();
$current_pb_cycle = get_post_custom_values( 'current_pb_cycle' )[0];

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
            'key'     => 'current_pb_cycle',
            'value'   => $current_pb_cycle,
            'compare' => 'IN',
        ),
    ),
);
$pb_pages = new WP_Query( $args );
if ( $pb_pages->have_posts() ) {
    echo '<ul class="menu small">';
    while ( $pb_pages->have_posts() ) {
        $pb_pages->the_post();
        ?><li class="<?php if ( $current_page == get_the_permalink() ) { echo 'active'; } ?>"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></li><?php
    }
    echo '</ul>';
}
wp_reset_postdata();
