<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
        </header>

        <section class="page-content">

          <style>
            #initiative-<?php the_ID(); ?> .image-header {
              margin: 0;
            }
            #initiative-<?php the_ID(); ?> .image-header::before {
              background-image: url("<?php the_post_thumbnail_url( 'small' ); ?>");
            }
            /* small retina */
            @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
              #initiative-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'medium' ); ?>");
              }
            }
            /* medium */
            @media only screen and (min-width: 40.0625em) {
              #initiative-<?php the_ID(); ?> .image-header::before {
                padding-bottom: 56.25%;
                background-image: url("<?php the_post_thumbnail_url( 'medium' ); ?>");
                margin: 0;
              }
            }
            /* medium retina */
            @media only screen and (min-width: 40.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 40.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min-resolution: 192dpi), only screen and (min-width: 40.0625em) and (min-resolution: 2dppx) {
              #initiative-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'large' ); ?>");
              }
            }
            /* large */
            @media only screen and (min-width: 64.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 64.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (min-resolution: 192dpi), only screen and (min-width: 64.0625em) and (min-resolution: 2dppx) {
              #initiative-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'xlarge' ); ?>");
              }
            }
          </style>
          <div id="initiative-<?php the_ID(); ?>">
            <div class="row">
              <div class="columns medium-6">
                <div class="image-header"></div>
              </div>
              <div class="columns medium-6">
                <p><?php echo get_the_excerpt(); ?></p>
                <?php
                $initiative_link_url = get_post_meta($post->ID, 'initiative_link_url', true);
                $initiative_link_text = get_post_meta($post->ID, 'initiative_link_text', true);
                if ( $initiative_link_url && $initiative_link_text ) {
                  echo '<a class="button large expanded" href="' . $initiative_link_url . '">' . $initiative_link_text . '</a>';
                }
                ?>
              </div>
              <div class="columns medium-12">
                <hr>
                <?php the_content(); ?>
              </div>
            </div>
          </div>

        </section>
      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
