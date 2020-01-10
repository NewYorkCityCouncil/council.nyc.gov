<?php

// Remove parent theme's admin pages, nav menus, and page tempaltes
require_once(get_template_directory().'/assets/functions/remove-in-child-theme.php');


require_once(get_stylesheet_directory().'/functions/posts.php');          // Posts
require_once(get_stylesheet_directory().'/functions/pages.php');          // Pages
require_once(get_stylesheet_directory().'/functions/widgets.php');        // Widgets


// Hide admin stuff
// function remove_jobs_menus(){
//   remove_menu_page( 'edit.php' );
//   remove_menu_page( 'edit-comments.php' );
//   if ( !current_user_can('administrator') ) {
//     remove_menu_page( 'plugins.php' );
//   }
//   remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
// }
// add_action( 'admin_menu', 'remove_jobs_menus' );

// // Remove admin bar links
// function remove_jobs_admin_bar_links() {
//     global $wp_admin_bar;
//     $wp_admin_bar->remove_menu('updates');
//     $wp_admin_bar->remove_menu('comments');
//     $wp_admin_bar->remove_menu('new-user');
//     $wp_admin_bar->remove_menu('new-post');
//     $wp_admin_bar->remove_menu('menus');

//     $new_content_node = $wp_admin_bar->get_node('new-content');
//     $new_content_node->href = admin_url() . 'post-new.php?post_type=page';
//     $wp_admin_bar->add_node($new_content_node);
// }
// add_action( 'wp_before_admin_bar_render', 'remove_jobs_admin_bar_links' );

// // Unregister widgets
// function nycc_unregister_jobs_widgets() {
//   unregister_widget('WP_Widget_Categories');
// }
// add_action('widgets_init', 'nycc_unregister_jobs_widgets', 11);

add_filter('jpeg_quality', function($arg){return 100;});