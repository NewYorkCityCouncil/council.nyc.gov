<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <h1 class="header-xlarge">Press Releases</h1>

      <hr>

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

          <header class="press-release-header">
            <h2 class="header-large"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <p class="byline"><?php the_time('F j, Y') ?></p>
          </header>

          <section class="post-content text-small">
            <?php the_excerpt(); ?>
          </section>

        </article>

      <?php endwhile; else : ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
      <?php endif; ?>

      <?php get_template_part( 'navigation-posts' ); ?>

    </div>

    <div class="sidebar columns medium-4">
      <?php get_sidebar(); ?>
    </div>

  </div>

<?php get_footer(); ?>
