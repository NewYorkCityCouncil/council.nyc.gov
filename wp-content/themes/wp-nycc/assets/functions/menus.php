<?php

// Register menus
register_nav_menus( array(
    'main-nav' => 'Main Menu',
) );

// Display the main menu
if ( ! function_exists ( 'nycc_main_nav' ) ) {
    function nycc_main_nav() {
        wp_nav_menu(array(
            'container' => false,
            'menu_class' => 'vertical large-horizontal menu',
            'items_wrap' => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion large-dropdown">%3$s</ul>',
            'theme_location' => 'main-nav',
            'depth' => 5,
            'fallback_cb' => false,
            'walker' => new Main_Menu_Walker()
        ));
    }
}

class Main_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = Array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"menu\">\n";
    }
}
