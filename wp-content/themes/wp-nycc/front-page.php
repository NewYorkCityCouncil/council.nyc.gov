<?php get_header(); ?>
<!-- Featured Content START -->
  <!-- <#?php include 'featured-content.php';?> -->
  <div class="row homepage-carousel-container">
    <div class="columns" style="padding: 0;">
      <!-- <#?php echo do_shortcode('[recent_post_slider design="design-4" limit="5" show_category_name="false" post_type="nycc_feature" dots="true" show_author="false" speed="500" media_size="full"]'); ?> -->
      <?php echo do_shortcode('[recent_post_slider design="design-4" limit="5" show_category_name="false" post_type="nycc_feature" dots="false" show_author="false" speed="500" media_size="full"]'); ?>
      <script>jQuery('.wppsac-readmorebtn').text('LEARN MORE');</script>
    </div>
  </div>
<!-- Featured Content END -->

<!-- Hearings Content START -->
<?php include 'hearings.php';?>
<!-- Hearings Content END -->

<!-- Livestream Content START -->
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>> <?php the_content(); ?> </article>
<?php endwhile; endif; ?>
<!-- Livestream Content END -->

<?php get_footer(); ?>