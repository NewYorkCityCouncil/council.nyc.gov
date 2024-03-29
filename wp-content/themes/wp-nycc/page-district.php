<?php /* Template Name: District */

// Get the District meta
$current_member_site = get_post_meta($post->ID, 'current_member_site', true);
$blog_details = get_blog_details($current_member_site);
$member_siteurl = $blog_details->siteurl;

if ($current_member_site) {
  // Switch to the current Member's site
  switch_to_blog($current_member_site);

  get_header();

  // Get the Member meta
  $d_number = get_option('council_district_number');
  $cm_number = 'council_member_' . get_option('council_district_number');
  $cm_name = get_option('council_member_name');

  restore_current_blog();
  wp_reset_postdata();
} else {
  get_header();
}

?>
<input type="hidden" id="district-val" value="<?php the_ID(); ?>" />
<script>
  var searchableTag = "district_"+window.location.href.split("district-")[1].replace("/","")
  function jsonFlickrApi(json) {
    jQuery.each(json.photos.photo, function(i, pic) {
      jQuery(".district-carousel").append("<div class='carousel-images'><div class='pic-title'>"+pic.title.split("-")[0]+"</div><img class='slider-image' src='https://c1.staticflickr.com/"+pic.farm+"/"+pic.server+"/"+pic.id+"_"+pic.secret+"_z.jpg'/></div>");
    });
  };

  jQuery.ajax({
    url: 'https://api.flickr.com/services/rest/',
    dataType: 'jsonp',
    data: {
      "method":"flickr.photos.search",
      "user_id":"34210875@N06",
      "api_key":"f5f12de72b3f9da379b9b6949ce0e219",
      "format":"json",
      "tags":searchableTag,
      "tag_mode": "any",
    }
  });

  jQuery(window).on("load", function() {
    jQuery('.district-carousel').show().slick({
      // adaptiveHeight: true,
      arrows: false,
      autoplay: true,
      autoplaySpeed:2500,
      cssEase: 'linear',
      dots: false,
      fade: true,
      infinite: true,
      pauseOnFocus: true,
      pauseOnHover: true,
      speed: 1000,
    });
    jQuery(".slider-image").css({width:"100%"})
    jQuery(".pic-title").each(function(){jQuery(this).width((jQuery(this).parent().children().last().width()-10))})
  });
