<?php

// Unregister widgets
function nycc_unregister_press_widgets() {
  unregister_widget('WP_Nav_Menu_Widget');
}
add_action('widgets_init', 'nycc_unregister_press_widgets', 11);
