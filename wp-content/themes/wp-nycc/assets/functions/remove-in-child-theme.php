<?php

// Remove admin pages
function remove_menus(){
  remove_menu_page( 'edit.php?post_type=nycc_committee' );
  remove_menu_page( 'edit.php?post_type=nycc_caucus' );
  remove_menu_page( 'edit.php?post_type=nycc_report' );
  remove_menu_page( 'edit.php?post_type=nycc_initiative' );
  remove_menu_page( 'edit.php?post_type=nycc_feature' );
}
add_action( 'admin_menu', 'remove_menus' );


// Remove admin bar links
function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('new-user');
    $wp_admin_bar->remove_menu('new-nycc_committee');
    $wp_admin_bar->remove_menu('new-nycc_caucus');
    $wp_admin_bar->remove_menu('new-nycc_report');
    $wp_admin_bar->remove_menu('new-nycc_initiative');
    $wp_admin_bar->remove_menu('new-nycc_feature');
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );


// Remove nav menus
function nycc_remove_parent_theme_menus () {
    unregister_nav_menu( 'main-nav' );
}
add_action( 'after_setup_theme', 'nycc_remove_parent_theme_menus', 20 );


// Remove page templates: committees, caucuses, etc.
function nycc_filter_theme_page_templates( $page_templates, $this, $post ) {
    $the_theme = wp_get_theme();

    if ( isset( $page_templates['page-caucuses.php'] ) ) {
         unset( $page_templates['page-caucuses.php'] );
    }
    if ( isset( $page_templates['page-committees.php'] ) ) {
         unset( $page_templates['page-committees.php'] );
    }
    if ( isset( $page_templates['page-district-list.php'] ) ) {
         unset( $page_templates['page-district-list.php'] );
    }
    if ( isset( $page_templates['page-full-width.php'] ) ) {
         unset( $page_templates['page-full-width.php'] );
    }
    if ( isset( $page_templates['page-image-header.php'] ) ) {
         unset( $page_templates['page-image-header.php'] );
    }
    if ( isset( $page_templates['page-initiatives.php'] ) ) {
         unset( $page_templates['page-initiatives.php'] );
    }
    if ( isset( $page_templates['page-pbdistricts.php'] ) ) {
         unset( $page_templates['page-pbdistricts.php'] );
    }
    if ( isset( $page_templates['page-pbsidebar.php'] ) ) {
         unset( $page_templates['page-pbsidebar.php'] );
    }
    if ( isset( $page_templates['page-pbresults.php'] ) ) {
         unset( $page_templates['page-pbresults.php'] );
    }
    if ( isset( $page_templates['page-listdistricts.php'] ) ) {
         unset( $page_templates['page-listdistricts.php'] );
    }

    return $page_templates;
}
add_filter( 'theme_page_templates', 'nycc_filter_theme_page_templates', 20, 3 );
