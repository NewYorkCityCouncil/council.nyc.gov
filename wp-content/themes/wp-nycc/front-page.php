<?php get_header(); ?>

  <div class="row">
    <div class="columns">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
          <?php the_content(); ?>
        </article>
      <?php endwhile; endif; ?>
      <hr>
    </div>
    <div class="columns medium-8">
      <div class="row">
        <div class="columns small-12">
          <h3>Featured at the Council</h3>
          <div class="featured-carousel" style="display:none;"></div>
        </div>
      </div>
      <div class="row">
        <br>
        <div class="columns small-12">
          <h3>Featured Content</h3>
        </div>
      </div>
      <div class="row small-up-1 large-up-2 block-grid">

      <?php
      $list_features = new WP_Query('post_type=nycc_feature&orderby=menu_order&order=ASC&posts_per_page=-1');
      if ( $list_features->have_posts() ) {
        while ( $list_features->have_posts() ) {
          $list_features->the_post(); ?>
          <style>
            #feature-<?php the_ID(); ?> .image-header {
              margin: 0;
            }
            #feature-<?php the_ID(); ?> .image-header::before {
              background-image: url("<?php the_post_thumbnail_url( 'small' ); ?>");
            }
            /* small retina */
            @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
              #feature-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'medium' ); ?>");
              }
            }
            /* medium */
            @media only screen and (min-width: 40.0625em) {
              #feature-<?php the_ID(); ?> .image-header::before {
                padding-bottom: 56.25%;
                background-image: url("<?php the_post_thumbnail_url( 'medium' ); ?>");
                margin: 0;
              }
            }
            /* medium retina */
            @media only screen and (min-width: 40.0625em) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min--moz-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-width: 40.0625em) and (min-device-pixel-ratio: 2), only screen and (min-width: 40.0625em) and (min-resolution: 192dpi), only screen and (min-width: 40.0625em) and (min-resolution: 2dppx) {
              #feature-<?php the_ID(); ?> .image-header::before {
                background-image: url("<?php the_post_thumbnail_url( 'large' ); ?>");
              }
            }
          </style>
          <div class="columns" id="feature-<?php the_ID(); ?>">
            <?php $feature_link_url = get_post_meta($post->ID, 'feature_link_url', true); ?>
            <a href="<?php echo $feature_link_url; ?>">
              <div class="image-header fit-container">
                <div class="image-overlay">
                  <h4 class="image-overlay-text header-small sans-serif"><?php the_title(); ?></h4>
                </div>
              </div>
            </a>
          </div>
          <?php
        }

      }
      wp_reset_postdata();
      ?>

      </div>
    </div>

    <?php get_sidebar(); ?>

    <!-- New content -->
    <div class="columns medium-11 medium-centered">
      <hr>
      <div class="columns medium-5 speaker-council-twitter-feed">
        <a class="twitter-timeline" data-height="600" href="https://twitter.com/NYCSpeakerCoJo?ref_src=twsrc%5Etfw">Tweets by NYCSpeakerCoJo</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>
      <div class="columns medium-5 medium-offset-2 speaker-council-twitter-feed">
        <a class="twitter-timeline" data-height="600" href="https://twitter.com/NYCCouncil?ref_src=twsrc%5Etfw">Tweets by NYCCouncil</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>
      <script async>
        function adjustiFrames(){
          jQuery("#twitter-widget-0").width(jQuery("#twitter-widget-0").parent().width());
          jQuery("#twitter-widget-1").width(jQuery("#twitter-widget-1").parent().width());
        };
        jQuery(window).on("load",function(){
          setTimeout(function(){adjustiFrames()},1000);
          jQuery(window).on("orientationchange",function(){setTimeout(function(){adjustiFrames()},500)}).resize(adjustiFrames());
        });
      </script>
      <script>
        /*--------------------------------------------------
          Upcoming Stated Meetings jQuery
        --------------------------------------------------*/
        jQuery.ajax({
          type:"GET",
          dataType:"jsonp",
          url:"https://webapi.legistar.com/v1/nyc/EventDates/1?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ",
          success:function(meetings){
            if (meetings.length > 0){
              var oneDate = meetings[0].split("T")[0].split("-");
              var date = new Date(oneDate[0],(parseInt(oneDate[1])-1),oneDate[2])
              jQuery("#upcoming-stated").html("<strong>"+date.toLocaleDateString("en-US",{ weekday: 'long', month: 'long', day: 'numeric' })+"</strong>")
            } else {
              jQuery("#stated").remove();
            };
          }
        });
      </script>
      <script>
        /*--------------------------------------------------
          Upcoming Hearings jQuery
        --------------------------------------------------*/
        Date.prototype.stdTimezoneOffset = function() {
          var jan = new Date(this.getFullYear(), 0, 1);
          var jul = new Date(this.getFullYear(), 6, 1);
          return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
        }
        Date.prototype.dst = function() {
          return this.getTimezoneOffset() < this.stdTimezoneOffset();
        }
        var date;
        new Date().dst() ?  date = new Date(new Date().getTime() - 4 * 3600 * 1000) : date = new Date(new Date().getTime() - 5 * 3600 * 1000)
        var month31 = [1,3,5,7,8,10,12], month30 = [4,6,9,11], startDate, endDate, startYear = date.getFullYear(), startMonth = date.getMonth()+1, startDay = date.getDate(), nowHour = date.getUTCHours(), nowMinute = date.getUTCMinutes(), midDay, meetingHour, meetingMinute, endYear, endMonth, endDay, agendaLink;
        var addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
        if(startMonth === 12 && startDay === 31){ // if start day is NYE. Unlikely.
          endYear = startYear+1;
          endMonth = 1;
          endDay = 1;
        } else if (startYear%4 !== 0 && startMonth === 2 && startDay === 28){ //if last day of Feb in normal year
          endYear = startYear;
          endMonth = 3;
          endDay = 1;
        } else if (startYear%4 === 0 && startMonth === 2 && startDay === 29){ //if last day of Feb in leap year
          endYear = startYear;
          endMonth = 3;
          endDay = 1;
        } else if ((month31.indexOf(startMonth) !== -1) && startDay === 31){ //if start day is 31st day of month
          endYear = startYear;
          endMonth = startMonth+1;
          endDay = 1;
        } else if ((month30.indexOf(startMonth) !== -1) && startDay === 30){ //if start day is 30th day of month
          endYear = startYear;
          endMonth = startMonth+1;
          endDay = 1;
        } else { //any other day
          endYear = startYear;
          endMonth = startMonth;
          endDay = startDay+1;
        };

        startDate = startYear+"-"+addZero(startMonth)+"-"+addZero(startDay);
        endDate = endYear+"-"+addZero(endMonth)+"-"+addZero(endDay);
        jQuery.ajax({
          type:"GET",
          dataType:"jsonp",
          url:"https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventDate+ge+datetime%27"+startDate+"%27+and+EventDate+lt+datetime%27"+endDate+"%27&$orderby=EventTime+asc",
          success:function(hearings){
            function timeConverter(timeString){
              var hr = parseInt(timeString.split(" ")[0].split(":")[0]);
              var min = parseInt(timeString.split(" ")[0].split(":")[1]);
              var ampm = timeString.split(" ")[1];
              ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr * 100 : hr = (hr+12) * 100;
              return hr+min;
            };
            var sortedHearings = hearings.sort(function(a,b){
              return timeConverter(a.EventTime) - timeConverter(b.EventTime);
            });
            jQuery("#committee-loader").remove();
            if (hearings.length === 0){
              jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></div>");
            } else {
              sortedHearings.forEach(function(hearing){
                midDay = hearing.EventTime.split(" ")[1];
                meetingHour = parseInt(hearing.EventTime.split(" ")[0].split(":")[0]);
                meetingMinute = parseInt(hearing.EventTime.split(" ")[0].split(":")[1]);
                hearing.EventAgendaFile !== null ? agendaLink = hearing.EventAgendaFile : agendaLink = "#";
                midDay === "PM" && meetingHour !== 12 ? meetingHour += 12 : meetingHour;
                if(hearing.EventAgendaStatusName.toLowerCase() !== "draft"){
                  if(hearing.EventAgendaStatusName.toLowerCase() === "deferred"){
                    jQuery("#front-page-hearings").append("<div class='columns column-block' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small><s>"+hearing.EventTime+"</s> Deferred</small><br><i class='fa fa-map-marker' aria-hidden='true'></i> <small>"+hearing.EventLocation+"</small></div>");
                  } else {
                    jQuery("#front-page-hearings").append("<div class='columns column-block' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small>"+hearing.EventTime+"</small><br><i class='fa fa-map-marker' aria-hidden='true'></i> <small>"+hearing.EventLocation+"</small></div>");
                  };
                };
              });
              if (jQuery("#front-page-hearings").children().length === 0){
                jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></div>");
              };
            };
          }
        });

        /*--------------------------------------------------
          Load Flickr API Response to Slick Slider
        --------------------------------------------------*/
        function jsonFlickrApi(json) {
          jQuery.each(json.photos.photo, function(i, pic) {
            jQuery(".featured-carousel").append("<div class='carousel-images'><a href='https://www.flickr.com/photos/nyccouncil/"+pic.id+"/' target='_blank'><div class='pic-title'>"+pic.title.split("-")[0]+"</div><img class='slider-image' src='https://c1.staticflickr.com/"+pic.farm+"/"+pic.server+"/"+pic.id+"_"+pic.secret+"_z.jpg'/></div>");
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
            "tags":"featured",
            "tag_mode": "any",
          }
        });

        jQuery(window).on("load", function() {
          jQuery(".pic-title").each(function(){$(this).width(($(this).parent().children().last().width()-10))})
          jQuery('.featured-carousel').show().slick({
            // adaptiveHeight: true,
            arrows: false,
            autoplay: true,
            autoplaySpeed:2500,
            cssEase: 'linear',
            dots: false,
            fade: true,
            infinite: true,
            pauseOnFocus: false,
            pauseOnHover: false,
            speed: 1000,
          });
          jQuery(".slider-image").width("100%");
        });
      </script>
      <script>
      //Pop up for subscribe
        function setCookie(key, value) {
          var expires = new Date();
          expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
          document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
        }

        function getCookie(key) {
          var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
          return keyValue ? keyValue[2] : null;
        }

        if(getCookie("subscribe_shown") === null){
          //Create cookie if none exists
          setCookie("subscribe_shown",true);
          //Display pop up to go to subscribe page
        };
      </script>
    </div>

  </div>

<?php get_footer(); ?>
