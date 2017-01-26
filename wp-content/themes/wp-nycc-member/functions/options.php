<?php

add_action( 'admin_menu', 'nycc_district_options' );

function nycc_district_options() {
    add_options_page( 'District Options', 'District Options', 'manage_options', 'district-options', 'nycc_district_options_page' );
    add_action( 'admin_init', 'register_nycc_district_options' );
}

function register_nycc_district_options() {
    register_setting( 'district-options-group', 'council_district_number', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_member_name', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_member_short_name', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_member_thumbnail', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_member_party', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_district_borough', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_member_short_bio', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_district_neighborhoods', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_district_contact', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_legislative_contact', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_district_email', 'sanitize_district_options' );
    register_setting( 'district-options-group', 'council_district_contact_form' );
}

function sanitize_district_options ($input) {
  global $allowedposttags;
  $input = wp_kses( $input, $allowedposttags);
  return $input;
}

function nycc_district_options_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    ?>
    <div class="wrap">
      <h1>District Options</h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'district-options-group' ); ?>
        <?php do_settings_sections( 'district-options-group' ); ?>
        <table class="form-table">

          <tr valign="top">
            <th scope="row">District Number</th>
            <td>
              <input type="number" name="council_district_number" value="<?php echo esc_attr( get_option('council_district_number') ); ?>" min="1" max="51" />
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Council Member</th>
            <td>
              Full Name: <input type="text" name="council_member_name" value="<?php echo esc_attr( get_option('council_member_name') ); ?>" />&nbsp;&nbsp;
              Short Name: <input type="text" name="council_member_short_name" value="<?php echo esc_attr( get_option('council_member_short_name') ); ?>" />
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Thumbnail</th>
            <td>
              <input type="text" name="council_member_thumbnail" value="<?php echo esc_attr( get_option('council_member_thumbnail') ); ?>" placeholder="http://..." />
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Party</th>
            <td>
              <select name="council_member_party">
                <option <?php echo ( get_option('council_member_party') == 'Democrat' )? 'selected':''; ?> value="Democrat">Democrat</option>
                <option <?php echo ( get_option('council_member_party') == 'Republican')? 'selected':''; ?> value="Republican">Republican</option>
              </select>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Borough</th>
            <td>
              <input type="text" name="council_district_borough" value="<?php echo esc_attr( get_option('council_district_borough') ); ?>" />
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Short Bio</th>
            <td>
              <textarea name="council_member_short_bio" rows="8" cols="50" class="large-text"><?php echo esc_attr( get_option('council_member_short_bio') ); ?></textarea>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Neighborhoods</th>
            <td>
              <textarea name="council_district_neighborhoods" rows="3" cols="50" class="large-text"><?php echo esc_attr( get_option('council_district_neighborhoods') ); ?></textarea>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Contact Info</th>
            <td>
              <table>
                <tr>
                  <td style="padding:0 10px 0 0;">District Office: <textarea name="council_district_contact" rows="8" cols="50" class="large-text"><?php echo esc_attr( get_option('council_district_contact') ); ?></textarea></td>
                  <td style="padding:0 10px 0 0;">Legislative Office: <textarea name="council_legislative_contact" rows="8" cols="50" class="large-text"><?php echo esc_attr( get_option('council_legislative_contact') ); ?></textarea></td>
                </tr>
              </table>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Email Address</th>
            <td>
              <input type="text" name="council_district_email" value="<?php echo esc_attr( get_option('council_district_email') ); ?>" placeholder="example@council.nyc.gov" class="regular-text" />
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Contact Form</th>
            <td>
              <textarea name="council_district_contact_form" rows="10" cols="50" class="large-text"><?php echo esc_attr( get_option('council_district_contact_form') ); ?></textarea>
            </td>
          </tr>

        </table>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
