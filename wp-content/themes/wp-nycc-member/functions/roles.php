<?php

// Let Editors manage theme options
function nycc_edit_admin_capabilities() {
  $role_object = get_role( 'editor' );
  $role_object->add_cap( 'edit_theme_options' );
}
add_action( 'init', 'nycc_edit_admin_capabilities' );


if( current_user_can('editor') && !current_user_can('administrator') ) {

  function remove_themes_menu(){
    // Hide the Appearace menu (Themes)
    remove_menu_page( 'themes.php' );
    // Show Menus & Widgets submenus
    add_menu_page( __('Menus', 'nav-menus'), __('Menus', 'nav-menus'), 'edit_theme_options', 'nav-menus.php', '', 'dashicons-menu', 50 );
    add_menu_page( __('Widgets', 'widgets'), __('Widgets', 'widgets'), 'edit_theme_options', 'widgets.php', '', 'dashicons-welcome-widgets-menus', 50 );
  }
  add_action( 'admin_menu', 'remove_themes_menu' );

}

// Hide stuff for PB Admin
function remove_pbadmin_menus(){
  if ( current_user_can('pbadmin') && !current_user_can('administrator') ) {
    remove_menu_page( 'edit.php' );
    remove_menu_page( 'edit.php?post_type=page' );
    remove_menu_page( 'themes.php' );
    remove_menu_page( 'options-general.php' );
    remove_menu_page( 'upload.php' );
    remove_menu_page( 'edit-tags.php?taxonomy=category' );
  }
}
add_action( 'admin_menu', 'remove_pbadmin_menus' );
