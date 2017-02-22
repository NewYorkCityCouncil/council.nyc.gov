<?php
/*
Plugin Name: NYCC Email Activity Log
Plugin URI:
Description: Sends an email when content is updated
Version: 0.1
Author: Andy Cochran
License: GNU General Public License & MIT
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function nycc_email_activity_log( $post ) {

    global $post;

    // don't send email if post is being autosaved or isn't published
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $post->post_status === 'auto-draft' || $post->post_status !== 'publish' )
        return;

    $emailto = get_option( 'admin_email' );
    $subject = 'Content Updated';
    $message = 'View it: ' . get_permalink( $post->ID ) . "\nEdit it: " . get_edit_post_link( $post->ID );

    wp_mail( $emailto, $subject, $message );

}
add_action( 'save_post', 'nycc_email_activity_log', 10, 3 );
