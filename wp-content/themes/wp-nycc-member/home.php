<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8 large-9 xxlarge-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'loop', 'archive' ); ?>

      <?php endwhile; else : ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
      <?php endif; ?>

      <?php nycc_page_navi(); ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
