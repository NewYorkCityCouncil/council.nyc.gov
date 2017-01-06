<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <header>
        <h1 class="header-small"><?php the_archive_title();?></h1>
        <?php the_archive_description('<div class="taxonomy-description">', '</div>');?>
        <hr>
      </header>

      <div class="row">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'loop', 'video' ); ?>
      <?php endwhile; else : ?>
        <p class="columns"><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
      <?php endif; ?>

      </div>

      <?php nycc_page_navi(); ?>

    </div>

    <div class="sidebar columns medium-4">
      <?php get_sidebar(); ?>
    </div>

  </div>

<?php get_footer(); ?>
