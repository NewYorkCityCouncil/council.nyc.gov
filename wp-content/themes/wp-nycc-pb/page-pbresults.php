<?php /* Template Name: Cycle Results */ ?>

<?php get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <div class="row">

    <div class="columns">
      <div class="page-header">
        <h1 class="header-xxlarge">Participatory Budgeting</h1>
        <div class="header-menu"><?php nycc_pb_nav(); ?></div>
        <hr>
      </div>

      <?php the_content(); ?>

      <h2 class="header-xxlarge">Winning Projects</h2>
      <br>
      <?php
      $current_pb_cycle = get_post_custom_values( 'current_pb_cycle' )[0];
      $sites = get_sites();
      foreach ( $sites as $site ) {

        $ID = $site->blog_id;
        switch_to_blog($ID);

        $number = get_blog_option($ID,'council_district_number');
        $name = get_blog_option($ID,'council_member_name');
        $thumbnail = get_blog_option($ID,'council_member_thumbnail' );
        $borough = get_blog_option($ID,'council_district_borough');

        $cycle = term_exists($current_pb_cycle,'pbcycle');
        if ( $number && $cycle !== 0 && $cycle !== null ) {
          $winner_args = array(
            'post_type'  => 'nycc_pb_ballot_item',
            'orderby'    => 'menu_order',
            'order'      => 'ASC',
            'posts_per_page' => '-1',
            'tax_query' => array(
              array(
                'taxonomy' => 'pbcycle',
                'field'    => 'slug',
                'terms'    => $current_pb_cycle,
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
          $loser_args = array(
            'post_type'  => 'nycc_pb_ballot_item',
            'orderby'    => 'menu_order',
            'order'      => 'ASC',
            'posts_per_page' => '-1',
            'tax_query' => array(
              array(
                'taxonomy' => 'pbcycle',
                'field'    => 'slug',
                'terms'    => $current_pb_cycle,
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
          $winners = new WP_Query( $winner_args );
          $losers = new WP_Query( $loser_args );
          if ( $winners->have_posts() ) {
            ?>
            <article class="row hentry">
              <div class="columns large-12">
                <!-- <div class="media-object"> -->
                  <!-- <div class="media-object-section">
                    <div class="thumbnail"><a href="<#?php echo get_blogaddress_by_id($ID); ?>pb/<#?php echo $current_pb_cycle;?>/"><img alt="Headshot of <#?php echo $name; ?>" style="max-width:80px;" src= "<#?php echo $thumbnail; ?>"></a></div>
                  </div> -->
                  <!-- <div class="media-object-section"> -->
                    <h3 class="header-xlarge">
                      District <?php echo $number; ?>
                      <br>
                      <small><?php echo $name; ?></small>
                    </h3>
                  <!-- </div> -->
                <!-- </div> -->
              </div>
              <div class="columns large-6">
                <h4 class="header-medium">Funded Projects</h4>
                <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                  <?php
                  while ( $winners->have_posts() ) {
                    $winners->the_post();
                    ?>
                    <li class="accordion-item" data-accordion-item>
                      <a style="font-size: 1.25em; margin-bottom: 0px;" class="accordion-title header-medium"><?php echo get_the_title(); ?></a>
                      <div class="accordion-content" data-tab-content>
                        <p class="no-break">
                          <?php
                          $tags = get_the_terms( get_the_ID(), 'pbtags' );
                          if ( $tags && ! is_wp_error( $tags ) ) :
                              foreach ( $tags as $tag ) {
                                echo '<span style="margin-right: 1px; text-transform: capitalize;" class="label primary">' . $tag->name . '</span>';
                              }
                          endif; ?>
                        </p>
                        <?php the_content(); ?>
                      </div>
                    </li>
                  <?php } ?>
                </ul>
              </div>
              <div class="columns large-6">
                <h4 class="header-medium">Other Projects</h4>
                <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                  <?php
                  while ( $losers->have_posts() ) {
                    $losers->the_post();
                  ?>
                    <li class="accordion-item" data-accordion-item>
                      <a style="font-size: 1.25em; margin-bottom: 0px;" class="accordion-title header-medium"><?php echo get_the_title(); ?></a>
                      <div class="accordion-content" data-tab-content>
                        <p class="no-break">
                          <?php
                          $tags = get_the_terms( get_the_ID(), 'pbtags' );
                          if ( $tags && ! is_wp_error( $tags ) ) :
                            foreach ( $tags as $tag ) {
                              echo '<span style="margin-right: 1px; text-transform: capitalize;" class="label primary">' . $tag->name . '</span>';
                            }
                          endif; ?>
                        </p>
                        <?php the_content(); ?>
                      </div>
                    </li>
                  <?php } ?>
                </ul>
              </div>
            </article>
            <?php
          }
        }

        restore_current_blog();

      }
      ?>

    </div>
  </div>

  <?php endwhile; endif; ?>
<style>.label{}</style>
<?php get_footer(); ?>
