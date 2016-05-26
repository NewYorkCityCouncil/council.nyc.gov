<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <h1>Sorry. <span class="label">Error code: 404</span></h1>
      <p class="header-medium subheader sans-serif">We can't find the page you're looking for.</p>
      <hr>

      <div class="row">
        <div class="columns large-7">
          <p>The page you're looking for appears to have been moved, deleted, or does not exist. If you typed the URL by hand, please double-check what you've entered. </p>
          <p>Here are some pages you might find helpful: </p>
          <?php switch_to_blog(1); ?>
          <?php nycc_404_menu(); ?>
          <?php restore_current_blog(); ?>
        </div>
        <div class="columns large-5">
        </div>
      </div>

    </div>
  </div>

<?php get_footer(); ?>
