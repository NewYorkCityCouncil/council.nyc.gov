<?php

// add the District Meta box
function nycc_district_meta_box() {
  global $post;
  $screen = get_current_screen();
  if ( $screen->post_type == 'page' && 'page-district.php' == get_post_meta( $post->ID, '_wp_page_template', true ) ) {
    add_meta_box('nycc_district_meta', 'District Meta', 'nycc_district_meta', 'page', 'side', 'default');
  }
}
add_action( 'add_meta_boxes_page', 'nycc_district_meta_box' );

// hide the Featured Image box
function remove_thumbnail_box() {
  global $post;
  $screen = get_current_screen();
  if ( $screen->post_type == 'page' && 'page-district.php' == get_post_meta( $post->ID, '_wp_page_template', true ) ) {
    remove_meta_box( 'postimagediv','page','side' );
  }
}
add_action('do_meta_boxes', 'remove_thumbnail_box');

// District Meta box display callback
function nycc_district_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_district_meta_noncename" id="nycc_district_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $current_member_site = get_post_meta($post->ID, 'current_member_site', true);
  ?>
  <table class="form-table">

    <tr valign="top">
      <th scope="row">Member Site ID</th>
      <td>
        <input type="number" name="current_member_site" value="<?php echo esc_attr( $current_member_site ); ?>" min="1" max="99999" />
      </td>
    </tr>

  </table>
<?php
}

// save the District Meta info
function save_nycc_district_meta($post_id, $post) {
  if ( isset($_POST['nycc_district_meta_noncename']) ) {
    if ( !wp_verify_nonce( $_POST['nycc_district_meta_noncename'], plugin_basename(__FILE__) )) {
      return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID ))
      return $post->ID;
    $district_meta['current_member_site'] = $_POST['current_member_site'];
    foreach ($district_meta as $key => $value) {
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
}
add_action('save_post', 'save_nycc_district_meta', 1, 2);
