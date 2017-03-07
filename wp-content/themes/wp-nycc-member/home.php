<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8 large-9 xxlarge-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="post-header">
          <h2 class="header-large"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
        </header>

        <section class="post-content">
          <?php echo the_excerpt(); ?>
        </section>

      </article>

      <?php endwhile; endif; ?>

      <?php nycc_page_navi(); ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
