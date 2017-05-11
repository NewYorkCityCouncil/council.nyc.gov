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


// Recent Plans widget
class Widget_Recent_Plans extends WP_Widget {
    public function __construct() {
        $widget_ops = array('classname' => 'nycc_recent_plans_widget', 'description' => __( "Recent Plans") );
        parent::__construct('recent-plans', __('Recent Plans'), $widget_ops);
        $this->alt_option_name = 'nycc_recent_plans_widget';

        add_action( 'save_post', array($this, 'flush_widget_cache') );
        add_action( 'deleted_post', array($this, 'flush_widget_cache') );
        add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    }

    public function widget($args, $instance) {
        $cache = array();
        if ( ! $this->is_preview() ) {
          $cache = wp_cache_get( 'nycc_recent_plans_widget', 'widget' );
        }

        if ( ! is_array( $cache ) ) {
          $cache = array();
        }

        if ( ! isset( $args['widget_id'] ) ) {
          $args['widget_id'] = $this->id;
        }

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
          echo $cache[ $args['widget_id'] ];
          return;
        }

        ob_start();

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Plans' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;

        $args = array(
          'post_type' => 'nycc_land_use_plan',
          'post_parent'=> 0,
          'posts_per_page'=> $number,
          'orderby' => 'menu_order post_date',
          'order'   => 'DESC',

        );
        $recent_plans_query = new WP_Query( $args );

        if ( $recent_plans_query->have_posts() ) {
          echo '<aside class="widget widget_nav_menu">';
          if ( $title ) {
            echo '<h4 class="widget-title">' . $title . '</h4>';
          }
          echo '<ul class="menu">';
          while ( $recent_plans_query->have_posts() ) {
            $recent_plans_query->the_post();
            echo '<li class="menu-item">';
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            echo '</li>';
          }
          echo '</ul>';
          echo '</aside>';
          wp_reset_postdata();
        }

       if ( ! $this->is_preview() ) {
          $cache[ $args['widget_id'] ] = ob_get_flush();
          wp_cache_set( 'nycc_recent_plans_widget', $cache, 'widget' );
       } else {
          ob_end_flush();
       }
    } // Widget function

    public function update( $new_instance, $old_instance ) {
       $instance = $old_instance;
       $instance['title'] = strip_tags($new_instance['title']);
       $instance['number'] = (int) $new_instance['number'];
       $this->flush_widget_cache();

       $alloptions = wp_cache_get( 'alloptions', 'options' );
       if ( isset($alloptions['widget_recent_entries']) )
          delete_option('widget_recent_entries');

       return $instance;
    }

    public function flush_widget_cache() {
       wp_cache_delete('nycc_recent_plans_widget', 'widget');
    }

    public function form( $instance ) {
       $title          = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
       $number         = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
       ?>

       <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
       <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

       <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of plans to show:' ); ?></label>
       <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

       <?php
    }

}

function load_nycc_recent_plans_widget() {
  register_widget( 'Widget_Recent_Plans' );
}
add_action( 'widgets_init', 'load_nycc_recent_plans_widget' );
