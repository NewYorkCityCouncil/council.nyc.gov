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
function nycc_jobs_unregister_default_widgets() {
  unregister_widget('WP_Widget_Archives');
  // unregister_widget('WP_Widget_Search');
  // unregister_widget('WP_Widget_Text');
  unregister_widget('WP_Widget_Categories');
  unregister_widget('WP_Widget_Recent_Posts');
  unregister_widget('WP_Nav_Menu_Widget');
}
add_action('widgets_init', 'nycc_jobs_unregister_default_widgets', 11);
