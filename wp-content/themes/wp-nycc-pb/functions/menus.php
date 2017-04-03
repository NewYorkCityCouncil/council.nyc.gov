<?php

// Remove main nav menu
function nycc_remove_parent_theme_menus () {
    unregister_nav_menu( 'main-nav' );
}
add_action( 'after_setup_theme', 'nycc_remove_parent_theme_menus', 20 );

// Register menus
register_nav_menus( array(
    'pb-nav' => 'PB Menu',
) );

// Display the pb menu
function nycc_pb_nav() {
    wp_nav_menu(array(
        'container' => false,
        'menu_class' => 'vertical large-horizontal menu small',
        'items_wrap' => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion large-dropdown">%3$s</ul>',
        'theme_location' => 'pb-nav',
        'depth' => 5,
        'fallback_cb' => false,
        'walker' => new pb_Menu_Walker()
    ));
}

class pb_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = Array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"menu\">\n";
    }
}
