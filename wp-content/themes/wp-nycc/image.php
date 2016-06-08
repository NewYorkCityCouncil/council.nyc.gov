<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

          <header>
            <h1 class="header-large"><?php the_title(); ?></h1>
            <?php the_excerpt(); ?>
          </header>
          <section class="post-content">
            <?php
            $image_attributes = wp_get_attachment_image_src( $post->ID, 'full' );
            echo '<a class="thumbnail" href="';
            echo $image_attributes[0];
            echo '">';
            ?>
            <img src="<?php echo wp_get_attachment_url() ?>">
            <?php
            echo '</a>';
            ?>
          </section>

          <footer class="post-footer">
            <p><?php if( has_tag() ) { ?><?php the_tags('Tagged '); } ?></p>
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
