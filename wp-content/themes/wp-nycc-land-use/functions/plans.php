<?php

function nycc_land_use_plan_post_type() {
  register_post_type( 'nycc_land_use_plan',
    array('labels' => array(
      'name' => __('Plans', 'nycc'),
      'singular_name' => __('Plan', 'nycc'),
      'all_items' => __('All Plans', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Plan', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Plan', 'nycc'),
      'new_item' => __('New Plan', 'nycc'),
      'view_item' => __('View Plan', 'nycc'),
      'search_items' => __('Search Plans', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'Land Use Plans', 'nycc' ),
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_nav_menus'=> true,
      'query_var' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-building',
      'rewrite'  => array( 'slug' => 'plans', 'with_front' => false ),
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => true,
      'supports' => array( 'title', 'editor', 'excerpt', 'page-attributes'),
      'register_meta_box_cb' => 'add_land_use_plan_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_land_use_plan_post_type');

function add_land_use_plan_metaboxes() {
  add_meta_box('nycc_land_use_plan_meta', 'Event Meta', 'nycc_land_use_plan_meta', 'nycc_land_use_plan', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_land_use_plan_metaboxes' );

function nycc_land_use_plan_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_land_use_plan_meta_noncename" id="nycc_land_use_plan_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $land_use_plan_event_title = get_post_meta($post->ID, 'land_use_plan_event_title', true);
  $land_use_plan_event_date = get_post_meta($post->ID, 'land_use_plan_event_date', true);
  $land_use_plan_event_time = get_post_meta($post->ID, 'land_use_plan_event_time', true);
  $land_use_plan_event_location = get_post_meta($post->ID, 'land_use_plan_event_location', true);
  $land_use_plan_event_map_link = get_post_meta($post->ID, 'land_use_plan_event_map_link', true);
  $land_use_plan_event_description = get_post_meta($post->ID, 'land_use_plan_event_description', true);
  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Event Title</th>
      <td>
        <input type="text" name="land_use_plan_event_title" value="<?php echo esc_attr( $land_use_plan_event_title ); ?>" class="regular-text" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Date</th>
      <td>
        <input type="text" class="custom_date" name="land_use_plan_event_date" value="<?php echo esc_attr( $land_use_plan_event_date ); ?>"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Time</th>
      <td>
        <input type="time" name="land_use_plan_event_time" value="<?php echo esc_attr( $land_use_plan_event_time ); ?>" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Location</th>
      <td>
        <input type="text" name="land_use_plan_event_location" value="<?php echo esc_attr( $land_use_plan_event_location ); ?>" class="regular-text" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Map Link</th>
      <td>
        <input type="text" name="land_use_plan_event_map_link" value="<?php echo esc_attr( $land_use_plan_event_map_link ); ?>" class="regular-text" placeholder="http://..." />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Description</th>
      <td>
        <input type="text" name="land_use_plan_event_description" value="<?php echo esc_attr( $land_use_plan_event_description ); ?>" class="regular-text" />
      </td>
    </tr>

  </table>
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('.custom_date').datepicker({
        dateFormat : 'DD, MM d, yy'
      });
    });
  </script>
<?php
}

function save_nycc_land_use_plan_meta($post_id, $post) {
  if ( isset($_POST['nycc_land_use_plan_meta_noncename']) ) {
    if ( !wp_verify_nonce( $_POST['nycc_land_use_plan_meta_noncename'], plugin_basename(__FILE__) )) {
      return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
      return $post->ID;
    $land_use_meta['land_use_plan_event_title'] = $_POST['land_use_plan_event_title'];
    $land_use_meta['land_use_plan_event_date'] = $_POST['land_use_plan_event_date'];
    $land_use_meta['land_use_plan_event_time'] = $_POST['land_use_plan_event_time'];
    $land_use_meta['land_use_plan_event_location'] = $_POST['land_use_plan_event_location'];
    $land_use_meta['land_use_plan_event_map_link'] = $_POST['land_use_plan_event_map_link'];
    $land_use_meta['land_use_plan_event_description'] = $_POST['land_use_plan_event_description'];
    foreach ($land_use_meta as $key => $value) { // Cycle through the $events_meta array!
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
add_action('save_post', 'save_nycc_land_use_plan_meta', 1, 2);

// Enqueue scripts
function admin_land_use_enqueue() {
    global $post_type;
    if( 'nycc_land_use_plan' == $post_type )
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action( 'admin_print_scripts-post-new.php', 'admin_land_use_enqueue', 11 );
add_action( 'admin_print_scripts-post.php', 'admin_land_use_enqueue', 11 );
