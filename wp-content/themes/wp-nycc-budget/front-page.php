<?php get_header(); ?>

  <?php if ( has_post_thumbnail() ) {
    get_template_part( 'img_header_style' );
  } ?>
  <div class="image-header">
    <header class="page-header image-overlay-large">
      <div class="row">
        <div class="columns clearfix">
          <h1 class="image-overlay-text header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="image-overlay-text header-medium sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </div>
      </div>
    </header>
  </div>

  <div class="row">
    <div class="columns large-8">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
        <?php the_content(); ?>
      </article>
      <?php endwhile; endif; ?>
    </div>
    <div class="columns large-4">
      <?php if ( is_active_sidebar( 'frontpage-sidebar' ) ) : ?>
        <?php dynamic_sidebar( 'frontpage-sidebar' ); ?>
      <?php endif; ?>
      <?php if ( is_active_sidebar( 'posts-sidebar' ) ) : ?>
        <?php dynamic_sidebar( 'posts-sidebar' ); ?>
      <?php endif; ?>
    </div>
  </div>

<?php get_footer(); ?>
