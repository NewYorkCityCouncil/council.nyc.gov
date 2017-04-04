<?php

// Unregister widgets
function nycc_jobs_unregister_default_widgets() {
  unregister_widget('WP_Widget_Archives');
  // unregister_widget('WP_Widget_Search');
  // unregister_widget('WP_Widget_Text');
  unregister_widget('WP_Widget_Categories');
  unregister_widget('WP_Widget_Recent_Posts');
  unregister_widget('WP_Nav_Menu_Widget');
}
add_action('widgets_init', 'nycc_jobs_unregister_default_widgets', 11);
