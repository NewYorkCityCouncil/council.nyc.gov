<?php

add_action( 'admin_menu', 'nycc_pb_options' );

function nycc_pb_options() {
    add_options_page( 'PB Options', 'PB Options', 'manage_options', 'pb-options', 'nycc_pb_options_page' );
    add_action( 'admin_init', 'register_nycc_pb_options' );
}

function register_nycc_pb_options() {
    register_setting( 'pb-options-group', 'pb_placeholder', 'sanitize_pb_options' );
    register_setting( 'pb-options-group', 'pb_above_district_list', 'sanitize_pb_options' );
}

function sanitize_pb_options ($input) {
  global $allowedposttags;
  $input = wp_kses( $input, $allowedposttags);
  return $input;
}

function nycc_pb_options_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    ?>
    <div class="wrap">
      <h1>PB Options</h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'pb-options-group' ); ?>
        <?php do_settings_sections( 'pb-options-group' ); ?>
        <table class="form-table">

          <tr valign="top">
            <th scope="row">PB Placeholder Markup</th>
            <td>
              <textarea name="pb_placeholder" rows="10" cols="50" class="large-text"><?php echo esc_attr( get_option('pb_placeholder') ); ?></textarea>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Above Sidebar Districts List</th>
            <td>
              <textarea name="pb_above_district_list" rows="2" cols="50" class="large-text"><?php echo esc_attr( get_option('pb_above_district_list') ); ?></textarea>
            </td>
          </tr>

        </table>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
