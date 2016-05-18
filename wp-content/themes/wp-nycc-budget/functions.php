<?php

// Remove parent theme's admin pages, nav menus, and page tempaltes
require_once(get_template_directory().'/assets/functions/remove-in-child-theme.php');


// Budget-specific functions
// require_once(get_stylesheet_directory().'/functions/options.php');        // Options

// Hide admin stuff
function remove_land_use_menus(){
  remove_menu_page( 'edit.php' );
  remove_menu_page( 'edit-comments.php' );
  remove_menu_page( 'plugins.php' );
  remove_menu_page( 'tools.php' );
  remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
}
add_action( 'admin_menu', 'remove_land_use_menus' );

// Remove admin bar links
function remove_land_use_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-user');
    $wp_admin_bar->remove_menu('new-post');
}
add_action( 'wp_before_admin_bar_render', 'remove_land_use_admin_bar_links' );