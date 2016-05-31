<?php

// Change "posts" to "press release"
function change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Press Releases';
    $submenu['edit.php'][5][0] = 'Press Releases';
    $submenu['edit.php'][10][0] = 'Add Press Release';
    $submenu['edit.php'][16][0] = 'Tags';
    $menu[5][6] = 'dashicons-megaphone';
    echo '';
}
add_action( 'admin_menu', 'change_post_menu_label' );

function change_post_object_label() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Press Releases';
    $labels->singular_name = 'Press Release';
    $labels->add_new = 'Add Press Release';
    $labels->add_new_item = 'Add Press Release';
    $labels->edit_item = 'Edit Press Release';
    $labels->new_item = 'Press Release';
    $labels->view_item = 'View Press Release';
    $labels->search_items = 'Search Press Releases';
    $labels->not_found = 'No Press Releases found';
    $labels->not_found_in_trash = 'No Press Releases found in Trash';
    $labels->name_admin_bar = 'Press Release';
}
add_action( 'init', 'change_post_object_label' );


// Remove categories from posts
function press_remove_categories() {
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
}
add_action('admin_menu', 'press_remove_categories');

function press_remove_categories_metaboxes() {
    remove_meta_box( 'categorydiv','post','normal' ); // Categories Metabox
}
add_action('admin_menu','press_remove_categories_metaboxes');
