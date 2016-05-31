<?php if ( is_active_sidebar( 'posts-sidebar' ) ) : ?>
  <?php dynamic_sidebar( 'posts-sidebar' ); ?>
<?php endif; ?>

<div class="press-release-tags">
  <?php
  $args = array(
    'smallest'                  => 1,
    'largest'                   => 1,
    'unit'                      => 'em',
    'number'                    => 0,
    'format'                    => 'list',
    'separator'                 => '',
    'orderby'                   => 'count',
    'order'                     => 'DESC',
    'link'                      => 'view',
    'taxonomy'                  => 'post_tag',
    'echo'                      => true,
  );
  wp_tag_cloud($args);
  ?>
</div>

<a class="button small expanded- dashicons-before dashicons-rss" href="<?php bloginfo('rss2_url'); ?>">&nbsp;Press Release RSS&nbsp;Feed</a>
