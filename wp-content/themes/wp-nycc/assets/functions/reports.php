<?php

function nycc_report_post_type() {
  register_post_type( 'nycc_report',
    array('labels' => array(
      'name' => __('Reports', 'nycc'),
      'singular_name' => __('Report Page', 'nycc'),
      'all_items' => __('All Reports', 'nycc'),
      'add_new' => __('Add New Report Page', 'nycc'),
      'add_new_item' => __('Add New Report Page', 'nycc'),
      'edit' => __( 'Edit', 'nycc' ),
      'edit_item' => __('Edit Report Page', 'nycc'),
      'new_item' => __('New Report Page', 'nycc'),
      'view_item' => __('View Report Page', 'nycc'),
      'search_items' => __('Search Reports', 'nycc'),
      'not_found' =>  __('Nothing found in the Database.', 'nycc'),
      'not_found_in_trash' => __('Nothing found in Trash', 'nycc')
      ),
      'description' => __( 'NYC Council Reports', 'nycc' ),
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_nav_menus'=> false,
      'query_var' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-chart-line',
      'rewrite'  => array( 'slug' => 'reports', 'with_front' => false ),
      'has_archive' => false,
      'capability_type' => 'page',
      'hierarchical' => true,
      'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
      'register_meta_box_cb' => 'add_report_metaboxes'
     )
  );
}
add_action( 'init', 'nycc_report_post_type');

function add_report_metaboxes() {
  add_meta_box('nycc_report_meta', 'Downloadable Report', 'nycc_report_meta', 'nycc_report', 'normal', 'default');
  add_meta_box('nycc_report_toc', 'Table of Contents', 'nycc_report_toc', 'nycc_report', 'side', 'default');
}
add_action( 'add_meta_boxes', 'add_report_metaboxes' );

function nycc_report_meta() {
  global $post;
  echo '<input type="hidden" name="nycc_report_meta_noncename" id="nycc_report_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $report_link_url = get_post_meta($post->ID, 'report_link_url', true);
  $report_link_text = get_post_meta($post->ID, 'report_link_text', true);
  ?>

  <p class="description">This displays on top-level pages only. Do not use this for child pages.</p>

  <table class="form-table">

    <tr valign="top">
      <th scope="row">Report URL</th>
      <td>
        <input type="text" name="report_link_url" value="<?php echo esc_attr( $report_link_url ); ?>" placeholder="http://..." class="regular-text" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Button Text</th>
      <td>
        <input type="text" name="report_link_text" value="<?php echo esc_attr( $report_link_text ); ?>" class="regular-text" />
      </td>
    </tr>

  </table>
<?php
}

function nycc_report_toc() {
  global $post;
  echo '<input type="hidden" name="nycc_report_toc_noncename" id="nycc_report_toc_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
  $report_toc_before = get_post_meta($post->ID, 'report_toc_before', true);
  $report_toc_label = get_post_meta($post->ID, 'report_toc_label', true);
  ?>
  <p><strong>TOC Before</strong> <small>(inserts heading)</small></p>
  <input type="text" name="report_toc_before" value="<?php echo esc_attr( $report_toc_before ); ?>" />
  <p><strong>Page Label</strong> <small>(replaces title)</small></p>
  <input type="text" name="report_toc_label" value="<?php echo esc_attr( $report_toc_label ); ?>" />
<?php
}

function save_nycc_report_meta($post_id, $post) {
  if ( !wp_verify_nonce( $_POST['nycc_report_meta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
  }
  if ( !current_user_can( 'edit_post', $post->ID ))
    return $post->ID;
  $report_meta['report_link_url'] = $_POST['report_link_url'];
  $report_meta['report_link_text'] = $_POST['report_link_text'];
  $report_meta['report_toc_before'] = $_POST['report_toc_before'];
  $report_meta['report_toc_label'] = $_POST['report_toc_label'];
  foreach ($report_meta as $key => $value) { // Cycle through the $events_meta array!
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
add_action('save_post', 'save_nycc_report_meta', 1, 2);
