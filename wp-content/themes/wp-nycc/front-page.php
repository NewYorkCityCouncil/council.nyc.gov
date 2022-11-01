<?php get_header(); ?>
  <div class="row homepage-carousel-container">
    <div class="columns" style="padding: 0;">
      <?php echo do_shortcode('[recent_post_slider design="design-4" limit="5" show_category_name="false" post_type="nycc_feature" dots="true" show_author="false" speed="3000" media_size="full"]'); ?>
      <!-- <a href=""> -->
    </div>
  </div>
  <div class="row view-featured-container"><div class="columns"><a class="button" style="margin: 0" href="/past-featured-content/">View Past Featured Content</a></div></div>
  <a class="anchor" id="hearings"></a>
  <?php include 'hearings.php';?>
  <div class="container" style="background-color:#2F56A6; padding: 2rem 0;">
    <div class="row">
      <div class="columns">
        <h2 style="color: #FFF">Live Video Feed</h2>
        <div class="row">
          <div class="columns medium-9">
            <div class="tabs-content" data-tabs-content="live-videos" style="border:0;">
              <div class="tabs-panel is-active" data-auto-focus="true" id="location-chambers" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe id="chamber-hearing" title="Livestream video in the Council Chambers at City Hall" src="https://councilnyc.viebit.com/live/?v=f04a2b84-6077-488f-80eb-0b714d18417e&amp;s=false" width="480" height="360" frameborder="0" scrolling="no" seamless="" allowfullscreen=""></iframe>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="location-committee-room" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Livestream video in the Committee Room at City Hall" src="https://councilnyc.viebit.com/live/?v=af1416e7-10aa-447b-825c-b637e4d67b91&amp;s=false" width="480" height="360" frameborder="0" scrolling="no" seamless="" allowfullscreen=""></iframe>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="location-14th-floor" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Livestream video in the hearing room on the 14th floor of 250 Broadway" src="https://councilnyc.viebit.com/live/?v=fb60e527-1bc1-4f3a-b055-bd281e0bcd59&amp;s=false" width="480" height="360" frameborder="0" scrolling="no" seamless="" allowfullscreen=""></iframe>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="location-16th-floor" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Livestream video in the hearing room on the 16th floor of 250 Broadway" src="https://councilnyc.viebit.com/live/?v=20ead200-e0a0-4086-ba86-4b683af69b96&amp;s=false" width="480" height="360" frameborder="0" scrolling="no" seamless="" allowfullscreen=""></iframe>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true"  id="virtual-room-1" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Virtual Room 1 Livestream Video" id="ls_embed_1591111682" src="https://livestream.com/accounts/17376047/events/9081844/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591111682" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="virtual-room-2" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Virtual Room 2 Livestream Video" id="ls_embed_1591111981" src="https://livestream.com/accounts/17376047/events/9106772/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591111981" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="virtual-room-3" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Virtual Room 3 Livestream Video" id="ls_embed_1591112063" src="https://livestream.com/accounts/17376047/events/9132761/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591112063" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="virtual-room-4" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Virtual Room 3 (Español) Livestream Video" id="ls_embed_1591112206" src="https://livestream.com/accounts/17376047/events/9152927/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591112206" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="virtual-room-5" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Virtual Room 5 Livestream Video" id="ls_embed_1591112291" src="https://livestream.com/accounts/17376047/events/9152928/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591112291" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="virtual-room-6" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Virtual Room 6 Livestream Video" id="ls_embed_1591112374" src="https://livestream.com/accounts/17376047/events/9152931/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591112374" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
              <div class="tabs-panel" data-auto-focus="true" id="press-room" style="padding:0;">
                <div class="flex-video widescreen" style="margin-bottom:0;">
                  <iframe title="Press Room Livestream Video" id="ls_embed_1591232136" src="https://livestream.com/accounts/17376047/events/9160781/player?width=960&amp;height=540&amp;enableInfoAndActivity=true&amp;defaultDrawer=feed&amp;autoPlay=false&amp;mute=false" width="960" height="540" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
                  <script type="text/javascript" data-embed_id="ls_embed_1591232136" src="https://livestream.com/assets/plugins/referrer_tracking.js"></script>
                </div>
              </div>
            </div>
            <p class="text-small text-center" style="color: #FFF;"><em>If videos streams are not active, <a href="http://legistar.nyccouncilstg.wpengine.com/Calendar.aspx?Mode=This%20Month" style="color: #FFF;">please check our calendar</a>.</em></p>
          </div>
          <div class="columns medium-3 live-stream-tabs-column">
            <h4 class="sans-serif subheading">Select a stream:</h4>
            <ul class="vertical tabs" data-tabs id="live-videos">
              <li class="tabs-title is-active" style="text-align:center; padding: 0.7rem"><a href="#location-chambers" aria-selected="true"><strong>City Hall, Council Chambers</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#location-committee-room"><strong>City Hall, Committee Room</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#location-14th-floor"><strong>250 Broadway, 14th Fl<span class="show-for-xlarge">oor</span> Hearing Room</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#location-16th-floor"><strong>250 Broadway, 16th Fl<span class="show-for-xlarge">oor</span> Hearing Room</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#virtual-room-1"><strong>Virtual Room 1</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#virtual-room-2"><strong>Virtual Room 2</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#virtual-room-3"><strong>Virtual Room 3</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#virtual-room-4"><strong>Virtual Room 4</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#virtual-room-5"><strong>Virtual Room 5</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#virtual-room-6"><strong>Virtual Room 6</strong></a></li>
              <li class="tabs-title" style="text-align:center; padding: 0.7rem"><a href="#press-room"><strong>Press Room</strong></a></li>
              <li><a class="button past-hearings small expanded" style="margin-bottom:0;" href="https://legistar.nyccouncilstg.wpengine.com/Calendar.aspx"><strong>Video archive of past hearings</strong></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="columns">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
          <?php the_content(); ?>
        </article>
      <?php endwhile; endif; ?>
    </div>

    <!-- New content
    <div class="columns medium-11 medium-centered">
      <hr>
      <h2>On Social Media</h2>
      <div class="columns medium-5 speaker-council-twitter-feed" style="overflow: scroll; height: 550px; box-shadow: 0px 0px 10px grey;">
        <a class="twitter-timeline" data-height="600" data-tweet-limit="10" data-aria-polite="assertive" href="https://twitter.com/NYCSpeakerAdams?ref_src=twsrc%5Etfw">Tweets by NYCSpeakerAdams</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
      </div>
      <div class="columns medium-5 medium-offset-2 speaker-council-twitter-feed" style="overflow: scroll; height: 550px; box-shadow: 0px 0px 10px grey;">
        <a class="twitter-timeline" data-height="600" data-tweet-limit="10" data-aria-polite="assertive" href="https://twitter.com/NYCCouncil?ref_src=twsrc%5Etfw">Tweets by NYCCouncil</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
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
      </script> -->
      
      <script>
        /*--------------------------------------------------
          Load Flickr API Response to Slick Slider
        --------------------------------------------------*/
        /*
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
            pauseOnFocus: true,
            pauseOnHover: true,
            speed: 1000,
          });
          jQuery(".slider-image").width("100%");
          jQuery(".pic-title").each(function(){jQuery(this).width((jQuery(this).parent().children().last().width()-10))})
        });
        */
      </script>
      <?php get_footer(); ?>
    </div>
  </div>
