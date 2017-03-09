<?php

function nycc_feature_post_type() {
  register_post_type( 'nycc_feature',
    array('labels' => array(
      'name' => __('Features', 'nycc'),
      'singular_name' => __('Feature', 'nycc'),
      'all_items' => __('All Features', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Feature', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Feature', 'nycc'),
      'new_item' => __('New Feature', 'nycc'),
      'view_item' => __('View Feature', 'nycc'),
      'search_items' => __('Search Features', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'NYC Council Features', 'nycc' ),
      'public' => false,
      'publicly_queryable' => false,
      'exclude_from_search' => true,
      'show_ui' => true,
      'show_in_nav_menus'=> false,
      'query_var' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-awards',
      'rewrite'  => array( 'slug' => 'features', 'with_front' => false ),
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => false,
      'supports' => array( 'title', 'thumbnail', 'page-attributes'),
      'register_meta_box_cb' => 'add_feature_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_feature_post_type');

function add_feature_metaboxes() {
  add_meta_box('nycc_feature_meta', 'The Link', 'nycc_feature_meta', 'nycc_feature', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_feature_metaboxes' );

function nycc_feature_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_feature_meta_noncename" id="nycc_feature_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $feature_link_url = get_post_meta($post->ID, 'feature_link_url', true);
  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">URL</th>
      <td>
        <input type="text" name="feature_link_url" value="<?php echo esc_attr( $feature_link_url ); ?>" placeholder="http://..." class="regular-text" />
      </td>
    </tr>

  </table>
<?php
}

function save_nycc_feature_meta($post_id, $post) {
  if ( isset($_POST['nycc_feature_meta_noncename']) ) {
    if ( !wp_verify_nonce( $_POST['nycc_feature_meta_noncename'], plugin_basename(__FILE__) )) {
      return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
      return $post->ID;
    $feature_meta['feature_link_url'] = $_POST['feature_link_url'];
    foreach ($feature_meta as $key => $value) { // Cycle through the $events_meta array!
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
add_action('save_post', 'save_nycc_feature_meta', 1, 2);
