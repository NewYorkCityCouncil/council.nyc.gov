<?php

// Register sidebars
if ( ! function_exists ( 'nycc_register_sidebars' ) ) {
    function nycc_register_sidebars() {
        register_sidebar(
            array(
                'id' => 'posts-sidebar',
                'name' => __( 'Sidebar', 'nycc' ),
                'description' => __( 'Widget Area', 'nycc' ),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h2 class="widget-title">',
                'after_title' => '</h2>'
            )
        );
    }
    add_action( 'widgets_init', 'nycc_register_sidebars' );
}

// Unregister widgets
function nycc_unregister_default_widgets() {
  unregister_widget('WP_Widget_Pages');
  unregister_widget('WP_Widget_Calendar');
  // unregister_widget('WP_Widget_Archives');
  unregister_widget('WP_Widget_Links');
  unregister_widget('WP_Widget_Meta');
  // unregister_widget('WP_Widget_Search');
  // unregister_widget('WP_Widget_Text');
  // unregister_widget('WP_Widget_Categories');
  // unregister_widget('WP_Widget_Recent_Posts');
  unregister_widget('WP_Widget_Recent_Comments');
  unregister_widget('WP_Widget_RSS');
  unregister_widget('WP_Widget_Tag_Cloud');
  // unregister_widget('WP_Nav_Menu_Widget');
  unregister_widget('Twenty_Eleven_Ephemera_Widget');
  wp_unregister_sidebar_widget('wpe_widget_powered_by');
}
add_action('widgets_init', 'nycc_unregister_default_widgets', 11);

// Disable WPEngine Widgets
function nycc_disable_wpe_widgets() {
  remove_meta_box('wpe_dify_news_feed', 'dashboard', 'normal');
}
add_action( 'admin_init', 'nycc_disable_wpe_widgets', 9999 );

// Disable the WP User Avatar widget
remove_action('widgets_init', 'wpua_widgets_init');
