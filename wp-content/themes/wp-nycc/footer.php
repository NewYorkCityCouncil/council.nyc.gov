        </div><!-- end .site-container -->
      </main>
      <footer class="site-footer">
      <div class="reveal" id="hearingsModal" aria-labelledby="hearingsModalHeader" data-reveal="">
        <h1 id="hearingsModalHeader">Please be advised!</h1>
          <p class="lead">Pursuant to the State Open Meetings Law, as amended by Part WW of chapter 56 of the Laws of 2022, and New York City Council Resolution No. 204, adopted on June 2, 2022, and as authorized by such law and resolution by the continuing state disaster emergency declared by Governor Hochul, last renewed on May 15, 2022, and the local state of emergency declared by former Mayor De Blasio, last renewed by Mayor Adams on June 5, 2022, Speaker Adrienne Adams made a determination that, due to the risk to Members of the Council and the general public posed by COVID-19, the in-person participation requirements of the State Open Meetings Law are hereby suspended for all hearings on <strong>June 9, 2022</strong>, and for the hearings of the Committee on Land Use and its Subcommittees on <strong>June 14, 2022</strong>.</p>
          <button class="close-button" data-close="" aria-label="Close this alert" type="button"><span aria-hidden="true">×</span></button>
        </div>
        <script>
          jQuery(document).ready(() => {
            let modal = jQuery("#hearingsModal");
            let seen = sessionStorage.getItem("modalSeen");
            let modalToday = new Date();
            let modalCutoffDate = new Date(2022,5,15)
            if ((modalToday < modalCutoffDate) && !seen){
              modal.foundation("open");
              sessionStorage.setItem("modalSeen", true)
            };    
          });
        </script>
        <?php
        switch_to_blog(1);
        echo get_option('site_footer_content');
        ?>
        <div class="reveal" id="general_inquiries" data-reveal>
          <h4 class="header-small">General Inquiries</h4>
          <?php echo do_shortcode('[contact-form-7 id="968" title="General Inquiries"]'); ?>
          <button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
        </div>
        <?php
        restore_current_blog();
        ?>
      </footer>

    </div><!-- end .sticky-wrapper -->

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
        if(url === "http://www.nyc.gov/html/citycouncil/html/budget/expense_funding.shtml"){
          ga('send', 'event', 'Outbound - Expense Funding', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function(){document.location = url;}
          });
        } else if (url === "http://www.nyc.gov/html/citycouncil/html/budget/capital_funding.shtml"){
          ga('send', 'event', 'Outbound - Capital Funding', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function(){document.location = url;}
          });
        } else if (url === "https://data.cityofnewyork.us/City-Government/New-York-City-Council-Discretionary-Funding/4d7f-74pe"){
          ga('send', 'event', 'Outbound - Discretionary Funding', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function(){document.location = url;}
          });
        } else if (url === "http://council.nyc.gov/data/wp-content/uploads/sites/73/2019/08/growing-food-equity-1.pdf"){
          ga('send', 'event', 'SOC Food Equity Report', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function(){document.location = url;}
          });
        } else if (url === "http://council.nyc.gov/data/wp-content/uploads/sites/73/2020/01/Taxi-Medallion-Task-Force-Report-Final.pdf"){
          ga('send', 'event', 'Taxi Medallion Task Force Report', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function(){document.location = url;}
          });
        } else if (url === "http://council.nyc.gov/data/wp-content/uploads/sites/73/2020/01/FINAL-PAPER.pdf"){
          ga('send', 'event', 'Homelessness Crisi Report', 'click', url, {
            'transport': 'beacon',
            'hitCallback': function(){document.location = url;}
          });
        }
      }
    </script>
  </body>
</html>
