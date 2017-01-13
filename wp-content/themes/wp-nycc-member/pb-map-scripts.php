<?php ?>

<script type="text/javascript">

// foo

    <?php
    $ballot_items = new WP_Query('post_type=nycc_pb_vote_site&orderby=menu_order&order=ASC&posts_per_page=-1');
    if ( $ballot_items->have_posts() ) : while ( $ballot_items->have_posts() ) : $ballot_items->the_post();

    $ID = $post->ID;
    $lat = get_post_meta($ID, 'pb_vote_site_lat', true);
    $lon = get_post_meta($ID, 'pb_vote_site_lon', true);
    $latlon = $lat . ',' . $lon;
    $the_content =  preg_replace("/\r?\n/", "\\n", addslashes( nl2br( get_the_content()) ));
    if ( $latlon ) { ?>
      var vote_site<?php echo $ID ?> = L.circleMarker([<?php echo $latlon; ?>], {
        radius: 8,
        weight: 2,
        color: '#2f56a6',
        opacity: 1,
        fillOpacity: 0.5
      }).addTo(map).bindPopup(
        '<small class="text-small text-dark-gray">PBNYC Vote Site:</small>' +
        '<h4><strong><?php preg_replace("/\r?\n/", "\\n", addslashes( the_title() )); ?></strong></h4>' +
        '<div class="text-small"><?php echo $the_content; ?></div>'
      ).on({
        mousemove: mousemoveMarker,
        mouseout: mouseoutMarker
      });
      function mousemoveMarker(e) {
        var layer = e.target;
        layer.setStyle({
          weight: 3,
          color: '#174299'
        });
      }
      function mouseoutMarker(e) {
        var layer = e.target;
        layer.setStyle({
          weight: 2,
          color: '#2f56a6'
        });
      }

      <?php
    }

    endwhile; endif;

    wp_reset_postdata();

    ?>

</script>
