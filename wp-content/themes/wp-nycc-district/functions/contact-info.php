<?php
// District Contact Info widget
class nycc_district_contact_widget extends WP_Widget {
    function nycc_district_contact_widget() {
        $widget_ops = array(
            'classname' => 'nycc_district_contact_widget',
            'description' => 'Contact Information'
        );
        $this->WP_Widget(
            'nycc_district_contact_widget',
            'Contact Information',
            $widget_ops
        );
    }
    // widget sidebar output
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        echo $before_widget;

        $district_contact = get_option('council_district_contact');
        $legislative_contact = get_option('council_legislative_contact');
        $email = get_option('council_district_email');
        if ( $district_contact || $legislative_contact ) {
            echo '<div class="callout">';
            if ( $district_contact ) { ?><h4 class="widget-title">District Office</h4><p class="text-small"><?php echo nl2br( $district_contact ); ?></p><?php }
            if ( $legislative_contact ) { ?><h4 class="widget-title">Legislative Office</h4><p class="text-small"><?php echo nl2br( $legislative_contact ); ?></p><?php }
            echo '</div>';
        }
        if ( $email ) { ?><a href="mailto:<?php echo $email; ?>" class="button secondary expanded dashicons-before dashicons-email-alt">&nbsp;Email</a><?php }

        echo $after_widget;
    }
} // end class nycc_district_contact_widget
add_action(
    'widgets_init',
    create_function('','return register_widget(nycc_district_contact_widget);')
);
