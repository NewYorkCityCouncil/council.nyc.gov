<?php /* Template Name: Speaker's District */

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

        <div id="district-widgets-container" data-membersite="<?php echo $member_siteurl; ?>"></div>

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
