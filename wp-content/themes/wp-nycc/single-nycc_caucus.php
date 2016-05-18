<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </header>

        <hr>

        <div class="row">
          <div class="columns medium-8">
            <?php the_content(); ?>
          </div>
          <div class="columns medium-4">
            <p><strong>The following Council Members serve on this caucus:</strong></p>
            <ul>

              <?php

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'chair' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a> <small>(Chair)</small></li>';
                      }
                  }
              }

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'co_chair' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a> <small>(Co-Chair)</small></li>';
                      }
                  }
              }

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'vice_chair' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a> <small>(Vice Chair)</small></li>';
                      }
                  }
              }

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'vice_co_chair' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a> <small>(Vice Co-Chair)</small></li>';
                      }
                  }
              }

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'secretary' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a> <small>(Secretary)</small></li>';
                      }
                  }
              }

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'treasurer' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a> <small>(Treasurer)</small></li>';
                      }
                  }
              }

              $sites = wp_get_sites();
              foreach ($sites as $site) {
                  $ID = $site['blog_id'];
                  $number = get_blog_option($ID,council_district_number);
                  if ( $number ) {
                      $cm_number = 'council_member_' . $number;
                      $status = get_post_meta($post->ID, $cm_number, true);
                      if ( $status == 'member' ) {
                          echo '<li><a href="' . get_site_url($ID) . '"><strong>' . get_blog_option($ID,council_member_name) . '</strong></a></li>';
                      }
                  }
              }

              ?>

            </ul>
          </div>
        </div>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
