<?php /* Template Name: Past Events*/ ?>
<?php get_header(); ?>
<?php if ( has_post_thumbnail() ) {
    get_template_part( 'img_header_style' );
?>
    <div class="image-header">
        <div class="page-header image-overlay-large">
            <div class="row">
                <div class="columns clearfix">
                    <h1 class="image-overlay-text header-xxlarge"><?php the_title(); ?></h1>
                    <?php if ( has_excerpt( $post->ID ) ) { ?><p class="image-overlay-text header-medium sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
  
    <div class="row">
        <div class="columns medium-12">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
                    <div class="page-header">
                        <h1><?php the_title(); ?></h1>
                        <hr>
                    </div>
                    <section class="page-content">
                        <div class='row'>
                            <div class='columns small-12'>
                                <p><strong style="text-decoration: underline;"><a id="back-link" href="/events">Click here to see our upcoming events</a></strong></p>
                            </div>
                        </div>
                        <br/>
                        <!-- get all active Events (that have not passed)
                        loop through and display each event -->
                        <?php
                            $today = date("Y-m-d");
                            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                            $args = array(
                                'meta_query' => array(
                                    array(
                                        'compare' => '<',
                                        'key' => 'ced_event_date',
                                        'type' => 'DATETIME',
                                        'value' => date("Y-m-d"),
                                    )
                                ),
                                'paged' => $paged,
                                'post_type' => 'nycc_ced_event',
                                'posts_per_page' => 5,
                                'order'     => 'DESC',
                                'orderby'   => 'ced_event_date',
                            );
                            $events = new WP_Query($args);
                            if ( $events->have_posts() ):
                                $months = [
                                    "1" => "January",
                                    "2" => "February",
                                    "3" => "March",
                                    "4" => "April",
                                    "5" => "May",
                                    "6" => "June",
                                    "7" => "July",
                                    "8" => "August",
                                    "9" => "September",
                                    "10" => "October",
                                    "11" => "November",
                                    "12" => "December",
                                ];
                                while ( $events->have_posts() ) : $events->the_post();
                                    $event_id = get_the_ID();
                                    $ced_event_title = get_post_meta($event_id, 'ced_event_title', true);
                                    $ced_event_code = get_post_meta($event_id, 'ced_event_code', true);
                                    $ced_event_date = get_post_meta($event_id, 'ced_event_date', true);
                                    $parsed_date_month = date("F",strtotime($ced_event_date));
                                    $parsed_date_day = date("j",strtotime($ced_event_date));
                                    $parsed_date_year = date("Y",strtotime($ced_event_date));
                                    $parsed_full_date = $parsed_full_date = $parsed_date_month . " " . $parsed_date_day . ", " . $parsed_date_year;
                                    $ced_event_time_doors = get_post_meta($event_id, 'ced_event_time_doors', true);
                                    $parsed_time_doors = date("g:i a",strtotime($ced_event_time_doors));
                                    $ced_event_time_start = get_post_meta($event_id, 'ced_event_time_start', true);
                                    $parsed_time_start = date("g:i a",strtotime($ced_event_time_start));
                                    $ced_event_room = get_post_meta($event_id, 'ced_event_room', true);
                                    $ced_event_location = get_post_meta($event_id, 'ced_event_location', true);
                                    $ced_event_description = get_post_meta($event_id, 'ced_event_description', true);
                                    $ced_event_invite_eng = get_post_meta($event_id, 'ced_event_invite_eng', true);
                                    $ced_event_invite_ben = get_post_meta($event_id, 'ced_event_invite_ben', true);
                                    $ced_event_invite_chi = get_post_meta($event_id, 'ced_event_invite_chi', true);
                                    $ced_event_invite_cre = get_post_meta($event_id, 'ced_event_invite_cre', true);
                                    $ced_event_invite_spa = get_post_meta($event_id, 'ced_event_invite_spa', true);
                        ?>
                                    <div class="row">
                                        <div class="event-date columns small-3">
                                            <div class="date">
                                                <?php echo $parsed_date_day ?><br/>
                                                <?php echo $parsed_date_month ?>
                                            </div>
                                        </div>
                                        <div class="event-details columns small-6">
                                            <h4><a href=<?php the_permalink(); ?>><?php echo $ced_event_title ?></a></h4>
                                            <strong><?php echo ($parsed_full_date . " at " . $ced_event_room) ?></strong><br/>
                                            <em>Event started at <?php echo $parsed_time_start ?></em><br/>
                                            <?php echo explode("@", $ced_event_location)[0] ?><br/>
                                            <small><?php echo explode("@", $ced_event_location)[1] ?></small>
                                        </div>
                                        <div class="event-image columns small-3">
                                            <a href=<?php the_permalink(); ?>>
                                                <img src="<?php echo get_the_post_thumbnail_url() ?>" alt="<?php echo $ced_event_title ?> Flyer">
                                            </a>
                                        </div>
                                    </div>
                                    <hr>
                        <?php 
                                endwhile; 
                                paginate_links();
                            else: 
                        ?>
                            <br/>
                            <h2 style='text-align:center;'>
                                Thank you for your interest in Council events.<br/>
                                We look forward to celebrating with you again soon.
                            </h2>
                        <?php endif; ?>
                    </section>

                </article>
                
            <?php 
                endwhile; 
                $past_events =  paginate_links(array(
                    'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                    'total' => $events->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'format' => '?paged=%#%',
                    'show_all'     => false,
                    'type'         => 'array',
                    'end_size'     => 2,
                    'mid_size'     => 1,
                    'prev_next' => true,
                    'prev_text' => sprintf('<i></i> %1$s', __('Newest', 'text-domain')),
                    'next_text' => sprintf('%1$s <i></i>', __('Oldest', 'text-domain')),
                    'add_args' => false,
                    'add_fragment' => '',
                ));
                if( $past_events ) {
                    echo '<nav class="page-navigation" style="border-top: none; margin-top: 0px; padding-top: 0px;"><ul class="pagination">';
                    foreach ( $past_events as $past_event ) {
                        echo "<li>$past_event</li>";
                    }
                    echo '</ul></nav>';
                }
                wp_reset_postdata(); 
                endif; 
            ?>
        </div>
    </div>
<?php get_footer(); ?>
