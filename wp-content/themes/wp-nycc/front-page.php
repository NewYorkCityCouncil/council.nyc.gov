<?php get_header(); ?>

<div class="row homepage-carousel-container">
  <div class="columns" style="padding: 0;">
    <?php echo do_shortcode('[recent_post_slider design="design-4" limit="5" show_category_name="false" post_type="nycc_feature" dots="true" show_author="false" speed="500" media_size="full"]'); ?>
  </div>
</div>
<div class="row view-featured-container">
  <div class="columns">
    <a class="button" style="margin: 0" href="/past-featured-content/">View Past Featured Content</a>
  </div>
</div>

<?php include 'hearings.php';?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>> <?php the_content(); ?> </article>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
