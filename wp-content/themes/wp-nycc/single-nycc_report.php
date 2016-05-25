<?php get_header(); ?>

<div class="row">
  <div class="columns medium-8">

    <?php
    if ( has_post_thumbnail() ) {
        get_template_part( 'img_header_style' );
        ?>
        <div class="image-header fit-container widescreen">
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
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </header>
        <hr>
        <?php
    }
    ?>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
        <?php the_content(); ?>
      </article>
    <?php endwhile; endif; ?>

    <?php
    if ( $post->post_parent > 0 ) {
      get_template_part( 'nycc_report_nav' );
    }
    ?>

  </div>
  <div class="columns medium-4">

    <h5 class="table-of-contents-header"><em>Table of Contents:</em></h5>
      <ul class="table-of-contents">

        <?php
        if($post->post_parent) {
          // this is a child page
          $list_report_pages = new WP_Query('post_type=nycc_report&orderby=menu_order&order=ASC&post_parent='.$post->post_parent.'&posts_per_page=-1');
          $report_toc_label = get_post_meta($post->post_parent, 'report_toc_label', true);
          if ( !$report_toc_label ) {
            $report_toc_label = get_the_title($post->post_parent);
          }
          echo '<li><a href="'. get_permalink($post->post_parent) .'">'. $report_toc_label . '</a></li>';
          $report_link_url = get_post_meta($post->post_parent, 'report_link_url', true);
          $report_link_text = get_post_meta($post->post_parent, 'report_link_text', true);
        } else {
          // this is the parent page
          $list_report_pages = new WP_Query('post_type=nycc_report&orderby=menu_order&order=ASC&post_parent='.$post->ID.'&posts_per_page=-1');
          $report_toc_label = get_post_meta($post->ID, 'report_toc_label', true);
          if ( !$report_toc_label ) {
            $report_toc_label = get_the_title($post->ID);
          }
          echo '<li>'. $report_toc_label . '</li>';
          $report_link_url = get_post_meta($post->ID, 'report_link_url', true);
          $report_link_text = get_post_meta($post->ID, 'report_link_text', true);
        }

        $current_page = $post->ID;

        if ( $list_report_pages->have_posts() ) {
          while ( $list_report_pages->have_posts() ) {
            $list_report_pages->the_post();

            // there's a "TOC Before" value (Category)
            $report_toc_before = get_post_meta($post->ID, 'report_toc_before', true);
            if ( $report_toc_before ) {
              echo '<li class="category"><strong>' . $report_toc_before . '</strong></li>';
            }

            $report_toc_label = get_post_meta($post->ID, 'report_toc_label', true);
            if ( !$report_toc_label ) {
              $report_toc_label = get_the_title($post->ID);
            }
            if ( $post->ID == $current_page ) {
              echo '<li>'. $report_toc_label . '</li>';
            } else {
              echo '<li><a href="'. get_the_permalink() .'">'. $report_toc_label . '</a></li>';
            }

          }
        }
        wp_reset_postdata();
        ?>

      </ul>

    <?php
    if ( $report_link_url && $report_link_text ) {
      echo '<a href="' . $report_link_url . '" class="button">' . $report_link_text . '</a>';
    }
    ?>

  </div>
</div>

<?php get_footer(); ?>
