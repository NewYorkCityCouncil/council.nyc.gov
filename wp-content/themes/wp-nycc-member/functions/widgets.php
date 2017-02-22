<?php

// Register sidebars
function nycc_register_sidebars() {
    register_sidebar(
        array(
            'id' => 'posts-sidebar',
            'name' => __( 'Sidebar', 'nycc' ),
            'description' => __( 'Widget Area', 'nycc' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>'
        )
    );
}
add_action( 'widgets_init', 'nycc_register_sidebars' );

// Unregister widgets
function nycc_unregister_press_widgets() {
  unregister_widget('WP_Nav_Menu_Widget');
}
add_action('widgets_init', 'nycc_unregister_press_widgets', 11);
