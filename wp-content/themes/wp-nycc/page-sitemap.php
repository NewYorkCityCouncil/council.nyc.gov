<?php /* Template Name: Site Map */ ?>

<?php get_header(); ?>

<div class="row">
  <div class="columns">
    <h2>Home</h2>
    <ul>
      <?php
        $sites = get_sites(array("site__in"=>array(1), 'number' => 1000000));
        foreach ( $sites as $site ) {
          $ID = $site->blog_id;
          echo "<li class='members'><strong style='text-decoration:underline;'><a href='https://" . $site->domain . $site->path . "'>NYC Council (Homepage)</a></strong></li><ul style='list-style-type:none;'><li><strong>Pages:</strong></li><ul style='list-style-type:square;'>";
          switch_to_blog($ID);
          $site_pages = get_pages();
          foreach ( $site_pages as $site_page) {
            echo "<li><a href='https://" . $site->domain . $site->path . $site_page->post_name . "'>" . $site_page->post_title . "</a></li>";
          };
          
          $site_posts = get_posts( array('numberposts' => -1) );
          if ( count($site_posts) == 0 ) {
            echo "</ul></ul><br>";
          } else {
            echo "</ul><br><li><strong>News Posts / Blogs / Press Releases:</strong></li><ul style='list-style-type:square;'>";
            foreach ( $site_posts as $site_post) {
              echo "<li><a href='https://" . $site->domain . $site->path . $site_post->post_name . "'>" . $site_post->post_title . "</a></li>";
            };
            echo "</ul></ul><br>";
          };          
        };
      ?>
    </ul>

    <h2>Active Council Members</h2>
    <ul>
      <?php
        $sites = get_sites(array("site__not_in"=>array(1,53,54,55,56,57,58,72,3,5,9,14,19,22,29,31,42,44,45),'number' => 1000000));
        foreach ( $sites as $site ) {
          $ID = $site->blog_id;
          $pretty = substr($site->path, 1, -1);
          $pretty1 = explode("-",$pretty);
          $pretty2 = implode(" ",$pretty1);
          echo "<li class='members'><strong style='text-decoration:underline;'><a href='https://" . $site->domain . $site->path . "'>". ucwords($pretty2) . "</a></strong></li><ul style='list-style-type:none;'><li><strong>Pages:</strong></li><ul style='list-style-type:square;'>";
          switch_to_blog($ID);
          $site_pages = get_pages();
          foreach ( $site_pages as $site_page) {
            echo "<li><a href='https://" . $site->domain . $site->path . $site_page->post_name . "'>" . $site_page->post_title . "</a></li>";
          };
          
          $site_posts = get_posts( array('numberposts' => -1) );
          if ( count($site_posts) == 0 ) {
            echo "</ul></ul><br>";
          } else {
            echo "</ul><br><li><strong>News Posts / Blogs / Press Releases:</strong></li><ul style='list-style-type:square;'>";
            foreach ( $site_posts as $site_post) {
              echo "<li><a href='https://" . $site->domain . $site->path . $site_post->post_name . "'>" . $site_post->post_title . "</a></li>";
            };
            echo "</ul></ul><br>";
          };          
        };
      ?>
    </ul>

    <h2>Past Council Members</h2>
    <ul>
      <?php
        $sites = get_sites(array("site__in"=>array(3,5,9,14,19,22,29,31,42,44,45),'number' => 1000000));
        foreach ( $sites as $site ) {
          $ID = $site->blog_id;
          $pretty = substr($site->path, 1, -1);
          $pretty1 = explode("-",$pretty);
          $pretty2 = implode(" ",$pretty1);
          echo "<li class='members'><strong style='text-decoration:underline;'><a href='https://" . $site->domain . $site->path . "'>". ucwords($pretty2) . "</a></strong></li><ul style='list-style-type:none;'><li><strong>Pages:</strong></li><ul style='list-style-type:square;'>";
          switch_to_blog($ID);
          $site_pages = get_pages();
          foreach ( $site_pages as $site_page) {
            echo "<li><a href='https://" . $site->domain . $site->path . $site_page->post_name . "'>" . $site_page->post_title . "</a></li>";
          };
          
          $site_posts = get_posts( array('numberposts' => -1) );
          if ( count($site_posts) == 0 ) {
            echo "</ul></ul><br>";
          } else {
            echo "</ul><br><li><strong>News Posts / Blogs / Press Releases:</strong></li><ul style='list-style-type:square;'>";
            foreach ( $site_posts as $site_post) {
              echo "<li><a href='https://" . $site->domain . $site->path . $site_post->post_name . "'>" . $site_post->post_title . "</a></li>";
            };
            echo "</ul></ul><br>";
          };          
        };
      ?>
    </ul>

    <h2>Divisions</h2>
    <ul>
      <?php
        $sites = get_sites(array("site__in"=>array(53,54,55,56,57,58,72), 'number' => 1000000));
        foreach ( $sites as $site ) {
          $ID = $site->blog_id;
          if ($site->blog_id == "58") {
            echo "<li class='members'><strong style='text-decoration:underline;'><a href='https://" . $site->domain . $site->path . "'>Partcipatory Budgeting</a></strong></li>";
          } else {
            $pretty = substr($site->path, 1, -1);
            $pretty1 = explode("-",$pretty);
            $pretty2 = implode(" ",$pretty1);
            echo "<li class='members'><strong style='text-decoration:underline;'><a href='https://" . $site->domain . $site->path . "'>". ucwords($pretty2) . "</a></strong></li>";
          };
          echo "<ul style='list-style-type:none;'><li><strong>Pages:</strong></li><ul style='list-style-type:square;'>";
          switch_to_blog($ID);
          $site_pages = get_pages();
          foreach ( $site_pages as $site_page) {
            echo "<li><a href='https://" . $site->domain . $site->path . $site_page->post_name . "'>" . $site_page->post_title . "</a></li>";
          };
          
          $site_posts = get_posts( array('numberposts' => -1) );
          if ( count($site_posts) == 0 ) {
            echo "</ul></ul><br>";
          } else {
            echo "</ul><br><li><strong>News Posts / Blogs / Press Releases:</strong></li><ul style='list-style-type:square;'>";
            foreach ( $site_posts as $site_post) {
              echo "<li><a href='https://" . $site->domain . $site->path . $site_post->post_name . "'>" . $site_post->post_title . "</a></li>";
            };
            echo "</ul></ul><br>";
          };          
        };
      ?>
    </ul>
  </div>
</div>

<?php get_footer(); ?>
