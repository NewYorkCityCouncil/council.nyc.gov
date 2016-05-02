<?php get_header(); ?>

  <div class="row">

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <header class="page-header columns">
      <h1 class="header-xxlarge"><?php the_title(); ?></h1>
      <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
      <hr>
    </header>

    <div class="page-content columns large-8">
      <?php the_content(); ?>
    </div>

    <?php endwhile; endif; ?>

    <div class="sidebar columns large-4">

      <?php
      $land_use_plan_event_title = get_post_meta($post->ID, 'land_use_plan_event_title', true);
      $land_use_plan_event_date = get_post_meta($post->ID, 'land_use_plan_event_date', true);
      $land_use_plan_event_time = get_post_meta($post->ID, 'land_use_plan_event_time', true);
      $land_use_plan_event_location = get_post_meta($post->ID, 'land_use_plan_event_location', true);
      $land_use_plan_event_map_link = get_post_meta($post->ID, 'land_use_plan_event_map_link', true);
      if ( $land_use_plan_event_title ) { ?>
      <div class="callout secondary widget">
        <h3 class="header-medium"><?php echo $land_use_plan_event_title; ?></h3>
        <p class="subheader text-medium"><strong><?php
          echo $land_use_plan_event_date;
          if ( $land_use_plan_event_time ) {
            echo ', <span class="no-break">' . date('g:i a', strtotime($land_use_plan_event_time)) . '</span>';
          }
          ?></strong></p>
        <p class="text-small"><?php
          echo $land_use_plan_event_location;
          if ( $land_use_plan_event_map_link ) {
            echo '&nbsp;<small><a href="' . $land_use_plan_event_map_link . '"><strong>MAP</strong></a></small>';
          }
        ?></p>
      </div>
      <?php }

      $subpages = new WP_Query('post_type=nycc_land_use_plan&orderby=menu_order&order=ASC&post_parent=' . $post->ID . '&posts_per_page=-1');
      if ( $subpages->have_posts() ) {
        while ( $subpages->have_posts() ) : $subpages->the_post();
          ?>
          <div class="callout">
            <a class="button expanded" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <p class="text-tiny"><?php echo get_the_excerpt(); ?></p>
            <?php
            $land_use_plan_event_title = get_post_meta(get_the_ID(), 'land_use_plan_event_title', true);
            $land_use_plan_event_date = get_post_meta(get_the_ID(), 'land_use_plan_event_date', true);
            $land_use_plan_event_time = get_post_meta(get_the_ID(), 'land_use_plan_event_time', true);
            $land_use_plan_event_location = get_post_meta(get_the_ID(), 'land_use_plan_event_location', true);
            $land_use_plan_event_map_link = get_post_meta(get_the_ID(), 'land_use_plan_event_map_link', true);
            if ( $land_use_plan_event_title ) { ?>
              <hr>
              <h3 class="header-tiny"><a href="<?php the_permalink(); ?>"><?php echo $land_use_plan_event_title; ?></a></h3>
              <p class="subheader text-small"><strong><?php
                echo $land_use_plan_event_date;
                if ( $land_use_plan_event_time ) {
                  echo ', <span class="no-break">' . date('g:i a', strtotime($land_use_plan_event_time)) . '</span>';
                }
                ?></strong></p>
            <?php }
            ?></div><?php
        endwhile;
        wp_reset_postdata();
      }

      $attachments = get_posts(array(
          'post_parent' => $post->ID,
          'post_type' => 'attachment',
          'posts_per_page' => -1,
          'orderby' => 'menu_order',
          'order' => 'ASC'

      ));
      if ( $attachments ) {
        ?><div class="widget">
          <ul class=""><?php
            foreach($attachments as $attachment) {
              echo '<li>';
              echo '<a href="' . wp_get_attachment_url($attachment->ID) . '">';
              echo get_the_title($attachment->ID);
              $mimetype = get_attachment_mime_type($attachment->ID);
              if ($mimetype) {
                echo ' <small>(' . $mimetype . ')</small>';
              }
              echo '</a>';
              $caption = get_post_field('post_excerpt', $attachment->ID);
              if ( $caption ) { echo '<div class="text-tiny">' . $caption . '</div>'; }
              echo '</li>';
            }
        ?></ul>
      </div><?php
      }
      ?>


    </div>

  </div>

<?php get_footer(); ?>
