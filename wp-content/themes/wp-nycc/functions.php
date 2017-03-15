<?php

require_once(get_template_directory().'/assets/functions/enqueue-scripts.php');   // Foundation & Theme CSS/JS
require_once(get_template_directory().'/assets/functions/menus.php');             // Menus
require_once(get_template_directory().'/assets/functions/widgets.php');           // Sidebars & Widgets
require_once(get_template_directory().'/assets/functions/cleanup.php');           // WP Head and other cleanup functions
require_once(get_template_directory().'/assets/functions/admin.php');             // Customize the WordPress admin
require_once(get_template_directory().'/assets/functions/disable-emoji.php');     // Remove emoji support
require_once(get_template_directory().'/assets/functions/login.php');             // Customize the WordPress login menu
require_once(get_template_directory().'/assets/functions/theme-support.php');     // Theme support options
require_once(get_template_directory().'/assets/functions/excerpts.php');          // Excerpts
require_once(get_template_directory().'/assets/functions/comments.php');          // Comments
require_once(get_template_directory().'/assets/functions/attachments.php');       // Attachments
require_once(get_template_directory().'/assets/functions/archive-titles.php');    // Archive Titles
require_once(get_template_directory().'/assets/functions/page-navigation.php');   // Numeric Page Navigation
require_once(get_template_directory().'/assets/functions/social-meta.php');       // Facebook & Twitter share meta
require_once(get_template_directory().'/assets/functions/roles.php');             // Roles & Capabilities

if ( is_main_site() ) {
  require_once(get_template_directory().'/assets/functions/options.php');         // NYCC Site Options
}

// Editor Styles
add_editor_style( 'assets/css/editor.min.css' );

// Custom Post Types
require_once(get_template_directory().'/assets/functions/districts.php');         // Districts custom post type
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


// Filter to remove password protected posts from SQL query
function exclude_protected($where) {
    global $wpdb;
    return $where .= " AND {$wpdb->posts}.post_password = '' ";
}
// Apply protected posts filter everywhere except single/page/admin
function exclude_protected_action($query) {
    if( !is_single() && !is_page() && !is_admin() ) {
        add_filter( 'posts_where', 'exclude_protected' );
    }
}
add_action('pre_get_posts', 'exclude_protected_action');


// Override the default tagline "Just another [WordPress] site"
if ( is_network_admin() ) {
  function set_default_options($blog_id) {
    update_blog_option($blog_id, 'blogdescription', 'New York City Council');
  }
  add_action( 'wpmu_new_blog', 'set_default_options', 10 , 2 );
}
