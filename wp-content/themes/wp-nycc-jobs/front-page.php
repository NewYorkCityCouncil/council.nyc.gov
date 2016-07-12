<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <h1 class="header-xxlarge">Job Opportunities</h1>
      <hr>
      <?php
      echo get_option('jobs_front_page_content');
      ?>

    </div>

    <div class="sidebar columns medium-4">
      <?php get_sidebar(); ?>
    </div>

  </div>

<?php get_footer(); ?>
