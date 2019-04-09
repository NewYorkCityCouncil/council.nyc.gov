<?php /* Template Name: Cycle Results */ ?>

<?php get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <div class="row">

    <div class="columns">
      <header class="page-header">
        <h1 class="header-xxlarge">Participatory Budgeting</h1>
        <div class="header-menu"><?php nycc_pb_nav(); ?></div>
        <hr>
      </header>

      <?php the_content(); ?>

      <h3 class="header-xxlarge">Winning Projects</h3>
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
          $args = array(
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
          $winners = new WP_Query( $args );
          if ( $winners->have_posts() ) {
            ?>
            <article class="row hentry">
              <div class="columns large-4">
                <div class="media-object">
                  <div class="media-object-section">
                    <div class="thumbnail"><a href="<?php echo get_blogaddress_by_id($ID); ?>pb/<?php echo $current_pb_cycle;?>/"><img alt="Headshot of <?php echo $name; ?>" style="max-width:80px;" src= "<?php echo $thumbnail; ?>"></a></div>
                  </div>
                  <div class="media-object-section">
                    <h4 class="header-xlarge">
                      <a href="<?php echo get_blogaddress_by_id($ID); ?>pb/<?php echo $current_pb_cycle;?>/">
                        District <?php echo $number; ?>
                        <br>
                        <small><?php echo $name; ?></small>
                      </a>
                    </h4>
                  </div>
                </div>
              </div>
              <div class="columns large-8">
                <?php
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
                ?>
                <article class="hentry no-border">
                  <a class="button small" href="<?php echo get_blogaddress_by_id($ID); ?>pb/<?php echo $current_pb_cycle;?>/">See all projects</a>
                </article>
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

<?php get_footer(); ?>
