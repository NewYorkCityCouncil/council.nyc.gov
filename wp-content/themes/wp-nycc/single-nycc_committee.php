<?php get_header(); ?>

  <div class="row">
    <div class="columns">

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

        <div class="page-header">
          <h1 class="header-xxlarge"><?php the_title(); ?></h1>
          <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
        </div>

        <hr>

        <div class="row">
          <div class="columns medium-8">
            <?php the_content(); ?>
            <script>
              console.log("test")
              if (jQuery("#legistar-committee-id").length === 1){
                Date.prototype.stdTimezoneOffset = function() {
                  let jan = new Date(this.getFullYear(), 0, 1);
                  let jul = new Date(this.getFullYear(), 6, 1);
                  return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
                }
                Date.prototype.dst = function() {
                  return this.getTimezoneOffset() < this.stdTimezoneOffset();
                }
                let date;
                new Date().dst() ?  date = new Date(new Date().getTime() - 4 * 3600 * 1000) : date = new Date(new Date().getTime() - 5 * 3600 * 1000)
                let startDate, endDate, startYear = date.getFullYear(), startMonth = date.getMonth()+1, startDay = date.getDate(),
                  nowHour = date.getUTCHours(), nowMinute = date.getUTCMinutes(),
                  midDay, meetingDate, meetingHour, meetingMinute, agendaLink;
                const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], month31 = [1,3,5,7,8,10,12], month30 = [4,6,9,11];
                const addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
                const token = "Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ";
                const committeeId = jQuery("#legistar-committee-id").val();

                startDate = startYear+"-"+addZero(startMonth)+"-"+addZero(startDay);
                endDate = month31.indexOf(startMonth) === -1 ? startYear+"-"+addZero(startMonth)+"-30" : startYear+"-"+addZero(startMonth)+"-31";
                jQuery.ajax({
                  type:"GET",
                  dataType:"jsonp",
                  url:"https://webapi.legistar.com/v1/nyc/events?token="+token+"&$filter=EventBodyId+eq+"+committeeId+"+and+EventDate+ge+datetime%27"+startDate+"%27+and+EventDate+lt+datetime%27"+endDate+"%27&$orderby=EventDate+asc",
                  success:function(hearings){
                    function timeConverter(timeString){
                      let hr = parseInt(timeString.split(" ")[0].split(":")[0]);
                      let min = parseInt(timeString.split(" ")[0].split(":")[1]);
                      let ampm = timeString.split(" ")[1];
                      ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr * 100 : hr = (hr+12) * 100;
                      return hr+min;
                    };
                      
                    jQuery("#hearing-loader").remove();
                    if (hearings.length === 0){
                      jQuery("#hearing-list").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS THIS MONTH</em></div>");
                    } else {
                      hearings.forEach(function(hearing){
                        meetingDate = new Date(hearing.EventDate);
                        midDay = hearing.EventTime.split(" ")[1];
                        meetingHour = parseInt(hearing.EventTime.split(" ")[0].split(":")[0]);
                        meetingMinute = parseInt(hearing.EventTime.split(" ")[0].split(":")[1]);
                        hearing.EventAgendaFile !== null ? agendaLink = hearing.EventAgendaFile : agendaLink = "#";
                        midDay === "PM" && meetingHour !== 12 ? meetingHour += 12 : meetingHour;
                        if(hearing.EventAgendaStatusName.toLowerCase() !== "draft"){
                          if(hearing.EventAgendaStatusName.toLowerCase() !== "deferred"){
                            jQuery("#hearing-list").append("<div class='columns column-block' aria-label='hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small aria-label='start time'>"+months[meetingDate.getMonth()]+" "+meetingDate.getDate()+" @ "+hearing.EventTime+"</small><br><i class='fas fa-map-marker-alt'></i> <small aria-label='location'>"+hearing.EventLocation+"</small></div>");
                          };
                        };
                      });
                      if (jQuery("#hearing-list").children().length === 0){
                        jQuery("#hearing-list").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></div>");
                      };
                    };
                  }
              });
            };
            // FOR IF A EVENT STATUS IS DEFERRED
            // $("#hearing-list").append("<div class='columns column-block' aria-label='hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small aria-label='start time'><s>"+months[meetingDate.getMonth()]+" "+meetingDate.getDay()+" @ "+hearing.EventTime+"</s> Deferred</small><br><i class='fas fa-map-marker-alt'></i> <small aria-label='location'>"+hearing.EventLocation+"</small></div>");
          </script>
          </div>
          <div class="columns medium-4">
            <p><strong>The following Council Members serve on this committee:</strong></p>
            <ul id="committee-members">

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
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Chair)</small></li>';
                }
                endwhile;

                $list_districts->rewind_posts();

                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'co_chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Co-Chair)</small></li>';
                }
                endwhile;

                $list_districts->rewind_posts();

                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'vice_chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Vice Chair)</small></li>';
                }
                endwhile;

                $list_districts->rewind_posts();

                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'vice_co_chair' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Vice Co-Chair)</small></li>';
                }
                endwhile;

                $list_districts->rewind_posts();

                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'secretary' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Secretary)</small></li>';
                }
                endwhile;

                $list_districts->rewind_posts();

                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'treasurer' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a> <small>(Treasurer)</small></li>';
                }
                endwhile;

                $list_districts->rewind_posts();

                while ( $list_districts->have_posts() ) : $list_districts->the_post();
                $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);
                if ($current_member_site_ID) {
                  switch_to_blog($current_member_site_ID);
                    $number = get_blog_option($current_member_site_ID,'council_district_number');
                    $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );
                    $cm_number = 'council_member_' . $number;
                  restore_current_blog();
                  wp_reset_postdata();
                }
                $status = get_post_meta($post->ID, $cm_number, true);
                if ( $status == 'member' ) {
                  echo '<li><a href="' . get_site_url($current_member_site_ID) . '"><strong>' . $cm_name . '</strong></a></li>';
                }
                endwhile;

              }

              wp_reset_postdata();
              ?>

            </ul>
          </div>
        </div>

      </article>

      <?php endwhile; endif; ?>

    </div>
  </div>

<?php get_footer(); ?>
