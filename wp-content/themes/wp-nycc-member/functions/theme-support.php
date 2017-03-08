<?php

function remove_them_support_in_child_theme() {
    remove_theme_support( 'post-formats' );
}
add_action( 'after_setup_theme', 'remove_them_support_in_child_theme', 11 );
