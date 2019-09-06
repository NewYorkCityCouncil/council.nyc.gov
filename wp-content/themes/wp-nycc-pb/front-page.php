<?php get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

      <div class="row">

        <div class="columns">
          <div class="page-header">
            <h1 class="header-xxlarge">Participatory Budgeting</h1>
            <div class="header-menu"><?php nycc_pb_nav(); ?></div>
            <hr>
          </div>
        </div>

        <div class="columns medium-8">

          <section class="page-content">
            <?php the_content(); ?>
          </section>

        </div>

        <?php get_sidebar(); ?>

      </div>

    </article>

  <?php endwhile; endif; ?>

<?php get_footer(); ?>
