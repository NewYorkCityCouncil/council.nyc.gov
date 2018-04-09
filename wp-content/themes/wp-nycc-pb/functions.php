<?php

// Remove excerpts from Pages
function pb_remove_experpts_from_pages() {
  remove_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'pb_remove_experpts_from_pages', 10 );

// Remove parent theme's admin pages, nav menus, and page tempaltes
require_once(get_template_directory().'/assets/functions/remove-in-child-theme.php');

// PB-specific functions
require_once(get_stylesheet_directory().'/functions/options.php');        // PB Options
require_once(get_stylesheet_directory().'/functions/menus.php');          // PB Menu
require_once(get_stylesheet_directory().'/functions/pb-cycle.php');       // PB Cycle Meta
require_once(get_stylesheet_directory().'/functions/pages.php');          // Pages

// Hide admin stuff
function remove_pb_menus(){
  remove_menu_page( 'edit.php' );
  remove_menu_page( 'edit-comments.php' );
  if ( !current_user_can('administrator') ) {
    remove_menu_page( 'plugins.php' );
  }
  remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
}
add_action( 'admin_menu', 'remove_pb_menus' );

// Remove admin bar links
function remove_pb_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-user');
    $wp_admin_bar->remove_menu('new-post');
    $wp_admin_bar->remove_menu('menus');

    $new_content_node = $wp_admin_bar->get_node('new-content');
    $new_content_node->href = admin_url() . 'post-new.php?post_type=page';
    $wp_admin_bar->add_node($new_content_node);
}
add_action( 'wp_before_admin_bar_render', 'remove_pb_admin_bar_links' );

// Unregister widgets
function nycc_unregister_pb_widgets() {
  unregister_widget('WP_Widget_Categories');
}
add_action('widgets_init', 'nycc_unregister_pb_widgets', 11);

// Gotta register an unused taxonomies so switch_to_blog() works
register_taxonomy( 'pbtags', array(''), array('') );
register_taxonomy( 'pbcycle', array(''), array('') );

add_filter('jpeg_quality', function($arg){return 100;});