<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

          <header class="press-release-header">
            <h1 class="header-xlarge"><?php the_title(); ?></h1>
            <p class="byline"><?php the_time('F j, Y') ?></p>
          </header>

          <section class="post-content">
            <?php the_content(); ?>
          </section>

          <footer class="post-footer">
            <p><?php if( has_tag() ) { the_tags('Tagged '); } ?></p>
          </footer>

          <?php if( comments_open() ) { ?>
          <div class="post-comments">
            <?php comments_template(); ?>
          </div>
          <?php } ?>

        </article>

      <?php endwhile; endif; ?>

    </div>
    <div class="sidebar columns medium-4">
      <?php get_sidebar(); ?>
    </div>
  </div>

<?php get_footer(); ?>
