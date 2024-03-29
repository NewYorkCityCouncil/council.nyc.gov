<?php

// Remove parent theme's admin pages, nav menus, and page tempaltes
require_once(get_template_directory().'/assets/functions/remove-in-child-theme.php');

require_once(get_stylesheet_directory().'/functions/menus.php'); 
require_once(get_stylesheet_directory().'/functions/events.php');         // Events
require_once(get_stylesheet_directory().'/functions/posts.php');          // Posts
require_once(get_stylesheet_directory().'/functions/pages.php');          // Pages
require_once(get_stylesheet_directory().'/functions/widgets.php');        // Widgets
require_once(get_stylesheet_directory().'/functions/theme-support.php');  // Theme Support


// Hide admin stuff
function remove_ced_menus(){
    remove_menu_page( 'edit.php' );
    remove_menu_page( 'edit-comments.php' );
    if ( !current_user_can('administrator') ) {
        remove_menu_page( 'plugins.php' );
    }
    remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
}
add_action( 'admin_menu', 'remove_ced_menus' );
  
// Remove admin bar links
function remove_ced_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-user');
    $wp_admin_bar->remove_menu('new-post');
}
add_action( 'wp_before_admin_bar_render', 'remove_ced_admin_bar_links' );

add_filter('jpeg_quality', function($arg){return 100;});
