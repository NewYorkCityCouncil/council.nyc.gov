<?php get_header(); ?>
<style>
  .image-header::before {
    /* background-image: url("<#?php the_post_thumbnail_url( 'small' ); ?>"); */
    background-image: url('https://council.nyc.gov/media/wp-content/uploads/sites/77/2019/11/Photo-Portada.jpeg');
  }
  /* small retina */
  @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
    .image-header::before {
      /* background-image: url("<#?php the_post_thumbnail_url( 'medium' ); ?>"); */
      background-image: url('https://council.nyc.gov/media/wp-content/uploads/sites/77/2019/11/Photo-Portada.jpeg');
    }
  }
  /* medium */
  @media only screen and (min-width: 40.0625em) {
    .image-header::before {
      /* background-image: url("<#?php the_post_thumbnail_url( 'medium' ); ?>"); */
      background-image: url('https://council.nyc.gov/media/wp-content/uploads/sites/77/2019/11/Photo-Portada.jpeg');
    }
  }
  /* medium retina */
  @media only screen and (min-width: 40.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 40.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min-resolution: 192dpi), only screen and (min-width: 40.0625em) and (min-resolution: 2dppx) {
    .image-header::before {
      /* background-image: url("<#?php the_post_thumbnail_url( 'large' ); ?>"); */
      background-image: url('https://council.nyc.gov/media/wp-content/uploads/sites/77/2019/11/Photo-Portada.jpeg');
    }
  }
  /* large */
  @media only screen and (min-width: 64.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 64.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 64.0625em) and (min-resolution: 192dpi), only screen and (min-width: 64.0625em) and (min-resolution: 2dppx) {
    .image-header::before {
      /* background-image: url("<#?php the_post_thumbnail_url( 'xlarge' ); ?>"); */
      background-image: url('https://council.nyc.gov/media/wp-content/uploads/sites/77/2019/11/Photo-Portada.jpeg');
    }
  }
</style>
<div class="image-header">
  <div class="page-header image-overlay-large">
    <div class="row">
      <div class="columns clearfix">
        <h1 class="image-overlay-text header-xxlarge">Media Unit</h1>
        <p class="image-overlay-text header-medium sans-serif">Photo, Video, and lab of visual content at the New York City Council</p>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="columns medium-8">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
        <div class="post-header">
          <h2 class="header-large"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
          <p><em><?php echo get_the_date( 'l, F j, Y' ); ?></em></p>
        </div>
      </article>
      <hr/>
    <?php endwhile; else : ?>
      <p  role="status" aria-live="polite"><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
    <?php endif; ?>
  </div>
  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
