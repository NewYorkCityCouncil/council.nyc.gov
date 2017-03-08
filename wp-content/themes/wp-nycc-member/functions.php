<?php

// Remove parent theme's admin pages, nav menus, and page tempaltes
require_once(get_template_directory().'/assets/functions/remove-in-child-theme.php');


// District-specific functions
require_once(get_stylesheet_directory().'/functions/posts.php');          // Posts
require_once(get_stylesheet_directory().'/functions/pages.php');          // Pages
require_once(get_stylesheet_directory().'/functions/options.php');        // District Options
// require_once(get_stylesheet_directory().'/functions/contact-info.php');   // Contact Info widget
require_once(get_stylesheet_directory().'/functions/pb.php');             // Participatory Budgeting
require_once(get_stylesheet_directory().'/functions/menus.php');          // Menus
require_once(get_stylesheet_directory().'/functions/widgets.php');        // Widgets
require_once(get_stylesheet_directory().'/functions/roles.php');          // Roles
require_once(get_stylesheet_directory().'/functions/theme-support.php');  // Theme Support
