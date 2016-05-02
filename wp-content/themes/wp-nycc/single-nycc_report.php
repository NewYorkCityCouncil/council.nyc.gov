<?php get_header(); ?>

<div class="row">
  <div class="columns">

    <?php
    if ( $post->post_parent > 0 ) {
        get_template_part( 'nycc_report_nav' );
        if ( has_post_thumbnail() ) {
            get_template_part( 'img_header_style' );
            ?>
            <div class="image-header fit-container">
              <header class="page-header image-overlay-large">
                <div class="row">
                  <div class="columns clearfix">
                    <h1 class="image-overlay-text header-xxlarge"><?php the_title(); ?></h1>
                  </div>
                </div>
              </header>
            </div>
            <?php
        } else {
            ?>
            <header class="page-header">
              <h1 class="page-title"><?php the_title(); ?></h1>
            </header>
            <?php
        }
    } else {
        ?>
        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </header>
        <hr>
        <div class="row">
          <div class="columns medium-4">
            <?php

            echo '<h5 class="table-of-contents-header"><em>Table of Contents:</em></h5>';
            echo '<ul class="table-of-contents">';

            $report_toc_label = get_post_meta($post->ID, 'report_toc_label', true);
            ?><li><?php if ( $report_toc_label ) { echo $report_toc_label; } else { the_title(); } ?></li><?php

            $list_report_pages = new WP_Query('post_type=nycc_report&orderby=menu_order&order=ASC&post_parent=' . $post->ID . '&posts_per_page=-1');

            if ( $list_report_pages->have_posts() ) {
              while ( $list_report_pages->have_posts() ) {
                $list_report_pages->the_post();
                $report_toc_before = get_post_meta($post->ID, 'report_toc_before', true);
                $report_toc_label = get_post_meta($post->ID, 'report_toc_label', true);
                if ( $report_toc_before ) {
                  echo '<li class="category"><strong>' . $report_toc_before . '</strong></li>';
                }
                ?><li><a href="<?php the_permalink(); ?>"><?php if ( $report_toc_label ) { echo $report_toc_label; } else { the_title(); } ?></a></li><?php
              }
            }

            wp_reset_postdata();

            echo '</ul>';

            $report_link_url = get_post_meta($post->ID, 'report_link_url', true);
            $report_link_text = get_post_meta($post->ID, 'report_link_text', true);
            if ( $report_link_url && $report_link_text ) {
              echo '<a href="' . $report_link_url . '" class="button">' . $report_link_text . '</a>';
            }

            ?>
          </div>
          <div class="columns medium-8">
            <?php
    }

    if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
        <?php the_content(); ?>
      </article>
    <?php endwhile; endif;

    if ( $post->post_parent > 0 ) {
        get_template_part( 'nycc_report_nav' );
    } else {
        ?>
          </div>
        </div>
        <?php
    }
    ?>

  </div>
</div>

<?php get_footer(); ?>
