<?php

// Remove main nav menu
function nycc_remove_parent_theme_menus () {
    unregister_nav_menu( 'main-nav' );
}
add_action( 'after_setup_theme', 'nycc_remove_parent_theme_menus', 20 );