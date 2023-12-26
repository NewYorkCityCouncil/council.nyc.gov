<?php get_header(); ?>
    <div class="row">
        <div class="columns">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
                $today = date("Y-m-d");
                $event_id = get_the_ID();
                $ced_event_link = get_post_meta($event_id, 'ced_event_link', true);
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
            <article id="post-<?php $event_id ?>" <?php post_class(''); ?>>
                <div class="page-header">
                    <h1 class="header-xxlarge"><?php the_title(); ?></h1>
                </div>
                <hr>
                <div class="row align-bottom">
                    <p class="columns medium-4 medium-push-8 medium-text-right">
                        <?php if ($today > $ced_event_date){ ?>
                            <strong style="text-decoration: underline;"><a href="/events/past-events">Return to past events</a></strong>
                        <?php } else { ?>
                            <strong style="text-decoration: underline;"><a href="/events">Return to upcoming events</a></strong>
                        <?php } ?>
                    </p>
                    <h2 class="columns medium-8 medium-pull-4">Event Information</h2>
                </div>
                <div class="row">
                    <div class="columns large-8">
                        <table>
                            <tr>
                                <td style="font-weight: bold;">Date</td>
                                <td><?php echo $parsed_full_date ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Time</td>
                                <td><?php echo $parsed_time_start ?> (Doors open at <?php echo $parsed_time_doors ?>)</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Location</td>
                                <td>
                                    <?php echo $ced_event_room ?><br/>
                                    <?php echo $ced_event_location?>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Invitation(s)</td>
                                <td>
                                    <ul style="margin-bottom: 0px;">
                                        <li><a href="<?php echo $ced_event_invite_eng ?>">English</a></li>
                                        <?php if ($ced_event_invite_ben){ ?> <li><a href="<?php echo $ced_event_invite_ben?>">Bengali</a></li> <?php } ?>
                                        <?php if ($ced_event_invite_chi){ ?> <li><a href="<?php echo $ced_event_invite_chi?>">Chinese</a></li> <?php } ?>
                                        <?php if ($ced_event_invite_cre){ ?> <li><a href="<?php echo $ced_event_invite_cre?>">Creole</a></li> <?php } ?>
                                        <?php if ($ced_event_invite_spa){ ?> <li><a href="<?php echo $ced_event_invite_spa?>">Spanish</a></li> <?php } ?>
                                    </ul>
                                </td>
                            </tr>
                            <?php if ($today < $ced_event_date){ ?>
                                <tr><td colspan="2"><a href="<?php echo $ced_event_link ?>" target="_blank" style="font-weight: bold;">RSVP FOR THIS EVENT</a></td></tr>
                            <?php } ?>
                        </table>
                        <?php the_content(); ?>
                    </div>
                    <div class="columns large-4 show-for-large">
                        <a href="<?php echo $ced_event_invite_eng ?>"><img style="margin-bottom: 1rem;" src="<?php echo get_the_post_thumbnail_url() ?>" alt="<?php echo the_title(); ?> Flyer"></a>
                    </div>
                </div>
                <div class="row">
                    <div class='columns small-12'>
                        <p>
                            <small>
                                The New York City Council is committed to ensuring its events are accessible to all members of the public.<br/>
                                For general questions about accessibility, or about specific accommodations, you may contact us at <a href='mailto:accessibility@council.nyc.gov'>accessibility@council.nyc.gov.</a>
                            </small>
                        </p>
                    </div>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </div>
  </div>

<?php get_footer(); ?>
