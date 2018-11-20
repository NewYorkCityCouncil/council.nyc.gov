<?php

// Remove page templates
function nycc_member_filter_theme_page_templates( $page_templates, $post ) {
    $the_theme = wp_get_theme();

    if ( isset( $page_templates['page-sidebar.php'] ) ) {
         unset( $page_templates['page-sidebar.php'] );
    }

    return $page_templates;
}
add_filter( 'theme_page_templates', 'nycc_member_filter_theme_page_templates', 20, 2 );
