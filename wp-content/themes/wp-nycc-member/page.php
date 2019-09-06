<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8 large-9 xxlarge-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <div class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><h4 class="page-excerpt sans-serif"><?php echo get_the_excerpt(); ?></h4><?php } ?>
        </div>

        <section class="page-content">
          <?php the_content(); ?>
          <?php wp_link_pages(); ?>
        </section>

        <footer class="page-footer">
        </footer>

      </article>

      <?php endwhile; endif; ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
