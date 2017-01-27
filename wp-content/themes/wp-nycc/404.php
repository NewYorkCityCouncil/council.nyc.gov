<?php get_header(); ?>

  <div class="row">
    <div class="columns">


      <?php
      switch_to_blog(1);
      echo get_option('404_content');
      restore_current_blog();
      ?>

    </div>
  </div>

<?php get_footer(); ?>
