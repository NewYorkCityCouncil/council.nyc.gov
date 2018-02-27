<?php get_header(); ?>

  <div class="row">
    <div class="columns">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
          <?php the_content(); ?>
        </article>
      <?php endwhile; endif; ?>
      <hr>
    </div>
    <div class="columns medium-8">
      <div class="row small-up-1 large-up-2 block-grid">

      <?php
      $list_features = new WP_Query('post_type=nycc_feature&orderby=menu_order&order=ASC&posts_per_page=-1');
      if ( $list_features->have_posts() ) {
        while ( $list_features->have_posts() ) {
          $list_features->the_post(); ?>
          <style>
            #feature-<?php the_ID(); ?> .image-header {
              margin: 0;
            }
            #feature-<?php the_ID(); ?> .image-header::before {
              background-image: url("<?php the_post_thumbnail_url( 'small' ); ?>");
            }
            /* small retina */
            @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
              #feature-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'medium' ); ?>");
              }
            }
            /* medium */
            @media only screen and (min-width: 40.0625em) {
              #feature-<?php the_ID(); ?> .image-header::before {
                padding-bottom: 56.25%;
                background-image: url("<?php the_post_thumbnail_url( 'medium' ); ?>");
                margin: 0;
              }
            }
            /* medium retina */
            @media only screen and (min-width: 40.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 40.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min-resolution: 192dpi), only screen and (min-width: 40.0625em) and (min-resolution: 2dppx) {
              #feature-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'large' ); ?>");
              }
            }
          </style>
          <div class="columns" id="feature-<?php the_ID(); ?>">
            <?php $feature_link_url = get_post_meta($post->ID, 'feature_link_url', true); ?>
            <a href="<?php echo $feature_link_url; ?>">
              <div class="image-header fit-container">
                <div class="image-overlay">
                  <h4 class="image-overlay-text header-small sans-serif"><?php the_title(); ?></h4>
                </div>
              </div>
            </a>
          </div>
          <?php
        }

      }
      wp_reset_postdata();
      ?>

      </div>
    </div>

    <?php get_sidebar(); ?>

    <!-- New content -->
    <div class="columns medium-11 medium-centered">
      <hr>
      <div class="columns medium-5 speaker-council-twitter-feed">
        <a class="twitter-timeline" href="https://twitter.com/NYCSpeakerCoJo?ref_src=twsrc%5Etfw">Tweets by NYCSpeakerCoJo</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>
      <div class="columns medium-5 medium-offset-2 speaker-council-twitter-feed">
        <a class="twitter-timeline" href="https://twitter.com/NYCCouncil?ref_src=twsrc%5Etfw">Tweets by NYCCouncil</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>
    </div>

  </div>

<?php get_footer(); ?>
