<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <header>
        <h1 class="header-small"><?php the_archive_title();?></h1>
        <?php the_archive_description('<div class="taxonomy-description">', '</div>');?>
        <hr>
      </header>


      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'loop', 'video' ); ?>
      <?php endwhile; else : ?>
        <p class="columns"><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
      <?php endif; ?>

      <?php nycc_page_navi(); ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
