<?php

// Remove page templates
function nycc_land_use_filter_theme_page_templates( $page_templates, $this, $post ) {
    $the_theme = wp_get_theme();

    if ( isset( $page_templates['page-sidebar.php'] ) ) {
         unset( $page_templates['page-sidebar.php'] );
    }

    return $page_templates;
}
add_filter( 'theme_page_templates', 'nycc_land_use_filter_theme_page_templates', 20, 3 );
