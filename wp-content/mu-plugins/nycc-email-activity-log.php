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

function nycc_email_activity_log( $post_id, $post_after, $post_before ) {

    // don't send email if...
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return; // post is being autosaved
    if ( get_post_status($post_id) !== 'publish' ) return; // post isn't published
    if ( is_super_admin() ) return; // user is Superadmin
    $theme = wp_get_theme();
    if ( 'NYCC Press' == $theme->name ) return; // it's the Press theme

    // Set the to address
    $emailto  = get_option( 'admin_email' );

    // Set the subject line
    if ( $post_before->post_status === 'publish' && $post_before->post_status === 'publish' ) {
      $subject = "Content Updated";
    } else {
      $subject = "New Content";
    }

    // Set the message
    $message = "Title:\n" . $post_after->post_title . "\n\nView it:\n" . get_permalink( $post_after->ID ) . "\n\nEdit it:\n" . get_edit_post_link( $post_after->ID );

    if ( did_action('post_updated') == 1 ) {
        wp_mail( $emailto, $subject, $message );
    }

}
add_action('post_updated', 'nycc_email_activity_log', 10, 3);
