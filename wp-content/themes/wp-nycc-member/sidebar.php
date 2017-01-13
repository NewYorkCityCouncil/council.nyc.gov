<div class="sidebar columns medium-4 large-3 xxlarge-4">
  <?php nycc_primary_nav(); ?>
  <?php if ( is_active_sidebar( 'posts-sidebar' ) ) : ?>
    <?php dynamic_sidebar( 'posts-sidebar' ); ?>
  <?php endif; ?>
</div>
