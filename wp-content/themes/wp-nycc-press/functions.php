<?php

// Remove parent theme's admin pages, nav menus, and page tempaltes
require_once(get_template_directory().'/assets/functions/remove-in-child-theme.php');


// Press-specific functions
require_once(get_stylesheet_directory().'/functions/posts.php');          // Posts
require_once(get_stylesheet_directory().'/functions/widgets.php');        // Widgets
require_once(get_stylesheet_directory().'/functions/media.php');          // Media

// Hide admin stuff
function remove_press_menus(){
  remove_menu_page( 'plugins.php' );
  remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
}
add_action( 'admin_menu', 'remove_press_menus' );

// Remove admin bar links
function remove_press_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-user');
}
add_action( 'wp_before_admin_bar_render', 'remove_press_admin_bar_links' );

// Unregister widgets
function nycc_unregister_press_widgets() {
  unregister_widget('WP_Widget_Categories');
}
add_action('widgets_init', 'nycc_unregister_press_widgets', 11);
