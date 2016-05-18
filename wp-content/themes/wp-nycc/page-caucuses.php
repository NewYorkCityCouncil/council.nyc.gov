<?php /* Template Name: Caucuses List */ ?>

<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><h4 class="page-excerpt sans-serif"><?php echo get_the_excerpt(); ?></h4><?php } ?>
        </header>

        <hr>

        <div class="row">
          <div class="columns medium-6">
            <section class="page-content">
              <?php the_content(); ?>
              <?php wp_link_pages(); ?>
            </section>
          </div>
          <div class="columns medium-6">
            <?php
            $list_caucuses = new WP_Query(
              array(
                'post_type' => 'nycc_caucus',
                'orderby' => 'menu_order title',
                'order' => 'ASC',
                'post_parent' => '0',
                'posts_per_page' => '-1'
              )
            );
            if ( $list_caucuses->have_posts() ) {
              echo '<ul class="text-large">';

                while ( $list_caucuses->have_posts() ) {
                  $list_caucuses->the_post();
                  echo '<li>';
                  ?><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong><?php
                  echo '</li>';
                }

              echo '</ul>';
            }
            wp_reset_postdata();
            ?>
          </div>
        </div>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
