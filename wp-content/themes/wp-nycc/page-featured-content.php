<?php /* Template Name: Past Featured Content */ ?>
<?php get_header(); ?>

<div class="row">
  <div class="columns medium-8">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
        <div class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <!-- <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?> -->
        </div>

        <hr>

        <div class="row">
          <div class="columns">
            <section class="page-content">
              <?php the_content(); ?>
              <?php wp_link_pages(); ?>
            </section>
            <ul class="row block-grid" style="list-style: none;">
              <?php
                $list_features = new WP_Query('post_type=nycc_feature&orderby=menu_order&order=ASC&offset=1');
                if ( $list_features->have_posts() ) {
                  while ( $list_features->have_posts() ) {
                    $list_features->the_post(); 
              ?>
              <li class="columns" id="feature-<?php the_ID(); ?>">
                <?php $feature_link_url = get_post_meta($post->ID, 'feature_link_url', true); ?>
                <a href="<?php echo $feature_link_url; ?>">
                  <div class="image-header fit-container">
                    <div class="image-overlay">
                      <p class="image-overlay-text header-sm sans-serif"><?php echo get_the_date(); ?></p>
                      <h3 class="image-overlay-text header-xlarge sans-serif"><?php the_title(); ?></h3>
                    </div>
                  </div>
                </a>
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
              </li>
              <?php
                  }
                }
                wp_reset_postdata();
              ?>
            </ul>
          </div>
        </div>
      </article>
    <?php endwhile; endif; ?>
  </div>
  <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
