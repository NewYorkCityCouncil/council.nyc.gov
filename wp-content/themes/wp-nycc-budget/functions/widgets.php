<?php

// Register sidebars
function nycc_register_sidebars() {
    register_sidebar(
        array(
            'id' => 'posts-sidebar',
            'name' => __( 'Posts Sidebar', 'nycc' ),
            'description' => __( 'widget area for posts, archives, search, etc', 'nycc' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        )
    );
    register_sidebar(
        array(
            'id' => 'frontpage-sidebar',
            'name' => __( 'Front Page Sidebar', 'nycc' ),
            'description' => __( 'widget area for front page only', 'nycc' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        )
    );
}
add_action( 'widgets_init', 'nycc_register_sidebars' );
