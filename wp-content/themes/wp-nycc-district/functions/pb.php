<?php

// PB Ballot Items
function nycc_pb_ballot_item_post_type() {
  register_post_type( 'nycc_pb_ballot_item',
    array('labels' => array(
      'name' => __('PB Ballot', 'nycc'),
      'singular_name' => __('Ballot Item', 'nycc'),
      'all_items' => __('All Ballot Items', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Ballot Item', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Ballot Item', 'nycc'),
      'new_item' => __('New Ballot Item', 'nycc'),
      'view_item' => __('View Ballot Item', 'nycc'),
      'search_items' => __('Search Ballot Items', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'Participatory Budgeting Ballot Items', 'nycc' ),
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => true,
      'show_ui' => true,
      'show_in_nav_menus'=> false,
      'query_var' => false,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-yes',
      'rewrite'  => array( 'slug' => 'pb', 'with_front' => false ),
      'has_archive' => true,
      'capability_type' => 'page',
      'hierarchical' => false,
      'supports' => array('title', 'editor', 'page-attributes'),
      'taxonomies' => array('pbtags')
     )
  );
  register_taxonomy( 'pbtags',
    array('nycc_pb_ballot_item'),
    array('hierarchical' => false,
      'labels' => array(
        'name' => __( 'PB Categories', 'nycc' ),
        'singular_name' => __( 'PB Category', 'nycc' ),
        'search_items' =>  __( 'Search PB Categories', 'nycc' ),
        'all_items' => __( 'All PB Categories', 'nycc' ),
        'edit_item' => __( 'Edit PB Category', 'nycc' ),
        'update_item' => __( 'Update PB Category', 'nycc' ),
        'add_new_item' => __( 'Add New PB Category', 'nycc' ),
        'new_item_name' => __( 'New PB Category Name', 'nycc' ),
        'parent_item'       => __( 'Parent PB Category' ),
        'parent_item_colon' => __( 'Parent PB Category:' ),
        'menu_name'         => __( 'Categories' ),
      ),
      'show_admin_column' => true,
      'show_ui' => true,
      'query_var' => true,
      'show_in_nav_menus' => false,
    )
  );
}
add_action( 'init', 'nycc_pb_ballot_item_post_type');

function add_pb_ballot_item_metaboxes() {
  add_meta_box('nycc_pb_ballot_item_meta', 'Ballot Item Meta', 'nycc_pb_ballot_item_meta', 'nycc_pb_ballot_item', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_pb_ballot_item_metaboxes' );

function nycc_pb_ballot_item_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_pb_ballot_item_meta_noncename" id="nycc_pb_ballot_item_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Winning Project</th>
      <td>
        <select name="pb_ballot_item_winner">
          <option <?php echo ( get_post_meta($post->ID, 'pb_ballot_item_winner', true) == 'no' )? 'selected':''; ?> value="no">No</option>
          <option <?php echo ( get_post_meta($post->ID, 'pb_ballot_item_winner', true) == 'yes')? 'selected':''; ?> value="yes">Yes</option>
        </select>
      </td>
    </tr>

  </table>
<?php
}

function save_nycc_pb_ballot_item_meta($post_id, $post) {
  if ( !wp_verify_nonce( $_POST['nycc_pb_ballot_item_meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
  }
  if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;
  $pb_ballot_item_meta['pb_ballot_item_winner'] = $_POST['pb_ballot_item_winner'];
  foreach ($pb_ballot_item_meta as $key => $value) {
    if( $post->post_type == 'revision' ) return;
    $value = implode(',', (array)$value);
    if(get_post_meta($post->ID, $key, FALSE)) {
      update_post_meta($post->ID, $key, $value);
    } else {
      add_post_meta($post->ID, $key, $value);
    }
    if(!$value) delete_post_meta($post->ID, $key);
  }
}
add_action('save_post', 'save_nycc_pb_ballot_item_meta', 1, 2);


// Redirect single post view back to archive
function nycc_redirect_nycc_pb_ballot_item() {
    $queried_post_type = get_query_var('post_type');
    if ( is_single() && 'nycc_pb_ballot_item' ==  $queried_post_type ) {
        wp_redirect( get_post_type_archive_link('nycc_pb_ballot_item'), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'nycc_redirect_nycc_pb_ballot_item' );


// PB Vote Sites
function nycc_pb_vote_site_post_type() {
  register_post_type( 'nycc_pb_vote_site',
    array('labels' => array(
      'name' => __('PB Vote Sites', 'nycc'),
      'singular_name' => __('Vote Site', 'nycc'),
      'all_items' => __('All Vote Sites', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Vote Site', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Vote Site', 'nycc'),
      'new_item' => __('New Vote Site', 'nycc'),
      'view_item' => __('View Vote Site', 'nycc'),
      'search_items' => __('Search Vote Sites', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'Participatory Budgeting Vote Sites', 'nycc' ),
      'public' => false,
      'publicly_queryable' => false,
      'exclude_from_search' => true,
      'show_ui' => true,
      'show_in_nav_menus'=> false,
      'show_in_menu' => true,
      'query_var' => false,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-location-alt',
      'rewrite'  => false,
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => false,
      'supports' => array( 'title', 'editor', 'page-attributes')
     )
  );
}
add_action( 'init', 'nycc_pb_vote_site_post_type');

function add_pb_vote_site_metaboxes() {
  add_meta_box('nycc_pb_vote_site_meta', 'Vote Site Meta', 'nycc_pb_vote_site_meta', 'nycc_pb_vote_site', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_pb_vote_site_metaboxes' );

function nycc_pb_vote_site_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_pb_vote_site_meta_noncename" id="nycc_pb_vote_site_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $pb_vote_site_lat = get_post_meta($post->ID, 'pb_vote_site_lat', true);
  $pb_vote_site_lon = get_post_meta($post->ID, 'pb_vote_site_lon', true);
  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Latitude &amp; Longitude</th>
      <td>
        <input type="number" step="0.0000001" name="pb_vote_site_lat" value="<?php echo esc_attr( $pb_vote_site_lat ); ?>" placeholder="40.7058316" />
        <input type="number" step="0.0000001" name="pb_vote_site_lon" value="<?php echo esc_attr( $pb_vote_site_lon ); ?>" placeholder="-74.2581887" />
      </td>
    </tr>

  </table>
<?php
}

function save_nycc_pb_vote_site_meta($post_id, $post) {
  if ( !wp_verify_nonce( $_POST['nycc_pb_vote_site_meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
  }
  if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;
  $pb_vote_site_meta['pb_vote_site_lat'] = $_POST['pb_vote_site_lat'];
  $pb_vote_site_meta['pb_vote_site_lon'] = $_POST['pb_vote_site_lon'];
  foreach ($pb_vote_site_meta as $key => $value) {
    if( $post->post_type == 'revision' ) return;
    $value = implode(',', (array)$value);
    if(get_post_meta($post->ID, $key, FALSE)) {
      update_post_meta($post->ID, $key, $value);
    } else {
      add_post_meta($post->ID, $key, $value);
    }
    if(!$value) delete_post_meta($post->ID, $key);
  }
}
add_action('save_post', 'save_nycc_pb_vote_site_meta', 1, 2);
