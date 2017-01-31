<?php

// add the PB Cycle Meta box
function nycc_pbcycle_meta_box() {
  global $post;
  add_meta_box('nycc_pbcycle_meta', 'Page Meta', 'nycc_pbcycle_meta', 'page', 'normal', 'default');
}
add_action( 'add_meta_boxes_page', 'nycc_pbcycle_meta_box' );

// hide the Featured Image box
function nycc_pb_remove_thumbnail_box() {
  global $post;
  remove_meta_box( 'postimagediv','page','side' );
}
add_action('do_meta_boxes', 'nycc_pb_remove_thumbnail_box');

// PB Cycyle Meta box display callback
function nycc_pbcycle_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_pbcycle_meta_noncename" id="nycc_pbcycle_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $current_pb_cycle = get_post_meta($post->ID, 'current_pb_cycle', true);
  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">PB Cycle</th>
      <td>
        <input type="number" name="current_pb_cycle" value="<?php echo esc_attr( $current_pb_cycle ); ?>" min="1" max="99999" />
      </td>
    </tr>

  </table>
<?php
}

// save the PB Cycle Meta info
function save_nycc_pbcycle_meta($post_id, $post) {
  if ( !wp_verify_nonce( $_POST['nycc_pbcycle_meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
  }
  if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;
  $pbcycle_meta['current_pb_cycle'] = $_POST['current_pb_cycle'];
  foreach ($pbcycle_meta as $key => $value) {
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
add_action('save_post', 'save_nycc_pbcycle_meta', 1, 2);
