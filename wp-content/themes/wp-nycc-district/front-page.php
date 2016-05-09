<?php get_header(); ?>

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <?php
        $council_member_name = get_option('council_member_name');
        $council_member_short_name = get_option('council_member_short_name');
        ?>

        <section class="page-content">

          <p><?php echo get_option('council_member_short_bio'); ?></p>

          <p>Council Member <?php if ( $council_member_short_name ) {
            echo $council_member_short_name;
          } else {
            echo $council_member_name;
          } ?> serves on the following committees:</p>

          <?php
          $cm_number = 'council_member_' . get_option('council_district_number');

          switch_to_blog(1);

          $list_committees = new WP_Query('post_type=nycc_committee&orderby=menu_order&order=ASC&post_parent=0&posts_per_page=-1');
          if ( $list_committees->have_posts() ) {
            echo '<ul>';
              while ( $list_committees->have_posts() ) {
                $list_committees->the_post();
                $cm_position = get_post_meta($post->ID, $cm_number, true);
                if ( $cm_position != '' ) {
                  echo '<li>';
                  ?><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong><?php
                  if ( $cm_position == 'chair' ):
                      echo ' <small>(Chair)</small>';
                  elseif (  $cm_position == 'co_chair'  ):
                      echo ' <small>(Co-Chair)</small>';
                  elseif (  $cm_position == 'vice_chair'  ):
                      echo ' <small>(Vice Chair)</small>';
                  elseif (  $cm_position == 'vice_co_chair'  ):
                      echo ' <small>(Vice Co-Chair)</small>';
                  elseif (  $cm_position == 'secretary'  ):
                      echo ' <small>(Secretary)</small>';
                  elseif (  $cm_position == 'treasurer'  ):
                      echo ' <small>(Treasurer)</small>';
                  endif;
                  $pub_id = get_the_ID();
                  $list_subcommittees = new WP_Query('post_type=nycc_committee&orderby=menu_order&order=ASC&post_parent=' . $pub_id . '&posts_per_page=-1');
                  if ( $list_subcommittees->have_posts() ) {
                    echo '<ul>';
                      while ( $list_subcommittees->have_posts() ) : $list_subcommittees->the_post();
                        $cm_position = get_post_meta($post->ID, $cm_number, true);
                        if ( $cm_position != '' ) {
                          echo '<li>';
                          ?><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong><?php
                          if ( $cm_position == 'chair' ):
                              echo ' <small>(Chair)</small>';
                          elseif (  $cm_position == 'co_chair'  ):
                              echo ' <small>(Co-Chair)</small>';
                          elseif (  $cm_position == 'vice_chair'  ):
                              echo ' <small>(Vice Chair)</small>';
                          elseif (  $cm_position == 'vice_co_chair'  ):
                              echo ' <small>(Vice Co-Chair)</small>';
                          elseif (  $cm_position == 'secretary'  ):
                              echo ' <small>(Secretary)</small>';
                          elseif (  $cm_position == 'treasurer'  ):
                              echo ' <small>(Treasurer)</small>';
                          endif;
                          echo '</li>';
                        }
                      endwhile;
                      wp_reset_postdata();
                      echo '</ul>';
                  }
                  echo '</li>';
                }
              }
            echo '</ul>';
          }

          $args = array(
            'post_type'  => 'nycc_caucus',
            'meta_key'   => $cm_number,
            'orderby'    => 'menu_order',
            'order'      => 'ASC',
            'meta_query' => array(
              array(
                'key'     => $cm_number,
                'value'   => array( 'member','chair','co_chair','vice_chair','vice_co_chair','secretary','treasurer' ),
                'compare' => 'IN'
              ),
            ),
            'posts_per_page' => '-1'
          );
          $list_caucuses = new WP_Query( $args );
          if ( $list_caucuses->have_posts() ) {
            echo '<p>Council Member ';
            if ( $council_member_short_name ) {
              echo $council_member_short_name;
            } else {
              echo $council_member_name;
            }
            echo ' is also a member of the following caucuses:</p>';

            echo '<ul>';
              while ( $list_caucuses->have_posts() ) {
                $list_caucuses->the_post();
                $cm_position = get_post_meta($post->ID, $cm_number, true);
                echo '<li>';
                ?><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong><?php
                if ( $cm_position == 'chair' ):
                    echo ' <small>(Chair)</small>';
                elseif (  $cm_position == 'co_chair'  ):
                    echo ' <small>(Co-Chair)</small>';
                elseif (  $cm_position == 'vice_chair'  ):
                    echo ' <small>(Vice Chair)</small>';
                elseif (  $cm_position == 'vice_co_chair'  ):
                    echo ' <small>(Vice Co-Chair)</small>';
                elseif (  $cm_position == 'secretary'  ):
                    echo ' <small>(Secretary)</small>';
                elseif (  $cm_position == 'treasurer'  ):
                    echo ' <small>(Treasurer)</small>';
                endif;
                echo '</li>';
              }
            echo '</ul>';
          }

          restore_current_blog();
          wp_reset_postdata();
          ?>

          <?php the_content(); ?>

        </section>

      </article>

      <?php endwhile; endif; ?>

    </div>

      <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
