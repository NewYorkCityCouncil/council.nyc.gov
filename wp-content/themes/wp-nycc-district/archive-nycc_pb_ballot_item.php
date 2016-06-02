<?php get_header(); ?>

      <?php if ( have_posts() ) : ?>

      <header class="page-header">
        <h1 class="header-xlarge">Participatory Budgeting in District&nbsp;<?php echo get_option('council_district_number'); ?></h1>
        <p class="header-medium subheader sans-serif">
          To recieve text alerts about when and where you can vote, text <strong>PBNYC</strong> to&nbsp;<strong>212-676-8384</strong>.
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


      <div class="row">
        <div class="columns large-6">

          <h3>What's on the ballot?</h3>

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

          <h3>Where do I vote?</h3>

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

    <?php else : ?>
      <h1 class="post-title">Participatory budgeting is not currently happening in District&nbsp;<?php echo get_option('council_district_number'); ?>.</h1>
    <?php endif; ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
