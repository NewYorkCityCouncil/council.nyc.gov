<?php /* Template Name: Initiatives List */ ?>

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

        <?php the_content(); ?>

        <?php
        $list_initiatives = new WP_Query('post_type=nycc_initiative&orderby=menu_order&order=ASC&posts_per_page=-1');
        if ( $list_initiatives->have_posts() ) {
          echo '<ul class="initiatives-list no-bullet">';

            while ( $list_initiatives->have_posts() ) {
              $list_initiatives->the_post();
              echo '<li>';
              ?>
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
                    <a href="<?php the_permalink(); ?>">
                      <div class="image-header"></div>
                    </a>
                  </div>
                  <div class="columns medium-6">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <p class="text-small"><?php echo get_the_excerpt(); ?></p>
                    <?php
                    $initiative_link_url = get_post_meta($post->ID, 'initiative_link_url', true);
                    $initiative_link_text = get_post_meta($post->ID, 'initiative_link_text', true);
                    if ( $initiative_link_url && $initiative_link_text ) {
                      echo '<a class="button" href="' . $initiative_link_url . '">' . $initiative_link_text . '</a>';
                    }
                    ?>
                  </div>
                </div>
              </div>
              <?php
              echo '</li>';
            }

          echo '</ul>';
        }
        wp_reset_postdata();
        ?>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
