<div id="district-sidebar" class="sidebar columns medium-4 large-3 xxlarge-4">
  <div id="district-widgets">
    <?php

    nycc_primary_nav();

    get_template_part('contact_widget');

    if ( is_active_sidebar( 'posts-sidebar' ) ) :
      dynamic_sidebar( 'posts-sidebar' );
    endif;

    ?>
  </div>
</div>
