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
      <div class="row" aria-hidden="true">
        <div class="columns small-12">
          <h2>Featured at the Council</h2>
          <div class="featured-carousel" style="display:none;"></div>
        </div>
      </div>
      <div class="row">
        <div class="columns small-12">
          <h2>Featured Content</h2>
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
                  <h3 class="image-overlay-text header-small sans-serif"><?php the_title(); ?></h3>
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
        <a class="twitter-timeline" data-height="600" data-tweet-limit="20" data-aria-polite="assertive" href="https://twitter.com/NYCSpeakerCoJo?ref_src=twsrc%5Etfw">Tweets by NYCSpeakerCoJo</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>
      <div class="columns medium-5 medium-offset-2 speaker-council-twitter-feed">
        <a class="twitter-timeline" data-height="600" data-tweet-limit="20" data-aria-polite="assertive" href="https://twitter.com/NYCCouncil?ref_src=twsrc%5Etfw">Tweets by NYCCouncil</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>		
      <script async>
        function adjustiFrames(){
          jQuery("#twitter-widget-0").width(jQuery("#twitter-widget-0").parent().width());
          jQuery("#twitter-widget-1").width(jQuery("#twitter-widget-1").parent().width());
        };
        jQuery(window).on("load",function(){
          jQuery("#twitter-widget-0").attr("title", "Twitter feed from @NYC Speaker Co Jo")
          jQuery("#twitter-widget-1").attr("title", "Twitter feed from @NYC Council")
          setTimeout(function(){adjustiFrames()},1000);
          jQuery(window).on("orientationchange",function(){setTimeout(function(){adjustiFrames()},500)}).resize(adjustiFrames());
        });
      </script>
      <script>
        /*--------------------------------------------------
          Upcoming Stated Meetings jQuery
        --------------------------------------------------*/
        today = new Date();
        todays_date = {};
        todays_date.year = today.getFullYear();
        todays_date.month = (today.getMonth()+1) < 10 ? "0" + (today.getMonth()+1) : (today.getMonth()+1);
        todays_date.day = today.getDate() < 10 ? "0" + today.getDate() : today.getDate();
        jQuery.ajax({
          type:"GET",
          dataType:"jsonp",
          url:'https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventBodyId+eq+1+and+EventAgendaStatusId+eq+2+and+EventDate+ge+datetime%27'+todays_date.year+"-"+todays_date.month+"-"+todays_date.day+'%27&$orderby=EventDate+asc',
          success:function(meetings){
            if (meetings.length > 0){
              let oneDate = meetings[0]["EventDate"].split("T")[0].split("-");
              let date = new Date(oneDate[0],(parseInt(oneDate[1])-1),oneDate[2]);
              jQuery("#stated").html("Our next <strong>Stated Meeting</strong> will be held on <strong>"+date.toLocaleDateString("en-US",{ weekday: 'long', month: 'long', day: 'numeric' })+"</strong>.")
            } else {
              jQuery("#stated").remove();
            };
          }
        });
      </script>
      <script>
        /*--------------------------------------------------
          Upcoming Hearings jQuery
          After COVID Pandemics and normal operations resume comment in and delete the following lines:
          Comment In: 162 - 190, 196 - 205, 222, 246, 249, 254
          Delete: 141 - 154, 158 - 161, 206 - 219, 223, 226, 238 - 244, 247, 250,  255
        --------------------------------------------------*/
        Date.prototype.stdTimezoneOffset = function() {
          let jan = new Date(this.getFullYear(), 0, 1);
          let jul = new Date(this.getFullYear(), 6, 1);
          return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
        }
        Date.prototype.dst = function() {
          return this.getTimezoneOffset() < this.stdTimezoneOffset();
        }
        Date.prototype.getWeek = function() {
          let date = new Date(this.valueOf())
          date.setHours(0)
          date.setMinutes(0)
          date.setSeconds(0)
          date.setMilliseconds(0)
          let sunday = date.setDate(date.getDate() - date.getDay());
          date.setHours(23)
          date.setMinutes(59)
          date.setSeconds(59)
          date.setMilliseconds(999)
          let saturday = date.setDate(date.getDate() + 6);
          return [new Date(sunday), new Date(saturday)];
        }
        let addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
        let date;
        new Date().dst() ?  date = new Date(new Date().getTime() - 4 * 3600 * 1000) : date = new Date(new Date().getTime() - 5 * 3600 * 1000)
        let sunday = date.getWeek()[0]
        let saturday = date.getWeek()[1]
        let startDate = sunday.getFullYear()+"-"+addZero(sunday.getMonth()+1)+"-"+addZero(sunday.getDate());
        let endDate = saturday.getFullYear()+"-"+addZero(saturday.getMonth()+1)+"-"+addZero(saturday.getDate());
        // let month31 = [1,3,5,7,8,10,12], month30 = [4,6,9,11], startDate, endDate, startYear = date.getFullYear(), startMonth = date.getMonth()+1, startDay = date.getDate(), nowHour = date.getUTCHours(), nowMinute = date.getUTCMinutes(), midDay, meetingHour, meetingMinute, endYear, endMonth, endDay, agendaLink;
        // if(startMonth === 12 && startDay === 31){ // if start day is NYE. Unlikely.
        //   endYear = startYear+1;
        //   endMonth = 1;
        //   endDay = 1;
        // } else if (startYear%4 !== 0 && startMonth === 2 && startDay === 28){ //if last day of Feb in normal year
        //   endYear = startYear;
        //   endMonth = 3;
        //   endDay = 1;
        // } else if (startYear%4 === 0 && startMonth === 2 && startDay === 29){ //if last day of Feb in leap year
        //   endYear = startYear;
        //   endMonth = 3;
        //   endDay = 1;
        // } else if ((month31.indexOf(startMonth) !== -1) && startDay === 31){ //if start day is 31st day of month
        //   endYear = startYear;
        //   endMonth = startMonth+1;
        //   endDay = 1;
        // } else if ((month30.indexOf(startMonth) !== -1) && startDay === 30){ //if start day is 30th day of month
        //   endYear = startYear;
        //   endMonth = startMonth+1;
        //   endDay = 1;
        // } else { //any other day
        //   endYear = startYear;
        //   endMonth = startMonth;
        //   endDay = startDay+1;
        // };

        // startDate = startYear+"-"+addZero(startMonth)+"-"+addZero(startDay);
        // endDate = endYear+"-"+addZero(endMonth)+"-"+addZero(endDay);
        jQuery.ajax({
          type:"GET",
          dataType:"jsonp",
          url:"https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventDate+ge+datetime%27"+startDate+"%27+and+EventDate+lt+datetime%27"+endDate+"%27+and+tolower(EventAgendaStatusName)+ne+'draft'&$orderby=EventTime+asc",
          success:function(hearings){
            // function timeConverter(timeString){
            //   let hr = parseInt(timeString.split(" ")[0].split(":")[0]);
            //   let min = parseInt(timeString.split(" ")[0].split(":")[1]);
            //   let ampm = timeString.split(" ")[1];
            //   ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr : hr = (hr+12);
            //   return hr+min;
            // };
            // let sortedHearings = hearings.sort(function(a,b){
            //   return timeConverter(a.EventTime) - timeConverter(b.EventTime);
            // });
            function dateTimeConverter(dateString, timeString){
              let fullDate = dateString.split("T")[0].split("-")
              let year = parseInt(fullDate[0])
              let month = parseInt(fullDate[1])
              let date = parseInt(fullDate[2])
              let hr = parseInt(timeString.split(" ")[0].split(":")[0]);
              let min = parseInt(timeString.split(" ")[0].split(":")[1]);
              let ampm = timeString.split(" ")[1];
              ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr : hr = (hr+12) ;
              return new Date(year, month, date, hr, min, 00)
            };
            let sortedHearings = hearings.sort(function(a,b){
              return dateTimeConverter(a.EventDate, a.EventTime).getTime() - dateTimeConverter(b.EventDate, b.EventTime).getTime();
            });
            jQuery("#committee-loader").remove();
            if (hearings.length === 0){
              // jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></div>");
              jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO SCHEDULED HEARINGS THIS WEEK</em></div>");
            } else {
              sortedHearings.forEach(function(hearing){
                let hearingName = "<strong>"+hearing.EventBodyName+"</strong><br>"
                let meetingDate = hearing.EventDate.split("T")[0];
                let meetingDateFormat = new Date(meetingDate.split("-")[0], parseInt(meetingDate.split("-")[1])-1, meetingDate.split("-")[2])
                meetingDate = meetingDateFormat.toDateString().split(" ")
                meetingDate.pop()
                meetingDate[0] = meetingDate[0] + ","
                meetingDate = meetingDate.join(" ")
                midDay = hearing.EventTime.split(" ")[1];
                meetingHour = parseInt(hearing.EventTime.split(" ")[0].split(":")[0]);
                meetingMinute = parseInt(hearing.EventTime.split(" ")[0].split(":")[1]);
                hearing.EventAgendaFile !== null ? agendaLink = hearing.EventAgendaFile : agendaLink = "#";
                midDay === "PM" && meetingHour !== 12 ? meetingHour += 12 : meetingHour;
                if (hearing.EventComment !== null){
                  if(hearing.EventComment.toLowerCase().includes("jointly") && !hearing.EventLocation.toLowerCase().includes("-")){
                      hearingName += "<small><em>("+hearing.EventComment+")</em></small><br>"
                  } else if (hearing.EventComment.toLowerCase().includes("jointly") && hearing.EventLocation.toLowerCase().includes("-")){
                      return
                  }
                }
                if(hearing.EventAgendaStatusName.toLowerCase() === "deferred"){
                  // jQuery("#front-page-hearings").append("<div class='columns column-block' aria-label='hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small aria-label='start time'><s>"+hearing.EventTime+"</s> Deferred</small><br><i class='fas fa-map-marker-alt'></i> <small aria-label='location'>"+hearing.EventLocation+"</small></div>");
                  jQuery("#front-page-hearings").append("<div class='columns column-block' aria-label='hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'>"+hearingName+"</a><i class='fa fa-calendar' aria-hidden='true'></i> <small aria-label='hearing date'><s>"+meetingDate+"</s> Deferred</small><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small aria-label='start time'><s>"+hearing.EventTime+"</s> Deferred</small></div>");
                } else {
                  // jQuery("#front-page-hearings").append("<div class='columns column-block' aria-label='hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small aria-label='start time'>"+hearing.EventTime+"</small><br><i class='fas fa-map-marker-alt'></i> <small aria-label='location'>"+hearing.EventLocation+"</small></div>");
                  jQuery("#front-page-hearings").append("<div class='columns column-block' aria-label='hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'>"+hearingName+"</a><i class='fa fa-calendar' aria-hidden='true'></i> <small aria-label='hearing date'>"+meetingDate+"</small><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small aria-label='start time'>"+hearing.EventTime+"</small></div>");
                };
              });
              if (jQuery("#front-page-hearings").children().length === 0){
                // jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></div>");
                jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO SCHEDULED HEARINGS THIS WEEK</em></div>");
              };
            };
          }
        });

        /*--------------------------------------------------
          Load Flickr API Response to Slick Slider
        --------------------------------------------------*/
        function jsonFlickrApi(json) {
          jQuery.each(json.photos.photo, function(i, pic) {
            jQuery(".featured-carousel").append("<div class='carousel-images'><a href='https://www.flickr.com/photos/nyccouncil/"+pic.id+"/' target='_blank'><div class='pic-title'>"+pic.title.split("-")[0]+"</div><img class='slider-image' alt='"+pic.title+"' src='https://c1.staticflickr.com/"+pic.farm+"/"+pic.server+"/"+pic.id+"_"+pic.secret+"_z.jpg'/></div>");
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
          jQuery(".pic-title").each(function(){jQuery(this).width((jQuery(this).parent().children().last().width()-10))})
        });
      </script>
    </div>

  </div>

<?php get_footer(); ?>
