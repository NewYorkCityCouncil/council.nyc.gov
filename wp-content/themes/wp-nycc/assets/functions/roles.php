<?php

// Add a custom user role
remove_role( 'pbadmin' );
$result = add_role( 'pbadmin', 'PB Admin', array(
    'delete_others_pages' => true,
    'delete_others_posts' => true,
    'delete_pages' => true,
    'delete_posts' => true,
    'delete_private_pages' => true,
    'delete_private_posts' => true,
    'delete_published_pages' => true,
    'delete_published_posts' => true,
    'edit_others_pages' => true,
    'edit_others_posts' => true,
    'edit_pages' => true,
    'edit_posts' => true,
    'edit_private_pages' => true,
    'edit_private_posts' => true,
    'edit_published_pages' => true,
    'edit_published_posts' => true,
    'manage_categories' => true,
    'publish_pages' => true,
    'publish_posts' => true,
    'read' => true,
    'read_private_pages' => true,
    'read_private_posts' => true,
    'upload_files' => true,
    'edit_theme_options' => true,
    'manage_options' => true,
  )
);
