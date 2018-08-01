<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">
      
      <!-- <header>
        <h1 class="header-small"><#?php the_archive_title();?></h1>
        <#?php the_archive_description('<div class="taxonomy-description">', '</div>');?>
        <hr>
      </header> -->
      <h1 class="header-small">Video Archive</h1>
      <iframe width="100%" height="350" src="https://www.youtube.com/embed/videoseries?list=PLY7w3pxk65yeWAw3eblIme615y32M3Q03" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
      <hr>

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
