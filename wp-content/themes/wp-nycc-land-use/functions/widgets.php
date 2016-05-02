<?php

// Register sidebars
function nycc_register_sidebars() {
    register_sidebar(
        array(
            'id' => 'land-use-sidebar',
            'name' => __( 'Land Use Sidebar', 'nycc' ),
            'description' => __( 'Sidebar Widget Area', 'nycc' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>'
        )
    );
}
add_action( 'widgets_init', 'nycc_register_sidebars' );
