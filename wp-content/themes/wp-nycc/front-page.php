<?php get_header(); ?>
<!-- Livestream Content -->
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>> <?php the_content(); ?> </article>
<?php endwhile; endif; ?>

<!-- Hearings Content -->
<?php include 'hearings.php';?>

<!-- Featured Content -->
<?php include 'featured-content.php';?>

<?php get_footer(); ?>