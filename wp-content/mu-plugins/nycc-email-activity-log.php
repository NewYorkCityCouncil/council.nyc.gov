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

function nycc_email_activity_log( $post_id, $post_after ) {

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return; // don't send email if post is being autosaved or isn't published
    if ( get_post_status($post_id) !== 'publish' ) return; // don't send email if post isn't published
    // if ( is_super_admin() ) return; // don't send email if user is Superadmin

    $post = $post_after;

    $emailto  = get_option( 'admin_email' );
    $subject = "Content Updated";
    $message = $post->post_title . "\nView it: " . get_permalink( $post->ID ) . "\nEdit it: " . get_edit_post_link( $post->ID );

    if ( did_action('post_updated') == 1 ) {
        wp_mail( $emailto, $subject, $message );
    }

}
add_action('post_updated', 'nycc_email_activity_log', 10, 2);
