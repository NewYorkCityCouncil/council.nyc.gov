<?php

function nycc_initiative_post_type() {
  register_post_type( 'nycc_initiative',
    array('labels' => array(
      'name' => __('Initiatives', 'nycc'),
      'singular_name' => __('Initiative', 'nycc'),
      'all_items' => __('All Initiatives', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Initiative', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Initiative', 'nycc'),
      'new_item' => __('New Initiative', 'nycc'),
      'view_item' => __('View Initiative', 'nycc'),
      'search_items' => __('Search Initiatives', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'NYC Council Initiatives', 'nycc' ),
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_nav_menus'=> false,
      'query_var' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-lightbulb',
      'rewrite'  => array( 'slug' => 'initiatives', 'with_front' => false ),
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => false,
      'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
      'register_meta_box_cb' => 'add_initiative_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_initiative_post_type');

function add_initiative_metaboxes() {
  add_meta_box('nycc_initiative_meta', 'Meta', 'nycc_initiative_meta', 'nycc_initiative', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_initiative_metaboxes' );

function nycc_initiative_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_initiative_meta_noncename" id="nycc_initiative_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $initiative_link_url = get_post_meta($post->ID, 'initiative_link_url', true);
  $initiative_link_text = get_post_meta($post->ID, 'initiative_link_text', true);
  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Initiative URL</th>
      <td>
        <input type="text" name="initiative_link_url" value="<?php echo esc_attr( $initiative_link_url ); ?>" placeholder="http://..." class="regular-text" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Button Text</th>
      <td>
        <input type="text" name="initiative_link_text" value="<?php echo esc_attr( $initiative_link_text ); ?>" class="regular-text" />
      </td>
    </tr>

  </table>
<?php
}

function save_nycc_initiative_meta($post_id, $post) {
  if ( !wp_verify_nonce( $_POST['nycc_initiative_meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
  }
  if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;
  $initiative_meta['initiative_link_url'] = $_POST['initiative_link_url'];
  $initiative_meta['initiative_link_text'] = $_POST['initiative_link_text'];
  foreach ($initiative_meta as $key => $value) { // Cycle through the $events_meta array!
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
add_action('save_post', 'save_nycc_initiative_meta', 1, 2);
