<?php

function nycc_caucus_post_type() {
  register_post_type( 'nycc_caucus',
    array('labels' => array(
      'name' => __('Caucuses', 'nycc'),
      'singular_name' => __('Caucus', 'nycc'),
      'all_items' => __('All Caucuses', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Caucus', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Caucus', 'nycc'),
      'new_item' => __('New Caucus', 'nycc'),
      'view_item' => __('View Caucus', 'nycc'),
      'search_items' => __('Search Caucuses', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'NYC Council Caucuses', 'nycc' ),
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_nav_menus'=> false,
      'query_var' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-groups',
      'rewrite'  => array( 'slug' => 'caucuses', 'with_front' => false ),
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => false,
      'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
      'register_meta_box_cb' => 'add_caucus_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_caucus_post_type');


function add_caucus_metaboxes() {
  add_meta_box('nycc_caucus_members', 'Caucus Members', 'nycc_caucus_members', 'nycc_caucus', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_caucus_metaboxes' );

function nycc_caucus_members() {
  global $post;
  echo '<input type="hidden" name="nycc_caucus_members_noncename" id="nycc_caucus_members_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  // $council_member_1 = get_post_meta($post->ID, 'council_member_1', true);
  // Instead of setting each one, let's do a loop...
  for($i=1; $i <= 51; $i++) {
      ${'council_member_' . $i} = get_post_meta($post->ID, 'council_member_' . $i, true);
  }
  ?>
  <table class="form-table">
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
      echo '<ul>';
        while ( $list_districts->have_posts() ) : $list_districts->the_post();

        // Get the District meta
        $current_member_site_ID = get_post_meta($post->ID, 'current_member_site', true);

        if ($current_member_site_ID) {
          // Switch to the current Member's site
          switch_to_blog($current_member_site_ID);

          // Get the Member's site meta
          $number = get_blog_option($current_member_site_ID,'council_district_number');
          $cm_name = get_blog_option($current_member_site_ID,'council_member_name' );

          // Set the variable for the committee's wp_options key
          $cm_number = 'council_member_' . $number;

          // Add the Member's table row
          ?>
          <tr valign="top">
            <th scope="row"><?php echo $cm_name; ?></th>
            <td>
              <select name="<?php echo $cm_number; ?>">
                <option <?php echo (${'council_member_' . $number} == '')? 'selected':''; ?> value=""> Non-Member</option>
                <option <?php echo (${'council_member_' . $number} == 'member')? 'selected':''; ?> value="member"> Member</option>
                <option <?php echo (${'council_member_' . $number} == 'chair')? 'selected':''; ?> value="chair"> Chair</option>
                <option <?php echo (${'council_member_' . $number} == 'co_chair')? 'selected':''; ?> value="co_chair"> Co-Chair</option>
                <option <?php echo (${'council_member_' . $number} == 'vice_chair')? 'selected':''; ?> value="vice_chair"> Vice Chair</option>
                <option <?php echo (${'council_member_' . $number} == 'vice_co_chair')? 'selected':''; ?> value="vice_co_chair"> Vice Co-Chair</option>
                <option <?php echo (${'council_member_' . $number} == 'acting_co_chair')? 'selected':''; ?> value="acting_co_chair"> Acting Co-Chair</option>
                <option <?php echo (${'council_member_' . $number} == 'secretary')? 'selected':''; ?> value="secretary"> Secretary</option>
                <option <?php echo (${'council_member_' . $number} == 'treasurer')? 'selected':''; ?> value="treasurer"> Treasurer</option>
                <option <?php echo (${'council_member_' . $number} == 'speaker')? 'selected':''; ?> value="speaker"> Speaker</option>
                <option <?php echo (${'council_member_' . $number} == 'deputy_speaker')? 'selected':''; ?> value="deputy_speaker"> Deputy Speaker</option>
                <option <?php echo (${'council_member_' . $number} == 'majority_whip')? 'selected':''; ?> value="majority_whip"> Majority Whip</option>
              </select>
            </td>
          </tr>
          <?php

          restore_current_blog();
          wp_reset_postdata();
        }

        endwhile;
        wp_reset_postdata();
        echo '</ul>';
    }

    ?>
  </table>
<?php
}

function save_nycc_caucus_members($post_id, $post) {
  if ( isset($_POST['nycc_caucus_members_noncename']) )  {
    if ( !wp_verify_nonce( $_POST['nycc_caucus_members_noncename'], plugin_basename(__FILE__) )) {
      return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
      return $post->ID;
    for($i=1; $i <= 51; $i++) {
        $caucus_members['council_member_' . $i] = $_POST['council_member_' . $i];
    }
    foreach ($caucus_members as $key => $value) { // Cycle through the $events_meta array!
      if( $post->post_type == 'revision' ) return; // Don't store custom data twice
      $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
      if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
        update_post_meta($post->ID, $key, $value);
      } else { // If the custom field doesn't have a value
        add_post_meta($post->ID, $key, $value);
      }
      if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
  }
}
add_action('save_post', 'save_nycc_caucus_members', 1, 2);
