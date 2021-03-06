<?php

add_action( 'admin_menu', 'nycc_options' );

function nycc_options() {
    add_options_page( 'Site Options', 'Site Options', 'manage_options', 'nycc-options', 'nycc_options_page' );
    add_action( 'admin_init', 'register_nycc_options' );
}

function register_nycc_options() {
    register_setting( 'nycc-options-group', 'site_footer_content' );
    register_setting( 'nycc-options-group', '404_content' );
    register_setting( 'nycc-options-group', 'pb_site_id' );
}


function nycc_options_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    ?>
    <div class="wrap">
      <h1>Site Options</h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'nycc-options-group' ); ?>
        <?php do_settings_sections( 'nycc-options-group' ); ?>
        <table class="form-table">

          <tr valign="top">
            <th scope="row">Footer Content</th>
            <td>
              <textarea name="site_footer_content" rows="20" cols="50" class="large-text"><?php echo esc_attr( get_option('site_footer_content') ); ?></textarea>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">404 Page Content</th>
            <td>
              <textarea name="404_content" rows="10" cols="50" class="large-text"><?php echo esc_attr( get_option('404_content') ); ?></textarea>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">PB Site ID</th>
            <td>
              <input type="number" name="pb_site_id" value="<?php echo esc_attr( get_option('pb_site_id') ); ?>" min="1" />
            </td>
          </tr>

        </table>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
