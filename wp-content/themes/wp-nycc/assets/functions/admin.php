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

//Remove the "Welcome to WordPress!" panel
remove_action('welcome_panel', 'wp_welcome_panel');

// Custom Backend Footer
function nycc_custom_admin_footer() {
    _e('<span id="footer-thankyou"><a href="http://council.nyc.gov/" target="_blank">New York City Council</a></span>', 'nycc');
}
add_filter('admin_footer_text', 'nycc_custom_admin_footer');

if ( is_super_admin() ) {
  add_filter( 'wp_default_editor', create_function('', 'return "html";') ); // Make text editor default for super admins
  // add_filter( 'user_can_richedit', '__return_false' ); // Disable the visual editor for super admins
}

// Disable the Appearance > Customize menu item
function disable_the_customizer () {
    global $submenu;
    unset($submenu['themes.php'][6]);
}
add_action('admin_menu', 'disable_the_customizer');

function disable_the_customizer_admin_bar_link() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('customize');
}
add_action( 'wp_before_admin_bar_render', 'disable_the_customizer_admin_bar_link' );
