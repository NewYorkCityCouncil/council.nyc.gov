      </div><!-- end .site-container -->

      <footer class="site-footer">
        <div class="row">
          <div class="columns medium-7 large-5">
            <aside class="widget footer-widget">
              <h4 class="widget-title">We want to hear from you.</h4>
              <p>With your insight, the Council discovers ways to improve the City we all call home. So we're meeting New Yorkers where they are—online and in person. Keep your feedback coming!</p>
              <p>You can reach us via social media, email, paper mail, or at your district office. For issues specific to a neighborhood, it's best to contact the Council Member representing that community</p>
              <ul class="menu simple social-buttons">
                <li><a href="https://www.facebook.com/NYCCouncil"><img class="inline-icon large" src="http://localhost/~andy/wp-labs/wp-content/uploads/2016/02/social-icon-facebook.png"></a></li>
                <li><a href="https://twitter.com/NYCCouncil"><img class="inline-icon large" src="http://localhost/~andy/wp-labs/wp-content/uploads/2016/02/social-icon-twitter.png"></a></li>
                <li><a href="https://vine.co/NYCCouncil"><img class="inline-icon large" src="http://localhost/~andy/wp-labs/wp-content/uploads/2016/02/social-icon-vine.png"></a></li>
                <li><a href="https://instagram.com/NYCCouncil"><img class="inline-icon large" src="http://localhost/~andy/wp-labs/wp-content/uploads/2016/02/social-icon-instagram.png"></a></li>
                <li><a href="https://plus.google.com/u/0/104432794073374421060/posts"><img class="inline-icon large" src="http://localhost/~andy/wp-labs/wp-content/uploads/2016/02/social-icon-googleplus.png"></a></li>
              </ul>
            </aside>
          </div>
          <div class="columns medium-5 large-7">
            <div class="row">
              <div class="columns large-6">
                <aside class="widget footer-widget">
                  <h4 class="widget-title">Visit the Council</h4>
                  <p>We're located at <a href="https://www.google.com/maps/place/New+York+City+Hall/@40.7127744,-74.008253,17z/data=!3m1!4b1!4m2!3m1!1s0x89c258fda88cefb3:0x7f1e88758d210007"><strong>New York City Hall</strong> (map)</a>. Council Members each have an office at <a href="https://www.google.com/maps/place/250+Broadway,+New+York,+NY+10007/@40.7129838,-74.010099,17z/data=!4m7!1m4!3m3!1s0x89c258828f59541d:0x539864ce22092177!2s250+Broadway,+New+York,+NY+10007!3b1!3m1!1s0x89c258828f59541d:0x539864ce22092177"><strong>250&nbsp;Broadway</strong> (map)</a>, as well as offices in each of their districts.</p>
                </aside>
              </div>
              <div class="columns large-6">
                <aside class="widget footer-widget">
                  <h4 class="widget-title">Unsure about your issue?</h4>
                  <p><strong>Contact 311</strong>, New York City's <a href="http://www1.nyc.gov/311/index.page"><strong>website</strong></a> and phone number for government information and non-emergency services. Just dial 311 (from outside the five boroughs, dial  212‑NEW‑YORK).</p>
                </aside>
              </div>
            </div>
          </div>
        </div>
      </footer>

    </div><!-- end .sticky-wrapper -->

    <?php wp_footer(); ?>

    <?php get_template_part( 'map_scripts' ); ?>
    <?php if ( is_post_type_archive('nycc_pb_ballot_item') ) { get_template_part( 'pb-map-scripts' ); } ?>
    <?php if ( is_page_template( 'page-pbdistricts.php' ) ) { get_template_part( 'geolocate-scripts' ); } ?>

  </body>
</html>
