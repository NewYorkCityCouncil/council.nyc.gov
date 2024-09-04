<?php

function change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'News Post';
    $submenu['edit.php'][5][0] = 'News Posts';
    $submenu['edit.php'][10][0] = 'Add News Post';
    $submenu['edit.php'][16][0] = 'Tags';
    $menu[5][6] = 'dashicons-megaphone';
    echo '';
}
add_action( 'admin_menu', 'change_post_menu_label' );

function change_post_object_label() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'News Posts';
    $labels->singular_name = 'News Post';
    $labels->add_new = 'Add News Post';
    $labels->add_new_item = 'Add News Post';
    $labels->edit_item = 'Edit News Post';
    $labels->new_item = 'News Post';
    $labels->view_item = 'View News Post';
    $labels->search_items = 'Search News Posts';
    $labels->not_found = 'No News Posts found';
    $labels->not_found_in_trash = 'No News Posts found in Trash';
    $labels->name_admin_bar = 'News Post';
}
add_action( 'init', 'change_post_object_label' );
