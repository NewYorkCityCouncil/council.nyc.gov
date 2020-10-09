<?php

function nycc_enp_events_post_type() {
  register_post_type( 'nycc_enp_events',
    array('labels' => array(
      'name' => __('Eventss', 'nycc'),
      'singular_name' => __('Events', 'nycc'),
      'all_items' => __('All Eventss', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Events', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Events', 'nycc'),
      'new_item' => __('New Events', 'nycc'),
      'view_item' => __('View Events', 'nycc'),
      'search_items' => __('Search Eventss', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'Events & Production Events', 'nycc' ),
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_nav_menus'=> true,
      'query_var' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-calendar-alt',
      'rewrite'  => array( 'slug' => 'events', 'with_front' => false ),
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => true,
      'supports' => array( 'title', 'editor', 'excerpt', 'page-attributes'),
      'register_meta_box_cb' => 'add_enp_events_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_enp_events_post_type');

function add_enp_events_metaboxes() {
  add_meta_box('nycc_enp_events_meta', 'Event Meta', 'nycc_enp_events_meta', 'nycc_enp_events', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_enp_events_metaboxes' );

function nycc_enp_events_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_enp_events_meta_noncename" id="nycc_enp_events_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $enp_events_event_title = get_post_meta($post->ID, 'enp_events_event_title', true);
  $enp_events_event_date = get_post_meta($post->ID, 'enp_events_event_date', true);
  $enp_events_event_time = get_post_meta($post->ID, 'enp_events_event_time', true);
  $enp_events_event_location = get_post_meta($post->ID, 'enp_events_event_location', true);
  $enp_events_event_code = get_post_meta($post->ID, 'enp_events_event_code', true);
  $enp_events_event_room = get_post_meta($post->ID, 'enp_events_event_room', true);
  ?>
  <table class="form-table">
    <!-- 
        "IRISH2020":[ DONE
			"Irish Heritage and Culture 2020", DONE
			"March 25, 2020", DONE
			"5:30 PM", DONE
			"Council Chambers",
			"New York City Hall @ New York, NY 10007",
			"http://council.nyc.gov/events/wp-content/uploads/sites/76/2020/02/IRISH2020-Flyer.jpg",
			"Irish Heritage and Culture 2020 Flyer",
			{
        		"English":"http://council.nyc.gov/events/wp-content/uploads/sites/76/2020/02/IRISH2020-English.pdf",
			}		
		],

     -->
    <tr valign="top">
      <th scope="row">Event Title</th>
      <td>
        <input type="text" name="enp_events_event_title" value="<?php echo esc_attr( $enp_events_event_title ); ?>" class="regular-text" placeholder="Irish Heritage and Culture 2020"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Code</th>
      <td>
        <input type="text" name="enp_events_event_code" value="<?php echo esc_attr( $enp_events_event_code ); ?>" class="regular-text" placeholder="IRISH2020" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Date</th>
      <td>
        <input type="text" class="custom_date" name="enp_events_event_date" value="<?php echo esc_attr( $enp_events_event_date ); ?>"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Time</th>
      <td>
        <input type="time" name="enp_events_event_time" value="<?php echo esc_attr( $enp_events_event_time ); ?>" placeholder="5:30 PM"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Room</th>
      <td>
        <input type="text" name="enp_events_event_room" value="<?php echo esc_attr( $enp_events_event_room ); ?>" class="regular-text" placeholder="Council Chamber"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Location</th>
      <td>
        <input type="text" name="enp_events_event_location" value="<?php echo esc_attr( $enp_events_event_location ); ?>" class="regular-text" placeholder="New York City Hall @ New York, NY 10007"/>
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

function save_nycc_enp_events_meta($post_id, $post) {
  if ( isset($_POST['nycc_enp_events_meta_noncename']) ) {
    if ( !wp_verify_nonce( $_POST['nycc_enp_events_meta_noncename'], plugin_basename(__FILE__) )) {
      return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
      return $post->ID;
    $enp_events_meta['enp_events_event_title'] = $_POST['enp_events_event_title'];
    $enp_events_meta['enp_events_event_date'] = $_POST['enp_events_event_date'];
    $enp_events_meta['enp_events_event_time'] = $_POST['enp_events_event_time'];
    $enp_events_meta['enp_events_event_location'] = $_POST['enp_events_event_location'];
    $enp_events_meta['enp_events_event_code'] = $_POST['enp_events_event_code'];
    $enp_events_meta['enp_events_event_room'] = $_POST['enp_events_event_room'];
    foreach ($enp_events_meta as $key => $value) { // Cycle through the $events_meta array!
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
add_action('save_post', 'save_nycc_enp_events_meta', 1, 2);

// Enqueue scripts
function admin_enp_enqueue() {
    global $post_type;
    if( 'nycc_enp_events' == $post_type )
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action( 'admin_print_scripts-post-new.php', 'admin_enp_enqueue', 11 );
add_action( 'admin_print_scripts-post.php', 'admin_enp_enqueue', 11 );
