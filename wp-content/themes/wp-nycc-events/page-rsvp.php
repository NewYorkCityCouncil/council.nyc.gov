<?php /* Template Name: RSVP */?>
<?php get_header(); ?>
<?php 
    // Get the RSVP event code
    $event_code = $_GET["event"];
    $args = array(
        'post_type' => 'nycc_ced_event',
        // 'meta_key' => 'nycc_ced_event_ced_event_link',
        // 'meta_value' => $event_code
        'meta_query' => array(
            array(
                'key'     => 'ced_event_link',
                'value'   => $event_code,
            )
        )   
    );
    $event = new WP_Query($args);
    if ( $event->have_posts() ) {
        while ( $event->have_posts() ) : $event->the_post();
            $event_id = get_the_ID();
            $ced_event_title = get_post_meta($event_id, 'ced_event_title', true);
            $ced_event_link = get_post_meta($event_id, 'ced_event_link', true);
            $ced_event_date = get_post_meta($event_id, 'ced_event_date', true);
            $parsed_date_month = date("F",strtotime($ced_event_date));
            $parsed_date_day = date("j",strtotime($ced_event_date));
            $parsed_date_year = date("Y",strtotime($ced_event_date));
            $parsed_full_date = $parsed_date_month . " " . $parsed_date_day . ", " . $parsed_date_year;
            $ced_event_time_doors = get_post_meta($event_id, 'ced_event_time_doors', true);
            $parsed_time_doors = date("g:i a",strtotime($ced_event_time_doors));
            $ced_event_time_start = get_post_meta($event_id, 'ced_event_time_start', true);
            $parsed_time_start = date("g:i a",strtotime($ced_event_time_start));
            $ced_event_room = get_post_meta($event_id, 'ced_event_room', true);
            $ced_event_location = get_post_meta($event_id, 'ced_event_location', true);
            $ced_event_description = get_post_meta($event_id, 'ced_event_description', true);
        endwhile;
    }
    wp_reset_postdata();
?>
<div class="row">
    <div class="columns">
        <h1 id="rsvp-event-title">RSVP</h1>
        <hr style="margin: 20px 0px 10px;">
        <p><strong style="text-decoration: underline;"><a id="back-link" href="/events">Return to Upcoming Events</a></strong></p>
    </div>
</div>
<?php echo do_shortcode('[contact-form-7 id="182" title="New CPT RSVP Form"]') ?>
<script>
    jQuery(document).ready(function(){
        const event_name = <?php echo json_encode($ced_event_title); ?>;
        const event_link = <?php echo json_encode($ced_event_link); ?>;
        const date_of_event = <?php echo json_encode($parsed_full_date); ?>;
        const doors_open = <?php echo json_encode($parsed_time_doors); ?>;
        const program_begins = <?php echo json_encode($parsed_time_start); ?>;
        const program_location = <?php echo json_encode($ced_event_location); ?>;
        if (event_code){
            jQuery("#event-name").html(`<option value="${event_code}">${event_name}</option>`);
            jQuery("#full-event-name").val(event_name);
            jQuery("#date-of-event").val(date_of_event);
            jQuery("#doors-open").val(doors_open);
            jQuery("#program-begins").val(program_begins);
            jQuery("#program-location").val(program_location);
        };
    })
</script>

<?php get_footer(); ?>
 