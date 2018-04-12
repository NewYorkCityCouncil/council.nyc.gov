<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8 large-9 xxlarge-8">

      <?php
      global $wp_query;
      $args = array_merge( $wp_query->query, array( 'post_type' => 'nycc_pb_ballot_item' ) );
      query_posts( $args );

      $term = $wp_query->get_queried_object();
      $cycleID = $term->name;

      if ( have_posts() ) :
      ?>

      <?php if( is_tax() ) {
          global $wp_query;
      }  ?>

      <header class="page-header">
        <h1 class="header-xxlarge">Participatory Budgeting <small>Cycle <?php echo $cycleID; ?></small></h1>
        <p class="header-medium subheader sans-serif">
          <?php switch_to_blog(1); ?>
          <a href="<?php echo esc_url( home_url( '/pb/', 'http' ) ); ?>">Learn more about PBNYC</a>
          <?php restore_current_blog(); ?>
        </p>
        <!-- DELETE THIS AFTER SUNDAY -->
        <?php
          if ($cycleID == 7)
          echo '<span><a class="button" style="color:white;" href="https://pbnyc2018.d21.me/">Vote Now!</a></span>';
        ?>
        <!-- DELETE THIS AFTER SUNDAY -->
        <hr>
      </header>

      <?php
      $args = array(
        'post_type'  => 'nycc_pb_ballot_item',
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'posts_per_page' => '-1',
        'tax_query' => array(
          array(
            'taxonomy' => 'pbcycle',
            'field'    => 'name',
            'terms'    => $cycleID,
          ),
        ),
        'meta_query' => array(
          array(
            'key'     => 'pb_ballot_item_winner',
            'value'   => 'yes',
            'compare' => 'IN',
          ),
        ),
      );
      $winners = new WP_Query( $args );
      if ( $winners->have_posts() ) {
        $winner_exists = 'yes';
        echo '<h3 class="header-xlarge">Winning Projects:</h3>';
        while ( $winners->have_posts() ) {
          $winners->the_post();
          ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class('no-border'); ?>>
            <span class="float-right no-break">
              <?php
              $tags = get_the_terms( get_the_ID(), 'pbtags' );
              if ( $tags && ! is_wp_error( $tags ) ) :
                  foreach ( $tags as $tag ) {
                    echo '<span class="label">' . $tag->name . '</span>';
                  }
              endif; ?><span class="label success"><strong>Funded</strong></span>
            </span>
            <h5 class="header-medium"><?php echo get_the_title(); ?></h5>
            <?php the_content(); ?>
          </article>
          <?php
        }
        echo '';
      }
      wp_reset_postdata();

      if ( isset($winner_exists) && $winner_exists == 'yes') {

        $args = array(
          'post_type'  => 'nycc_pb_ballot_item',
          'orderby'    => 'menu_order',
          'order'      => 'ASC',
          'posts_per_page' => '-1',
          'tax_query' => array(
            array(
              'taxonomy' => 'pbcycle',
              'field'    => 'name',
              'terms'    => $cycleID,
            ),
          ),
          'meta_query' => array(
            array(
              'key'     => 'pb_ballot_item_winner',
              'value'   => 'no',
              'compare' => 'IN',
            ),
          ),
        );
        $losers = new WP_Query( $args );
        if ( $losers->have_posts() ) {
          echo '<hr><h3 class="header-xlarge">The following projects were not funded:</h3>';
          while ( $losers->have_posts() ) {
            $losers->the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('no-border'); ?>>
              <?php
              $tags = get_the_terms( get_the_ID(), 'pbtags' );
              if ( $tags && ! is_wp_error( $tags ) ) :
                  foreach ( $tags as $tag ) {
                    echo '<span class="label float-right">' . $tag->name . '</span>';
                  }
              endif; ?>
              <h5 class="header-medium"><?php echo get_the_title(); ?></h5>
              <?php the_content(); ?>
            </article>
            <?php
          }
          echo '';
        }
        wp_reset_postdata();

      } else {
      ?>

      <div class="row">
        <div class="columns large-6">

          <h3 class="header-large">What's on the ballot?</h3>

          <ul class="accordion" data-accordion data-allow-all-closed="true">
            <?php
            global $wp_query;
            $args = array_merge( $wp_query->query, array( 'posts_per_page' => -1 ) );
            query_posts( $args );
            while ( have_posts() ) : the_post();
            ?>
            <li class="accordion-item" data-accordion-item>
              <a href="#" class="accordion-title"><strong><?php the_title(); ?></strong></a>
              <div class="accordion-content text-small" data-tab-content>
                <?php the_content(); ?>
              </div>
            </li>
            <?php endwhile; ?>
          </ul>

        </div>
        <div class="columns large-6">

          <h3 class="header-large">Where do I vote?</h3>

          <?php
          $ballot_items = new WP_Query('post_type=nycc_pb_vote_site&orderby=menu_order&order=ASC&posts_per_page=-1');
          if ( $ballot_items->have_posts() ) {
            echo '<ul class="accordion" data-accordion data-allow-all-closed="true">';

              while ( $ballot_items->have_posts() ) {
                $ballot_items->the_post();
                ?>
                <li class="accordion-item" data-accordion-item>
                  <a href="#" class="accordion-title"><strong><?php the_title(); ?></strong></a>
                  <div class="accordion-content text-small" data-tab-content>
                    <?php the_content(); ?>
                  </div>
                </li>
                <?php
              }

            echo '</ul>';
          } else {
            echo '<p>Sorry. There are no vote sites yet. Voting dates and locations will be posted soon.</p>';
          }
          wp_reset_postdata();
          ?>

        </div>
      </div>

      <?php } ?>

      <?php else : ?>
        <h1 class="header-xxlarge">Participatory Budgeting <small>Cycle <?php echo $cycleID; ?></small></h1>
        <?php

        switch_to_blog(1);
          $pbsite = get_option('pb_site_id');
        restore_current_blog();

        switch_to_blog($pbsite);
          echo get_option('pb_placeholder');
        restore_current_blog();

        ?>
      <?php endif; ?>

      <?php wp_reset_query(); ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
