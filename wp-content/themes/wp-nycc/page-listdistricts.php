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
                  $sites = wp_get_sites();
                  foreach ($sites as $site) {
                      $ID = $site['blog_id'];
                      $number = get_blog_option($ID,'council_district_number');
                      $name = get_blog_option($ID,'council_member_name' );
                      $thumbnail = get_blog_option($ID,'council_member_thumbnail' );
                      $party = get_blog_option($ID,'council_member_party');
                      $borough = get_blog_option($ID,'council_district_borough');
                      $neighborhoods = get_blog_option($ID,'council_district_neighborhoods');
                      if ( $number ) {
                        ?>
                        <tr>
                          <td class="sort-district"><a class="button small expanded" href="<?php echo get_blogaddress_by_id($ID); ?>"><strong><?php echo $number; ?></strong></a></td>
                          <td class="sort-member"><a data-member-name="<?php echo $name; ?>" href="<?php echo get_blogaddress_by_id($ID); ?>"><strong><?php echo $name; ?></strong></a></td>
                          <td><a href="<?php echo get_blogaddress_by_id($ID); ?>"><img class="inline-icon large" src="<?php echo $thumbnail; ?>" /></a></td>
                          <td class="sort-borough"><?php echo $borough; ?></td>
                          <td class="sort-party"><?php echo $party; ?></td>
                          <td class="sort-neighborhoods neighborhoods"><?php echo $neighborhoods; ?></td>
                        </tr>
                        <?php
                      }
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
