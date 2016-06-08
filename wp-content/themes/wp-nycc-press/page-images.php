<?php /* Template Name: All Images */ ?>

<?php get_header(); ?>

  <div class="row">
    <div class="columns medium-8">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </header>

        <hr>

        <?php if($post->post_content=="") : ?>
        <?php else : ?>
        <?php the_content(); ?>
        <hr>
        <?php endif; ?>

          <?php
          echo '<div class="image-grid"><div class="row collapse">';
          $temp_query = $wp_query;
          $paged = get_query_var( 'paged', 1 );
          $args = array(
              'post_type' => 'attachment',
              'post_mime_type' => 'image',
              'post_status' => null,
              'post_parent' => null,
              'post_status' => 'any',
              'posts_per_page' => 20,
              'paged' => $paged,
              );
          query_posts($args);
          while ( have_posts() ) : the_post();
          ?>
          <div id="image-<?php the_ID(); ?>" class="columns medium-6">
            <?php
            echo '<a class="thumbnail" href="';
            echo the_permalink();
            echo '">';
            echo wp_get_attachment_image( $attachment->ID, 'large' );
            if ( has_excerpt( $post->ID ) ) {
              echo '<span class="caption">';
              echo get_the_excerpt();
              echo '</span>';
            }
            echo '</a>';
            ?>
          </div>
          <?php
          endwhile;
          echo '</div></div>';
          nycc_page_navi();
          $wp_query = $temp_query;
          ?>

      </article>

      <?php endwhile; endif; ?>

    </div>

    <div class="sidebar columns medium-4">
      <?php get_sidebar(); ?>
    </div>

  </div>

<?php get_footer(); ?>
