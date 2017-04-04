<div id="district-sidebar" class="sidebar columns medium-4 large-3 xxlarge-4">
  <div id="district-widgets">
    <?php

    nycc_sidebar_nav();

    get_template_part( '../wp-nycc-member/contact_widget' );

    dynamic_sidebar( 'posts-sidebar' );

    ?>
  </div>
</div>
