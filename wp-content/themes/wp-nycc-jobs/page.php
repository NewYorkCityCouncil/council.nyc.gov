<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
          <hr>
        </header>

        <section class="page-content">
          <?php the_content(); ?>
        </section>

      </article>

      <?php endwhile; endif; ?>

    </div>

    <div class="sidebar columns medium-4">
      <?php get_sidebar(); ?>
    </div>

  </div>

<?php get_footer(); ?>
