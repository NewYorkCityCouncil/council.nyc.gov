<?php

add_action( 'admin_menu', 'nycc_jobs_options' );

function nycc_jobs_options() {
    add_options_page( 'Jobs Options', 'Jobs Options', 'edit_posts', 'jobs-options', 'nycc_jobs_options_page' );
    add_action( 'admin_init', 'register_nycc_jobs_options' );
}

function register_nycc_jobs_options() {
    register_setting( 'jobs-options-group', 'jobs_front_page_content' );
}


function nycc_jobs_options_page() {
    if ( !current_user_can( 'edit_posts' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    ?>
    <div class="wrap">
      <h1>Jobs Options</h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'jobs-options-group' ); ?>
        <?php do_settings_sections( 'jobs-options-group' ); ?>
        <table class="form-table">

          <tr valign="top">
            <th scope="row">Front Page Content</th>
            <td>
              <textarea name="jobs_front_page_content" rows="20" cols="50" class="large-text"><?php echo esc_attr( get_option('jobs_front_page_content') ); ?></textarea>
            </td>
          </tr>

        </table>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
