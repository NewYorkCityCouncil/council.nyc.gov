<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1><?php the_title(); ?></h1>
          <hr>
        </header>

        <section class="page-content">
          <?php the_content(); ?>
        </section>

      </article>

      <?php endwhile; endif; ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
