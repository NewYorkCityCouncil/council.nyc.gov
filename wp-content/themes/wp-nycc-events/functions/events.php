<?php

function nycc_ced_event_post_type() {
  register_post_type( 'nycc_ced_event',
    array('labels' => array(
      'name' => __('Events', 'nycc'),
      'singular_name' => __('Event', 'nycc'),
      'all_items' => __('All Events', 'nycc'),
      'add_new' => __('Add New', 'nycc'),
      'add_new_item' => __('Add New Event', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Event', 'nycc'),
      'new_item' => __('New Event', 'nycc'),
      'view_item' => __('View Event', 'nycc'),
      'search_items' => __('Search Events', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'CED Events', 'nycc' ),
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
      'supports' => array( 'title', 'thumbnail', 'editor',),
      'register_meta_box_cb' => 'add_ced_event_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_ced_event_post_type');

function add_ced_event_metaboxes() {
  add_meta_box('nycc_ced_event_meta', 'Event Meta', 'nycc_ced_event_meta', 'nycc_ced_event', 'normal', 'default');
}
add_action( 'add_meta_boxes', 'add_ced_event_metaboxes' );

function nycc_ced_event_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_ced_event_meta_noncename" id="nycc_ced_event_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $ced_event_title = get_post_meta($post->ID, 'ced_event_title', true);
  $ced_event_link = get_post_meta($post->ID, 'ced_event_link', true);
  $ced_event_date = get_post_meta($post->ID, 'ced_event_date', true);
  $ced_event_time_doors = get_post_meta($post->ID, 'ced_event_time_doors', true);
  $ced_event_time_start = get_post_meta($post->ID, 'ced_event_time_start', true);
  $ced_event_room = get_post_meta($post->ID, 'ced_event_room', true);
  $ced_event_location = get_post_meta($post->ID, 'ced_event_location', true);
  $ced_event_description = get_post_meta($post->ID, 'ced_event_description', true);
  $ced_event_invite_eng = get_post_meta($post->ID, 'ced_event_invite_eng', true);
  $ced_event_invite_ben = get_post_meta($post->ID, 'ced_event_invite_ben', true);
  $ced_event_invite_chi = get_post_meta($post->ID, 'ced_event_invite_chi', true);
  $ced_event_invite_cre = get_post_meta($post->ID, 'ced_event_invite_cre', true);
  $ced_event_invite_krn = get_post_meta($post->ID, 'ced_event_invite_krn', true);
  $ced_event_invite_spa = get_post_meta($post->ID, 'ced_event_invite_spa', true);
  ?>
  <span>All fields are required.</span>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Event Title*</th>
      <td>
        <input required type="text" name="ced_event_title" value="<?php echo esc_attr( $ced_event_title ); ?>" class="regular-text" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">RSVP Link*</th>
      <td>
        <input required type="text" name="ced_event_link" value="<?php echo esc_attr( $ced_event_link ); ?>" class="regular-text" placeholder="https://..." />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Date*</th>
      <td>
        <input required type="date" class="custom_date" name="ced_event_date" value="<?php echo esc_attr( $ced_event_date ); ?>"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Time (Doors Open)*</th>
      <td>
        <input required type="time" name="ced_event_time_doors" value="<?php echo esc_attr( $ced_event_time_doors ); ?>" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Time (Start)*</th>
      <td>
        <input required type="time" name="ced_event_time_start" value="<?php echo esc_attr( $ced_event_time_start ); ?>" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Room*</th>
      <td>
        <input required type="text" name="ced_event_room" value="<?php echo esc_attr( $ced_event_room ); ?>" class="regular-text" placeholder="Council Chambers"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Location*</th>
      <td>
        <input required type="text" name="ced_event_location" value="<?php echo esc_attr( $ced_event_location ); ?>" class="regular-text" placeholder="New York City Hall @ New York, NY 10007"/>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Flyer PDF (English)*</th>
      <td>
        <input required type="text" name="ced_event_invite_eng" value="<?php echo esc_attr( $ced_event_invite_eng ); ?>" class="regular-text"  placeholder="http://..." />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Flyer PDF (Bengali)</th>
      <td>
        <input type="text" name="ced_event_invite_ben" value="<?php echo esc_attr( $ced_event_invite_ben ); ?>" class="regular-text" placeholder="http://..." />
      </td>
    </tr>
    <tr valign="top">
        <th scope="row">Event Flyer PDF (Chinese)</th>
        <td>
            <input type="text" name="ced_event_invite_chi" value="<?php echo esc_attr( $ced_event_invite_chi ); ?>" class="regular-text" placeholder="http://..." />
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">Event Flyer PDF (Creole)</th>
        <td>
        <input type="text" name="ced_event_invite_cre" value="<?php echo esc_attr( $ced_event_invite_cre ); ?>" class="regular-text" placeholder="http://..." />
        </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Flyer PDF (Korean)</th>
      <td>
        <input type="text" name="ced_event_invite_krn" value="<?php echo esc_attr( $ced_event_invite_krn ); ?>" class="regular-text"  placeholder="http://..." />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Event Flyer PDF (Spanish)</th>
      <td>
        <input type="text" name="ced_event_invite_spa" value="<?php echo esc_attr( $ced_event_invite_spa ); ?>" class="regular-text" placeholder="http://..." />
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

function save_nycc_ced_event_meta($post_id, $post) {
  if ( isset($_POST['nycc_ced_event_meta_noncename']) ) {
    if ( !wp_verify_nonce( $_POST['nycc_ced_event_meta_noncename'], plugin_basename(__FILE__) )) {
      return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
      return $post->ID;
    $ced_event_meta['ced_event_title'] = $_POST['ced_event_title'];
    $ced_event_meta['ced_event_link'] = $_POST['ced_event_link'];
    $ced_event_meta['ced_event_date'] = $_POST['ced_event_date'];
    $ced_event_meta['ced_event_time_doors'] = $_POST['ced_event_time_doors'];
    $ced_event_meta['ced_event_time_start'] = $_POST['ced_event_time_start'];
    $ced_event_meta['ced_event_room'] = $_POST['ced_event_room'];
    $ced_event_meta['ced_event_location'] = $_POST['ced_event_location'];
    $ced_event_meta['ced_event_description'] = $_POST['ced_event_description'];
    $ced_event_meta['ced_event_invite_eng'] = $_POST['ced_event_invite_eng'];
    $ced_event_meta['ced_event_invite_ben'] = $_POST['ced_event_invite_ben'];
    $ced_event_meta['ced_event_invite_chi'] = $_POST['ced_event_invite_chi'];
    $ced_event_meta['ced_event_invite_cre'] = $_POST['ced_event_invite_cre'];
    $ced_event_meta['ced_event_invite_krn'] = $_POST['ced_event_invite_krn'];
    $ced_event_meta['ced_event_invite_spa'] = $_POST['ced_event_invite_spa'];

    foreach ($ced_event_meta as $key => $value) { // Cycle through the $events_meta array!
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
add_action('save_post', 'save_nycc_ced_event_meta', 1, 2);

// Enqueue scripts
function admin_ced_enqueue() {
    global $post_type;
    if( 'nycc_ced_event' == $post_type )
    // wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action( 'admin_print_scripts-post-new.php', 'admin_ced_enqueue', 11 );
add_action( 'admin_print_scripts-post.php', 'admin_ced_enqueue', 11 );
