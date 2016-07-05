<?php get_header(); ?>

    <?php if ( have_posts() ) : ?>

      <header class="page-header">
        <h1 class="header-xlarge">Participatory Budgeting in District&nbsp;<?php echo get_option('council_district_number'); ?></h1>
        <p class="header-medium subheader sans-serif">
          To receive text alerts about when and where you can vote, text <strong>PBNYC</strong> to&nbsp;<strong>212-676-8384</strong>.
          <?php
            switch_to_blog(1);
            $pbpage = get_page_by_path( 'pb' );
            if ( $pbpage ) {
              echo '<small><a href="' . get_permalink( $pbpage ) . '" class="">Learn more about PBNYC</a>.</small>';
            }
            restore_current_blog();
          ?>
        </p>
        <hr>
      </header>

      <?php
      $args = array(
        'post_type'  => 'nycc_pb_ballot_item',
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'posts_per_page' => '-1',
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

      if ($winner_exists == 'yes') {

        $args = array(
          'post_type'  => 'nycc_pb_ballot_item',
          'orderby'    => 'menu_order',
          'order'      => 'ASC',
          'posts_per_page' => '-1',
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
            <?php while ( have_posts() ) : the_post(); ?>
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
          }
          wp_reset_postdata();
          ?>

        </div>
      </div>

      <?php } ?>

    <?php else : ?>
      <h1 class="post-title">Participatory budgeting is not currently happening in District&nbsp;<?php echo get_option('council_district_number'); ?>.</h1>
    <?php endif; ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
