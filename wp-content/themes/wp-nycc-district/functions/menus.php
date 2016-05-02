<?php

// Register nav menus
register_nav_menus( array(
    'primary' => 'Primary Menu',
) );

// Display the primary menu
function nycc_primary_nav() {
    wp_nav_menu(array(
        'container' => 'div',
        'container_class' => 'widget district-menu',
        'menu_class' => 'vertical menu',
        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'theme_location' => 'primary',
        'depth' => 5,
        'fallback_cb' => false,
        'walker' => new Main_Menu_Walker()
    ));
}
