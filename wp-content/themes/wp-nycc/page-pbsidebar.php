<?php /* Template Name: PB Sidebar */ ?>

<?php get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <div class="row">
    <div class="columns">

      <header class="page-header">
        <h1 class="header-xxlarge">Participatory Budgeting</h1>
        <?php get_template_part( 'pb_page_nav' ); ?>
        <hr>
      </header>


    </div>
    <div class="columns medium-8">

      <?php the_content(); ?>

    </div>
    <div class="columns medium-4">

      <?php get_sidebar('pbnyc'); ?>

    </div>
  </div>

  <?php endwhile; endif; ?>

<?php get_footer(); ?>
