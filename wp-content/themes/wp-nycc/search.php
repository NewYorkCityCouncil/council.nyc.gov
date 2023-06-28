<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <div>
        <h1 class="header-small"><small>Search Results for: </small><em><?php echo esc_attr(get_search_query()); ?></em></h1>
        <hr>
      </div>

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'loop', 'archive' ); ?>

      <?php endwhile; else : ?>
        <p  role="status" aria-live="polite"><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
      <?php endif; ?>

      <?php nycc_page_navi(); ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
