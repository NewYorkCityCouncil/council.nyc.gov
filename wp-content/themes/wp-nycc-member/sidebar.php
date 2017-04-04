<div class="sidebar columns medium-4 large-3 xxlarge-4">
  <?php

  nycc_sidebar_nav();

  get_template_part('contact_widget');

  ?>

  <div id="district-widgets">
    <?php
    if ( is_active_sidebar( 'posts-sidebar' ) ) :
      dynamic_sidebar( 'posts-sidebar' );
    endif;
    ?>
  </div>

</div>
