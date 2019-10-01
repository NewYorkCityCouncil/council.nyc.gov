<div class="row collapse expanded">
  <div class="columns medium-7 xxlarge-12">

    <?php
    $frontpage_id = get_option( 'page_on_front' );
    $frontpage_query = new WP_Query( 'page_id=' . $frontpage_id );
    while ( $frontpage_query->have_posts() ) : $frontpage_query->the_post();
      get_template_part( 'img_header_style' );
    endwhile;
    ?>

    <div class="image-header widescreen district-image-header">
      <div class="image-overlay">
        <div class="row">
          <div class="columns clearfix">
            <h2 class="image-overlay-text district-number"><a href="<?php echo esc_url( network_site_url() ); ?>district-<?php echo get_option('council_district_number'); ?>/">District&nbsp;<?php echo get_option('council_district_number'); ?></a></h1>
            <h2 class="image-overlay-text district-member"><a href="<?php echo site_url(); ?>/"><?php echo get_option('council_member_name'); ?></a></h4>
            <h2 class="image-overlay-text district-neighborhoods show-for-large"><?php echo get_option('council_district_neighborhoods'); ?></h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="columns medium-5 xxlarge-12">
    <div id="map-container" aria-hidden="true" class="district">
      <div id="map"></div>
    </div>
  </div>
</div>
