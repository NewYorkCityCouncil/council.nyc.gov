<?php /* Template Name: Districts List */ ?>

<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <div class="page-header">
          <h1 id="for-table-caption" class="header-xxlarge"><?php the_title(); ?></h1>
        </div>

        <div id="districts-list">

          <div class="row">
            <div class="columns large-4 xxlarge-12">

              <?php the_content(); ?>

            </div>
            <div class="columns large-8 xxlarge-12 scrollable">
              <input type="text" aria-hidden="true" style="right:1000%;position:absolute;" value="" id="clipboard-copy">
              <form role="search" id="list-search" style="position:relative;">
                <input type="text" aria-label="Search for your district and council member" id="list-search-input" class="-no-margin -search -search--no-submit" placeholder="Address &amp; Borough | Member | Neighborhood" />
                <span class="district-submit" onClick="jQuery('#list-search').submit();" style="color: #666; cursor: pointer; position: absolute; top: 7px; right: 10px;"><i class="fa fa-search" aria-hidden="true"></i></span>
              </form>
              <div style="position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden;" role="alert" aria-live="assertive" id="assertive-message"></div>
              <table class="full-width" aria-describedby="for-table-caption">
                <thead>
                  <th><button onclick="declareAction('Sorted list based on district number in ascending order')" class="button sort small secondary expanded" aria-label="Sort by district number" data-sort="sort-district">No.</button></th>
                  <th colspan="2"><button onclick="declareAction('Sorted list based on council member name in A to Z order')" class="button sort small secondary expanded" aria-label="Sort by council member" data-sort="sort-member">Member</button></th>
                  <th><button onclick="declareAction('Sorted list based on borough in A to Z order')" class="button sort small secondary expanded" aria-label="Sort by borough" data-sort="sort-borough">Borough</button></th>
                  <th class="show-for-medium"><button onclick="declareAction('Sorted list based on political party in A to Z order')" class="button sort small secondary expanded" aria-label="Sort by political party" data-sort="sort-party">Party</button></th>
                  <th class="show-for-medium" style="width:30%;"><button class="button disabled no-outline small secondary expanded" tabindex="-1" disabled>Neighborhoods</button></th>
                  <th><button class="button disabled no-outline small secondary expanded" tabindex="-1" disabled>Email</button></th>
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
                      while ( $list_districts->have_posts() ) : $list_districts->the_post();

                      // Get the District meta
                      $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                      $neighborhoods = get_post_meta($post->ID, 'neighborhoods',true);

                      if ($current_member_site_ID) {
                        // Switch to the current Member's site
                        switch_to_blog($current_member_site_ID);

                        // Get the Member's site meta
                        $number = get_blog_option($current_member_site_ID,'council_district_number');
                        $name = get_blog_option($current_member_site_ID,'council_member_name' );
                        $thumbnail = get_blog_option($current_member_site_ID,'council_member_thumbnail' );
                        $party = get_blog_option($current_member_site_ID,'council_member_party');
                        $borough = get_blog_option($current_member_site_ID,'council_district_borough');
                        $district_url = esc_url( network_site_url() ) . 'district-' . $number . '/';
                        //New from JC
                        $email = get_blog_option($current_member_site_ID,'council_district_email');

                        // Add the Member's table row
                        ?>
                        <tr>
                          <td class="sort-district"><a class="button small expanded" href="<?php echo $district_url; ?>"
                            ><strong><?php echo $number; ?></strong></a></td>
                          <td class="sort-member"><a data-member-name="<?php echo $name; ?>" href="<?php echo $district_url; ?>"><strong><?php echo $name; ?></strong></a></td>
                          <td style="text-align: right;"><a href="<?php echo $district_url; ?>"><img alt="<?php echo $name; ?> Head Shot" class="inline-icon large" src="<?php echo $thumbnail; ?>" /></a></td>
                          <td class="sort-borough"><?php echo $borough; ?></td>
                          <td class="sort-party show-for-medium"><?php echo $party; ?></td>
                          <td class="sort-neighborhoods neighborhoods show-for-medium"><?php echo $neighborhoods; ?></td>
                          <?php if ($email !== "") :  ?>
                            <td class="sort-email email" style="text-align:center;"><a aria-label="Send an email to Council Member <?php echo $name; ?>" href="mailto:<?php echo $email; ?>"><i class="fa fa-share" aria-hidden="true"></i><i class="fa fa-envelope-o" aria-hidden="true"></i></a><br><span style="cursor:pointer;" aria-label="Click to copy Council Member <?php echo $name; ?>'s email address" onclick="copyToClipboard(jQuery(this))" data-email=<?php echo $email; ?>>Copy</span></td>
                          <?php else : ?>
                            <td></td>
                          <?php endif; ?>  
                        </tr>
                        <?php

                        restore_current_blog();
                        wp_reset_postdata();
                      } else {
                        $number = $post->menu_order;
                        $name = 'Office of Council District ' . $number;
                        $district_url = esc_url( network_site_url() ) . 'district-' . $number . '/';
                        ?>
                        <tr>
                          <td class="sort-district">
                            <a class="button small expanded" href="<?php echo $district_url ?>">
                              <strong><?php echo $number; ?></strong>
                            </a>
                          </td>
                          <td class="sort-member">
                            <a data-member-name="<#?php echo $name; ?>" href="<?php echo $district_url ?>">
                              <strong><?php echo $name; ?></strong>
                            </a>
                          </td>
                          <td><span style="opacity: 0%;">Blank</span></td>
                          <td class="sort-borough"><span style="opacity: 0%;">Blank</span></td>
                          <td class="sort-party show-for-medium"><span style="opacity: 0%;">Blank</span></td>
                          <td class="sort-neighborhoods neighborhoods show-for-medium"><?php echo $neighborhoods ?></td>
                          <td class="sort-email email" style="text-align:center;"><a aria-label="Send an email to District <?php echo $number; ?>" href="mailto:District<?php echo $number; ?>@council.nyc.gov"><i class="fa fa-share" aria-hidden="true"></i><i class="fa fa-envelope-o" aria-hidden="true"></i></a><br><span style="cursor:pointer;" aria-label="Click to copy District <?php echo $number; ?>'s email address" onclick="copyToClipboard(jQuery(this))" data-email="District<?php echo $number; ?>@council.nyc.gov">Copy</span></td>
                        </tr>
                        <?php
                      }

                      endwhile;
                      wp_reset_postdata();
                  }
                  ?>
                </tbody>
              </table>
              <script>
                function copyToClipboard(el) {
                  var copyText = jQuery("#clipboard-copy");
                  copyText.val(el.attr("data-email"));
                  copyText.select();
                  document.execCommand("copy");
                  $(el).animate({'opacity': 0}, 250, function () {
                      $(el).text('Copied!');
                  }).animate({'opacity': 1}, 250);
                }

                function declareAction(msg){
                  let newMsg = "";
                  if ($("#assertive-message").html().includes("district") && msg.includes("district")){
                    if ($("#assertive-message").html().includes("ascending")){newMsg = msg.replace("ascending", "descending")} else {newMsg = msg.replace("descending", "ascending")};
                  } else if ($("#assertive-message").html().includes("council") && msg.includes("council")){
                    if ($("#assertive-message").html().includes("A to Z")){newMsg = msg.replace("A to Z", "Z to A");} else {newMsg = msg.replace("Z to A", "A to Z");};
                  } else if ($("#assertive-message").html().includes("borough") && msg.includes("borough")){
                    if ($("#assertive-message").html().includes("A to Z")){newMsg = msg.replace("A to Z", "Z to A");} else {newMsg = msg.replace("Z to A", "A to Z");};
                  } else if ($("#assertive-message").html().includes("party") && msg.includes("party")){
                    if ($("#assertive-message").html().includes("A to Z")){newMsg = msg.replace("A to Z", "Z to A");} else {newMsg = msg.replace("Z to A", "A to Z");};
                  } else {
                    newMsg = msg;
                  };
                    $("#assertive-message").html(newMsg);
                }
              </script>
              <div id="list-search-error" class="callout alert text-center hide">
                <p class="text-small">No results match your search terms.</p>
                <p><strong>If you're searching an address, be sure to separate the address and borough with a comma.</strong></p>
              </div>
            </div>
          </div>

        </div>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
