<?php /* Template Name: Districts List */ ?>

<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <header class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
        </header>

        <div id="districts-list">

          <div class="row">
            <div class="columns large-3 xxlarge-12">
              <?php the_content(); ?>
              <input type="text" class="search search--no-submit" placeholder="Search the list..." />
              <input type="submit" class="search--hidden-submit" />
            </div>
            <div class="columns large-9 xxlarge-12 scrollable">
              <table class="full-width table--no-border-spacing">
                <thead>
                  <th><button class="button sort small secondary expanded" data-sort="sort-district">#</button></th>
                  <th colspan="2"><button class="button sort small secondary expanded" data-sort="sort-member">Member</button></th>
                  <th><button class="button sort small secondary expanded" data-sort="sort-borough">Borough</button></th>
                  <th><button class="button sort small secondary expanded" data-sort="sort-party">Party</button></th>
                  <th><button class="button disabled no-outline small secondary expanded" tabindex="-1">Neighborhoods</button></th>
                </thead>
                <tbody class="list">
                  <?php

                  // Get all the pages that use the District template
                  $args = array(
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'orderby'    => 'menu_order',
                    'order'      => 'ASC',
                    'posts_per_page' => '-1',
                    'meta_query' => array(
                        array(
                            'key' => '_wp_page_template',
                            'value' => 'page-district.php',
                        )
                    )
                  );
                  $list_districts = new WP_Query( $args );

                  // Loop through the District pages
                  if ( $list_districts->have_posts() ) {
                    echo '<ul>';
                      while ( $list_districts->have_posts() ) : $list_districts->the_post();

                      // Get the District meta
                      $ID = get_post_meta($post->ID, 'current_member_site', true);

                      if ($ID) {
                        // Switch to the current Member's site
                        switch_to_blog($current_member_site);

                        // Get the Member's site meta
                        $number = get_blog_option($ID,'council_district_number');
                        $name = get_blog_option($ID,'council_member_name' );
                        $thumbnail = get_blog_option($ID,'council_member_thumbnail' );
                        $party = get_blog_option($ID,'council_member_party');
                        $borough = get_blog_option($ID,'council_district_borough');
                        $neighborhoods = get_blog_option($ID,'council_district_neighborhoods');
                        $district_url = esc_url( network_site_url() ) . 'district-' . $number . '/';

                        // Add the Member's table row
                        ?>
                        <tr>
                          <td class="sort-district"><a class="button small expanded" href="<?php echo $district_url; ?>"
                            ><strong><?php echo $number; ?></strong></a></td>
                          <td class="sort-member"><a data-member-name="<?php echo $name; ?>" href="<?php echo $district_url; ?>"><strong><?php echo $name; ?></strong></a></td>
                          <td><a href="<?php echo $district_url; ?>"><img class="inline-icon large" src="<?php echo $thumbnail; ?>" /></a></td>
                          <td class="sort-borough"><?php echo $borough; ?></td>
                          <td class="sort-party"><?php echo $party; ?></td>
                          <td class="sort-neighborhoods neighborhoods"><?php echo $neighborhoods; ?></td>
                        </tr>
                        <?php

                        restore_current_blog();
                        wp_reset_postdata();
                      } else {
                        $number = $post->menu_order;
                        $name = 'Vacant';
                        $district_url = esc_url( network_site_url() ) . 'district-' . $number . '/';
                        ?>
                        <tr>
                          <td class="sort-district"><a class="button small expanded" href="<?php echo $district_url; ?>"
                            ><strong><?php echo $number; ?></strong></a></td>
                          <td class="sort-member"><a data-member-name="<?php echo $name; ?>" href="<?php echo $district_url; ?>"><strong><?php echo $name; ?></strong></a></td>
                          <td></td>
                          <td class="sort-borough"></td>
                          <td class="sort-party"></td>
                          <td class="sort-neighborhoods neighborhoods"></td>
                        </tr>
                        <?php
                      }

                      endwhile;
                      wp_reset_postdata();
                      echo '</ul>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
