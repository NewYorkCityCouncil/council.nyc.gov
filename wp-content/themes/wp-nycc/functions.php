<?php

require_once(get_template_directory().'/assets/functions/enqueue-scripts.php');   // Foundation & Theme CSS/JS
require_once(get_template_directory().'/assets/functions/menus.php');             // Menus
require_once(get_template_directory().'/assets/functions/widgets.php');           // Sidebars & Widgets
require_once(get_template_directory().'/assets/functions/cleanup.php');           // WP Head and other cleanup functions
require_once(get_template_directory().'/assets/functions/admin.php');             // Customize the WordPress admin
require_once(get_template_directory().'/assets/functions/disable-emoji.php');     // Remove emoji support
require_once(get_template_directory().'/assets/functions/login.php');             // Customize the WordPress login menu
require_once(get_template_directory().'/assets/functions/theme-support.php');     // Theme support options
require_once(get_template_directory().'/assets/functions/comments.php');          // Comments
require_once(get_template_directory().'/assets/functions/attachments.php');       // Attachments
require_once(get_template_directory().'/assets/functions/archive-titles.php');    // Archive Titles
require_once(get_template_directory().'/assets/functions/page-navigation.php');   // Numeric Page Navigation

// Custom Post Types
require_once(get_template_directory().'/assets/functions/committees.php');        // Committees custom post type
require_once(get_template_directory().'/assets/functions/caucuses.php');          // Caucuses custom post type
require_once(get_template_directory().'/assets/functions/initiatives.php');       // Initiatives custom post type
require_once(get_template_directory().'/assets/functions/reports.php');           // Reports custom post type
require_once(get_template_directory().'/assets/functions/features.php');          // Features custom post type

// Remove auto paragraph content filter on certain page templates
remove_filter('the_content','wpautop');
function nycc_custom_formatting($content){
    if( is_page_template( 'page-full-width.php' ) )
        return $content;
    else
        return wpautop($content);
}
add_filter('the_content','nycc_custom_formatting');


// Remove the WordPress.org menu
function remove_wp_dot_org_menu( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', 'remove_wp_dot_org_menu', 999 );
