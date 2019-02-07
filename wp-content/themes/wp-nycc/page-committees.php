<?php /* Template Name: Committees List */ ?>

<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </header>

        <hr>

        <div class="row">
          <div class="columns medium-6">

            <?php the_content(); ?>

          </div>
          <div class="columns medium-6">
            <?php
            $list_committees = new WP_Query(
              array(
                'post_type' => 'nycc_committee',
                'orderby' => 'menu_order title',
                'order' => 'ASC',
                'post_parent' => '0',
                'posts_per_page' => '-1'
              )
            );
            if ( $list_committees->have_posts() ) {
              echo '<ul aria-label="Committees list" class="text-large">';

                while ( $list_committees->have_posts() ) {
                  $list_committees->the_post();
                  echo '<li>';
                  ?><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong><?php
                  $parent_committee = get_the_title();
                  $pub_id = get_the_ID();
                  $issue = new WP_Query(
                    array(
                      'post_type' => 'nycc_committee',
                      'orderby' => 'menu_order title',
                      'order' => 'ASC',
                      'post_parent' => '0',
                      'post_parent' => $pub_id,
                      'posts_per_page' => '-1'
                    )
                  );
                  if ( $issue->have_posts() ) {
                    echo '<ul aria-label="Subcommittees of the '.$parent_committee.'" class="text-small">';
                      while ( $issue->have_posts() ) : $issue->the_post();
                          echo '<li>';
                          ?><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong><?php
                          echo '</li>';
                      endwhile;
                      wp_reset_postdata();
                      echo '</ul>';
                  }

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
