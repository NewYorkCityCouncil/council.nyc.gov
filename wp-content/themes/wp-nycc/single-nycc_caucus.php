<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <div class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </div>

        <hr>

        <div class="row">
          <div class="columns medium-8">
            <?php the_content(); ?>
          </div>
          <div class="columns medium-4">
            <p><strong>The following Council Members participate in this caucus:</strong></p>
            <ul id="caucus-members">

              <?php

              // Get all the pages that use the District template
              $args = array(
                'post_type' => 'page',
                'post_status' => 'publish',
                'orderby'    => 'menu_order',
                'order'      => 'ASC',
                'posts_per_page' => '-1',
                'meta_query' => array(
                    array(
                        'key' => '_wp_page_template',
                        'value' => 'page-district.php',
                    )
                )
              );
              $list_districts = new WP_Query( $args );

              // Loop through the District pages
              if ( $list_districts->have_posts() ) {
                // ----- Caucus Speaker ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'speaker' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Speaker)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Deputy Speaker ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'deputy_speaker' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Deputy Speaker)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Majority Whip ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'majority_whip' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Majority Whip)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Chair ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Chair)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Co-Chair ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'co_chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Co-Chair)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Vice Chair ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'vice_chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Vice Chair)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Vice Co-Chair ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'vice_co_chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Vice Co-Chair)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                 // ----- Caucus Acting Co-Chair ----- //
                 while ( $list_districts->have_posts() ) : $list_districts->the_post();
                 $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                 if ($current_member_site_ID) {
                   switch_to_blog($current_member_site_ID);
                     $number = get_blog_option($current_member_site_ID,'council_district_number');
                     $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                     $cm_number = 'council_member_' . $number;
                   restore_current_blog();
                   wp_reset_postdata();
                 }
                 $status = get_post_meta($post->ID, $cm_number, true);
                 if ( $status == 'acting_co_chair' ) {
                   echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Acting Co-Chair)</small></li>';
                 }
                 endwhile;

                 $list_districts->rewind_posts();

                // ----- Caucus Secretary ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'secretary' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Secretary)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Treasurer ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'treasurer' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Treasurer)</small></li>';
                }
                endwhile;
                $list_districts->rewind_posts();

                // ----- Caucus Member ----- //
                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'member' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a></li>';
                }
                endwhile;

              }

              wp_reset_postdata();
              ?>

            </ul>
          </div>
        </div>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
