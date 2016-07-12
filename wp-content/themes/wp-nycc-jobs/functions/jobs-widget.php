<?php
// Jobs List Widget
class nycc_jobs_list_widget extends WP_Widget {
    function nycc_jobs_list_widget() {
        $widget_ops = array(
            'classname' => 'nycc_jobs_list_widget',
            'description' => 'Jobs List'
        );
        $this->WP_Widget(
            'nycc_jobs_list_widget',
            'Jobs List',
            $widget_ops
        );
    }
    // widget sidebar output
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        echo $before_widget;

        // Get all the divisions
        $division_terms = get_terms( 'job_division' );

        // Loop through all the divisions
        foreach ( $division_terms as $term ) {
            // Show a list of page links for the division
            $divisions_query = new WP_Query( array(
                'post_type' => 'page',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'job_division',
                        'field' => 'slug',
                        'terms' => array( $term->slug ),
                        'operator' => 'IN'
                    )
                )
            ) );
            ?>
            <h4 class="widget-title"><?php echo $term->name; ?></h4>
            <ul>
              <?php
              if ( $divisions_query->have_posts() ) : while ( $divisions_query->have_posts() ) : $divisions_query->the_post(); ?>
                <li><strong><a href="<?php echo the_permalink(); ?>"><?php echo the_title(); ?></a></strong></li>
              <?php endwhile; endif; ?>
            </ul>
            <?php
            // Reset the query (for good measure)
            $divisions_query = null;
            wp_reset_postdata();
        }

        echo $after_widget;
    }
} // end class nycc_jobs_list_widget
add_action(
    'widgets_init',
    create_function('','return register_widget(nycc_jobs_list_widget);')
);
