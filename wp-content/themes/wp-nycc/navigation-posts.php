<?php if ( get_previous_posts_link() || get_next_posts_link() ) { ?>
  <div class="previous-next-links">
    <div class="float-left"><?php previous_posts_link( '&laquo; Newer' ); ?></div>
    <div class="float-right"><?php next_posts_link( 'Older &raquo;', '' ); ?></div>
  </div>
<?php } ?>
