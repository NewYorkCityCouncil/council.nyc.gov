<?php /* Template Name: Page with Sidebar */ ?>

<?php get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <div class="row">
    <div class="columns medium-8">

      <header class="page-header">
        <h1 class="header-xxlarge"><?php the_title(); ?></h1>
        <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        <hr>
      </header>

      <?php the_content(); ?>

    </div>
    <div class="columns medium-4">

      <?php get_sidebar(); ?>

    </div>
  </div>

  <?php endwhile; endif; ?>

<?php get_footer(); ?>
