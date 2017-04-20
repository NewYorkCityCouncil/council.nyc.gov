      </div><!-- end .site-container -->

      <footer class="site-footer">
        <?php
        switch_to_blog(1);
        echo get_option('site_footer_content');
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

  </body>
</html>
