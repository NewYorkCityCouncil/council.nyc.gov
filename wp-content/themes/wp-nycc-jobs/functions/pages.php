<?php

// Change pages to job opportunities
// function change_post_menu_label() {
//     global $menu;
//     global $submenu;
//     $menu[20][0] = 'Jobs';
//     $submenu['edit.php?post_type=page'][5][0] = 'Job Opportunities';
//     $submenu['edit.php?post_type=page'][10][0] = 'Add Job Opportunity';
//     $menu[20][6] = 'dashicons-id-alt';
//     echo '';
// }
// add_action( 'admin_menu', 'change_post_menu_label' );

// function change_post_object_label() {
//     global $wp_post_types;
//     $labels = &$wp_post_types['page']->labels;
//     $labels->name = 'Job Opportunities';
//     $labels->singular_name = 'Job Opportunity';
//     $labels->add_new = 'Add Job Opportunity';
//     $labels->add_new_item = 'Add Job Opportunity';
//     $labels->edit_item = 'Edit Job Opportunity';
//     $labels->new_item = 'Job Opportunity';
//     $labels->view_item = 'View Job Opportunity';
//     $labels->search_items = 'Search Job Opportunities';
//     $labels->not_found = 'No Job Opportunities found';
//     $labels->not_found_in_trash = 'No Job Opportunities found in Trash';
//     $labels->name_admin_bar = 'Job Opportunity';
// }
// add_action( 'init', 'change_post_object_label' );


// Create Job Division taxonomy
function create_job_division_tax() {
    $labels = array(
      'name'                       => _x( 'Job Divisions', 'taxonomy general name' ),
      'singular_name'              => _x( 'Job Division', 'taxonomy singular name' ),
      'search_items'               => __( 'Search Divisions' ),
      'popular_items'              => __( 'Popular Divisions' ),
      'all_items'                  => __( 'All Divisions' ),
      'parent_item'                => null,
      'parent_item_colon'          => null,
      'edit_item'                  => __( 'Edit Division' ),
      'update_item'                => __( 'Update Division' ),
      'add_new_item'               => __( 'Add New Division' ),
      'new_item_name'              => __( 'New Division Name' ),
      'separate_items_with_commas' => __( 'Separate divisions with commas' ),
      'add_or_remove_items'        => __( 'Add or remove divisions' ),
      'choose_from_most_used'      => __( 'Choose from the most used divisions' ),
      'not_found'                  => __( 'No divisions found.' ),
      'menu_name'                  => __( 'Job Divisions' ),
    );
    $args = array(
      'hierarchical'          => false,
      'labels'                => $labels,
      'show_ui'               => true,
      'show_admin_column'     => true,
      'update_count_callback' => '_update_post_term_count',
      'query_var'             => true,
      'rewrite'               => array( 'slug' => 'division' ),
      'meta_box_cb'           => 'job_division_meta_box',
    );
    register_taxonomy( 'job_division', 'page', $args );
}
add_action( 'init', 'create_job_division_tax' );

// Display job_division meta box
function job_division_meta_box( $post ) {
  $terms = get_terms( 'job_division', array( 'hide_empty' => false ) );
  $post  = get_post();
  $job_division = wp_get_object_terms( $post->ID, 'job_division', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
  $name  = '';
  if ( ! is_wp_error( $job_division ) ) {
    if ( isset( $job_division[0] ) && isset( $job_division[0]->name ) ) {
    $name = $job_division[0]->name;
    }
  }
  ?>
  <label title="">
      <input type="radio" name="tax_input[job_division]" value="" <?php
      if ( !isset($term) ) {
        echo 'checked="checked"';
      } else if ( null == $term->name ) {
        echo 'checked="checked"';
      }
      ?>>
    <span>N/A <small>(regular page)</small></span>
  </label><br>
  <?php
  foreach ( $terms as $term ) { ?>
    <label title="<?php esc_attr_e( $term->name ); ?>">
        <input type="radio" name="tax_input[job_division]" value="<?php esc_attr_e( $term->name ); ?>" <?php checked( $term->name, $name ); ?>>
      <span><?php esc_html_e( $term->name ); ?></span>
    </label><br>
  <?php }
}

// Nix the Page Attributes box
function remove_page_attribute_meta_box() {
    remove_meta_box('pageparentdiv', 'page', 'normal');
}
add_action( 'admin_menu', 'remove_page_attribute_meta_box' );

// Nix the Featured Image box
function remove_featured_img_box() {
    remove_meta_box('postimagediv', 'page', 'side');
}
add_action( 'do_meta_boxes', 'remove_featured_img_box' );


// Remove page templates
function nycc_jobs_filter_theme_page_templates( $page_templates, $post ) {
    $the_theme = wp_get_theme();

    if ( isset( $page_templates['page-sidebar.php'] ) ) {
         unset( $page_templates['page-sidebar.php'] );
    }

    return $page_templates;
}
add_filter( 'theme_page_templates', 'nycc_jobs_filter_theme_page_templates', 20, 2 );
