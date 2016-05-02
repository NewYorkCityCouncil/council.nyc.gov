<?php /* Template Name: Raw HTML, Full-width, No Header */ ?>

<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

    <?php the_content(); ?>
    <?php wp_link_pages(); ?>

  </article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
