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
        $contact_form = get_option('council_district_contact_form');
        $subscribe_form = get_option('council_district_subscribe_form');
        if ( $district_contact || $legislative_contact ) {
            echo '<div class="callout">';
            if ( $district_contact ) { ?><h4 class="widget-title">District Office</h4><p class="text-small"><?php echo nl2br( $district_contact ); ?></p><?php }
            if ( $legislative_contact ) { ?><h4 class="widget-title">Legislative Office</h4><p class="text-small"><?php echo nl2br( $legislative_contact ); ?></p><?php }
        }
        if ( $email ) { ?><a href="mailto:<?php echo $email; ?>" class="button secondary expanded dashicons-before dashicons-email-alt">&nbsp;Send&nbsp;Email</a><?php }
        if ( $contact_form ) {
            ?>
            <div class="reveal" id="contact_form" data-reveal>
              <h4 class="header-small">Contact <?php echo get_option('council_member_name'); ?></h4>
              <?php echo $contact_form; ?>
              <button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <a data-open="contact_form" class="button secondary expanded dashicons-before dashicons-admin-comments">&nbsp;Send&nbsp;Message</a>
            <?php
        }
        if ( $subscribe_form ) {
            ?>
            <div class="reveal" id="subscribe_form" data-reveal>
              <h4 class="header-small">Subscribe to<?php echo get_option('council_member_name'); ?> updates</h4>
              <?php echo $subscribe_form; ?>
              <button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <a data-open="subscribe_form" class="button secondary expanded dashicons-before dashicons-email-alt">&nbsp;Subscribe</a>
            <?php
        }
        if ( $district_contact || $legislative_contact ) {
            echo '</div>';
        }

        echo $after_widget;
    }
} // end class nycc_district_contact_widget
add_action(
    'widgets_init',
    create_function('','return register_widget(nycc_district_contact_widget);')
);
