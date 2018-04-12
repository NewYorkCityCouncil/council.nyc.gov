<?php /* Template Name: District Sidebar */ ?>

<?php get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <div class="row">

    <div class="columns">
      <header class="page-header">
        <h1 class="header-xxlarge">Participatory Budgeting</h1>
        <div class="header-menu"><?php nycc_pb_nav(); ?></div>
        <hr>
      </header>
    </div>

    <div class="columns large-7">

      <?php the_content(); ?>

    </div>
    <div class="columns large-5">

      <h4><?php echo esc_attr( get_option('pb_above_district_list') ); ?></h4>
      <div id="districts-list">
        <table class="full-width">
          <thead>
            <th><button class="button sort small secondary expanded" data-sort="sort-district">#</button></th>
            <th colspan="2"><button class="button sort small secondary expanded" data-sort="sort-member">Member</button></th>
            <th><button class="button sort small secondary expanded" data-sort="sort-borough">Borough</button></th>
          </thead>
          <tbody class="list">
            <?php
            $current_pb_cycle = get_post_custom_values( 'current_pb_cycle' )[0];
            $sites = get_sites();

            function sortScripts($a, $b) {
              $aID = $a->blog_id;
              switch_to_blog($aID);
              $aDistrict = get_blog_option($aID,'council_district_number');
              $bID = $b->blog_id;
              switch_to_blog($bID);
              $bDistrict = get_blog_option($bID,'council_district_number');
              restore_current_blog();
              return  $aDistrict - $bDistrict;
            };

            usort($sites, "sortScripts");

            foreach ( $sites as $site ) {

              $ID = $site->blog_id;
              switch_to_blog($ID);

              $number = get_blog_option($ID,'council_district_number');
              $name = get_blog_option($ID,'council_member_name');
              $thumbnail = get_blog_option($ID,'council_member_thumbnail' );
              $borough = get_blog_option($ID,'council_district_borough');

              if ( $number ) {
                $cycle = term_exists($current_pb_cycle,'pbcycle');
                if ($cycle !== 0 && $cycle !== null) {
                  $districtCycleLink = get_blogaddress_by_id($ID) . 'pb/' . $current_pb_cycle . '/';
                  ?>
                  <tr>
                    <td class="sort-district"><a class="button small expanded" href="<?php echo $districtCycleLink ?>"><strong><?php echo $number; ?></strong></a></td>
                    <td class="sort-member"><a href="<?php echo $districtCycleLink; ?>"><strong><?php echo $name; ?></strong></a></td>
                    <td><a href="<?php echo $districtCycleLink; ?>"><img class="inline-icon large" src="<?php echo $thumbnail; ?>" /></a></td>
                    <td class="sort-borough"><?php echo $borough; ?></td>
                  </tr>
                  <?php
                }
              }

              restore_current_blog();  
              
            }
            ?>
          </tbody>
        </table>
      </div>

      <br>

      <h4>Don't know your District?</h4>
      <form id="geolocate-district" class="callout secondary">
        <div class="input-group">
          <label class="input-group-label" for="myAddress">Address</label>
          <input class="input-group-field" type="text" name="myAddress" id="myAddress" />
        </div>
        <div class="input-group">
          <label class="input-group-label" for="myBorough">Borough</label>
          <select class="input-group-field" name="myBorough" id="myBorough">
            <option></option>
            <option value="Manhattan">Manhattan</option>
            <option value="Bronx">Bronx</option>
            <option value="Brooklyn">Brooklyn</option>
            <option value="Queens">Queens</option>
            <option value="Staten Island">Staten Island</option>
          </select>
        </div>
        <input type="submit" value="Find my district" class="button expanded secondary" />
      </form>
      <div id="geolocate-district-result"></div>

    </div>
  </div>

  <?php endwhile; endif; ?>

<?php get_footer(); ?>
