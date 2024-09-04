<?php

// Register sidebars
function nycc_register_sidebars() {
    register_sidebar(
        array(
            'id' => 'data-sidebar',
            'name' => __( 'Data Sidebar', 'nycc' ),
            'description' => __( 'Sidebar Widget Area', 'nycc' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        )
    );
}
add_action( 'widgets_init', 'nycc_register_sidebars' );