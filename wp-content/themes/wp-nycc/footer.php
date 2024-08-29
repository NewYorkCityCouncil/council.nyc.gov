        </div><!-- end .site-container - line 127 ./header.php -->
      </main>
      <footer class="site-footer">
        <?php
          switch_to_blog(1);
          echo get_option('site_footer_content');
        ?>
        <div class="reveal" id="general_inquiries" data-reveal>
          <h4 class="header-small">General Inquiries</h4>
          <?php echo do_shortcode('[contact-form-7 id="968" title="General Inquiries"]'); ?>
          <button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
        </div>
        <?php restore_current_blog(); ?>
      </footer>

    </div><!-- end #sticky-wrapper - line 67 ./header.php -->

    <?php wp_footer(); ?>

    <?php get_template_part( 'map_scripts' ); ?>
    <?php if ( is_post_type_archive('nycc_pb_ballot_item') ) { get_template_part( 'pb-map-scripts' ); } ?>
    <?php if ( is_page_template('page-pbdistricts.php') ) { get_template_part( 'geolocate-scripts' ); } ?>

    <!-- Google Analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-68577323-2', 'auto');
      ga('send', 'pageview');
    </script>
    <script>
      /**
      * Function that captures a click on an outbound link in Analytics.
      * This function takes a valid URL string as an argument, and uses that URL string
      * as the event label. Setting the transport method to 'beacon' lets the hit be sent
      * using 'navigator.sendBeacon' in browser that support it.
      */
      var captureOutboundLink = function(url) {
        switch(url){
          case "http://www.nyc.gov/html/citycouncil/html/budget/expense_funding.shtml":
            ga('send', 'event', 'Outbound - Expense Funding', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          case "http://www.nyc.gov/html/citycouncil/html/budget/capital_funding.shtml":
            ga('send', 'event', 'Outbound - Capital Funding', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          case "https://data.cityofnewyork.us/City-Government/New-York-City-Council-Discretionary-Funding/4d7f-74pe":
            ga('send', 'event', 'Outbound - Discretionary Funding', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          case "http://council.nyc.gov/data/wp-content/uploads/sites/73/2019/08/growing-food-equity-1.pdf":
            ga('send', 'event', 'SOC Food Equity Report', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          case "http://council.nyc.gov/data/wp-content/uploads/sites/73/2020/01/Taxi-Medallion-Task-Force-Report-Final.pdf":
            ga('send', 'event', 'Taxi Medallion Task Force Report', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          case "http://council.nyc.gov/data/wp-content/uploads/sites/73/2020/01/FINAL-PAPER.pdf":
            ga('send', 'event', 'Homelessness Crisi Report', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          case "https://rnd.council.nyc.gov/expense_funding/":
            ga('send', 'event', 'Outbound - Expense Funding (RND)', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
          break;
            case "https://rnd.council.nyc.gov/capital_funding/":
            ga('send', 'event', 'Outbound - Capital Funding (RND)', 'click', url, {
              'transport': 'beacon',
              'hitCallback': function(){document.location = url;}
            });
            break;
          default:
            console.log("Unregistered GTM url")
        }
      }
    </script>
  </body>
</html>
