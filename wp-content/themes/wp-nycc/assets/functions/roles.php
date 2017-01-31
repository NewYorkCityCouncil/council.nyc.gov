<?php

// Add a custom user role
$result = add_role( 'pbadmin', 'PB Admin', array(
    'delete_others_pages' => true,
    'delete_pages' => true,
    'delete_private_pages' => true,
    'delete_published_pages' => true,
    'edit_others_pages' => true,
    'edit_pages' => true,
    'edit_private_pages' => true,
    'edit_published_pages' => true,
    'edit_theme_options' => true,
    'manage_options' => true,
    'publish_pages' => true,
    'read_private_pages' => true,
    'read' => true,
    'upload_files' => true,
  )
);
