<?php

// Disable dashboard widgets
function disable_default_dashboard_widgets() {
    remove_meta_box('dashboard_right_now', 'dashboard', 'core');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'core');
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'core');
    remove_meta_box('dashboard_plugins', 'dashboard', 'core');
    // remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'core');
    remove_meta_box('dashboard_primary', 'dashboard', 'core');
    remove_meta_box('dashboard_secondary', 'dashboard', 'core');
}
add_action('admin_menu', 'disable_default_dashboard_widgets');

// Remove the "Welcome to WordPress!" panel
remove_action('welcome_panel', 'wp_welcome_panel');

// Custom Backend Footer
function nycc_custom_admin_footer() {
    _e('<span id="footer-thankyou"><a href="http://council.nyc.gov/" target="_blank">New York City Council</a></span>', 'nycc');
}
add_filter('admin_footer_text', 'nycc_custom_admin_footer');

if ( is_super_admin() ) {
  add_filter( 'wp_default_editor', function(){ return "html";} ); // Make text editor default for super admins
  // create_function is deprecated in v7.2
  // add_filter( 'wp_default_editor', create_function('', 'return "html";') ); 
  // add_filter( 'user_can_richedit', '__return_false' ); // Disable the visual editor for super admins
}

// Remove Profile sidebar menu item
function remove_profile_sidebar_menu_item(){
  remove_menu_page( 'profile.php' );
  remove_submenu_page( 'users.php', 'profile.php' );
}
add_action( 'admin_menu', 'remove_profile_sidebar_menu_item' );

// Don't show the Admin Bar on the Map Widget page template
function my_theme_hide_admin_bar($bool) {
  if ( is_page_template( 'page-widget-map.php' ) ) :
    return false;
    else :
      return $bool;
    endif;
}
add_filter('show_admin_bar', 'my_theme_hide_admin_bar');

// Only show Contact Forms 7 for Super Admins
if ( !is_super_admin() ) {
  function remove_wpcf7() {
    remove_menu_page( 'wpcf7' );
  }
  add_action('admin_menu', 'remove_wpcf7');
}