</script>
<div class="row">
  <div class="columns medium-8 large-9 xxlarge-8">
    <!-- <#?php if($current_member_site) : ?>
      <div class="district-carousel" aria-hidden="true" style="display:none;"></div>
      <br>
    <#?php endif; ?> -->
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

      <section class="page-content">

        <?php if ($current_member_site) { ?>
          <?php

          switch_to_blog($current_member_site);

          $frontpage_id = get_option('page_on_front');
          $args = array(
            'page_id' => $frontpage_id,
          );
          $frontpage_query = new WP_Query( $args );
          while ( $frontpage_query->have_posts() ) : $frontpage_query->the_post();
            the_excerpt();
          endwhile;

          restore_current_blog();
          wp_reset_postdata();

          ?>
          <hr>
          <div class="row">
            <div class="columns large-6">
              <?php
                // List Committees
                $list_committees = new WP_Query('post_type=nycc_committee&orderby=menu_order&order=ASC&posts_per_page=-1');
                if ( $list_committees->have_posts() ) {
                  echo '<h2 class="header-tiny">Committees</h2>';
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
                        // elseif (  $cm_position == 'acting_co_chair'  ):
                        //     echo ' <small>(Acting Co-Chair)</small>';
                        elseif (  $cm_position == 'secretary'  ):
                            echo ' <small>(Secretary)</small>';
                        elseif (  $cm_position == 'treasurer'  ):
                            echo ' <small>(Treasurer)</small>';
                        // elseif (  $cm_position == 'speaker'  ):
                        //     echo ' <small>(Speaker)</small>';
                        // elseif (  $cm_position == 'ex-officio'  ):
                        //     echo ' <small>(Ex-Officio)</small>';
                        // elseif (  $cm_position == 'deputy_speaker'  ):
                        //     echo ' <small>(Deputy Speaker)</small>';
                        // elseif (  $cm_position == 'majority_whip'  ):
                        //     echo ' <small>(Majority Whip)</small>';
                        endif;
                        // $pub_id = get_the_ID();
                        // $list_subcommittees = new WP_Query('post_type=nycc_committee&orderby=menu_order&order=ASC&post_parent=' . $pub_id . '&posts_per_page=-1');
                        // if ( $list_subcommittees->have_posts() ) {
                        //   echo '<ul>';
                        //     while ( $list_subcommittees->have_posts() ) : $list_subcommittees->the_post();
                        //       $cm_position = get_post_meta($post->ID, $cm_number, true);
                        //       if ( $cm_position != '' ) {
                        //         echo '<li>';
                        //         ?#><strong><a href="<?php the_permalink(); ?#>"><?php the_title(); ?#></a></strong><?php
                        //         if ( $cm_position == 'chair' ):
                        //             echo ' <small>(Chair)</small>';
                        //         elseif (  $cm_position == 'co_chair'  ):
                        //             echo ' <small>(Co-Chair)</small>';
                        //         elseif (  $cm_position == 'vice_chair'  ):
                        //             echo ' <small>(Vice Chair)</small>';
                        //         elseif (  $cm_position == 'vice_co_chair'  ):
                        //             echo ' <small>(Vice Co-Chair)</small>';
                        //         elseif (  $cm_position == 'secretary'  ):
                        //             echo ' <small>(Secretary)</small>';
                        //         elseif (  $cm_position == 'treasurer'  ):
                        //             echo ' <small>(Treasurer)</small>';
                        //         endif;
                        //         echo '</li>';
                        //       }
                        //     endwhile;
                        //     wp_reset_postdata();
                        //     echo '</ul>';
                        // }
                        echo '</li>';
                      }
                    }
                  echo '</ul>';
                  // echo '<hr>';
                }
              ?>
            </div>
            <div class="columns large-6">
              <?php
                // List Caucuses
                $args = array(
                  'post_type'  => 'nycc_caucus',
                  'meta_key'   => $cm_number,
                  'orderby'    => 'menu_order',
                  'order'      => 'ASC',
                  'meta_query' => array(
                    array(
                      'key'     => $cm_number,
                      'value'   => array( 'member','chair','co_chair','vice_chair','vice_co_chair', 'acting_co_chair', 'secretary','treasurer', 'speaker', 'deputy_speaker', 'majority_whip' ),
                      'compare' => 'IN'
                    ),
                  ),
                  'posts_per_page' => '-1'
                );
                $list_caucuses = new WP_Query( $args );
                if ( $list_caucuses->have_posts() ) {
                  echo '<h2 class="header-tiny">Caucuses</h2>';

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
                      // elseif (  $cm_position == 'acting_co_chair'  ):
                      //     echo ' <small>(Acting Co-Chair)</small>';
                      elseif (  $cm_position == 'secretary'  ):
                          echo ' <small>(Secretary)</small>';
                      elseif (  $cm_position == 'treasurer'  ):
                          echo ' <small>(Treasurer)</small>';
                      // elseif (  $cm_position == 'speaker'  ):
                      //     echo ' <small>(Speaker)</small>';
                      // elseif (  $cm_position == 'ex-officio'  ):
                      //     echo ' <small>(Ex-Officio)</small>';
                      // elseif (  $cm_position == 'deputy_speaker'  ):
                      //     echo ' <small>(Deputy Speaker)</small>';
                      // elseif (  $cm_position == 'majority_whip'  ):
                      //     echo ' <small>(Majority Whip)</small>';
                      endif;
                      echo '</li>';
                    }
                  echo '</ul>';
                }
              ?>
            </div>
          </div>
        <?php
        } else {
          the_content();
        }
        ?>

      </section>

    </article>

    <?php endwhile; endif; ?>

  </div>

  <?php
  if ($current_member_site) {
    // Switch to the current Member's site
    switch_to_blog($current_member_site);
    ?>

    <div class="sidebar columns medium-4 large-3 xxlarge-4">
      <?php

      nycc_sidebar_nav();

      $is_main = true;
      include(locate_template('../wp-nycc-member/contact_widget.php'));
      // get_template_part( '../wp-nycc-member/contact_widget' );

      ?>

      <div id="district-widgets-container" data-membersite="<?php echo $member_siteurl; ?>">
      </div>

    </div>

    <?php
    restore_current_blog();
    wp_reset_postdata();
  } else {
    get_header();
  }
  ?>

</div>

<?php get_footer(); ?>
