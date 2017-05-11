<?php

/**
 * Description of class-fscf-options
 * Class used to encapsulate functions related to the options menu.
 * Functions are called statically, so no need to instantiate the class
 * @authors Mike Challis and Ken Carlson
 */

class FSCF_Options {

	static $form_defaults, $style_defaults;
	static $global_options, $form_options;
	static $current_form, $form_option_name, $current_tab;
	static $av_fld_arr, $av_fld_subj_arr;		// list of avail field tags
	static $autoresp_ok, $new_field_added, $new_field_key;
    static $post_types_slugs = array('post','page','attachment','revision');

	static function get_form_num() {
		// Set the number of the current form
		// Form 1 cannot be deleted, so we can use it as the default
		self::$current_form = 1;	 // This is the default
		$form_num_default = 1;
		if ( isset( $_REQUEST['fscf_form'] ) ) {
			self::$current_form = $_REQUEST['fscf_form'];
		} elseif ( isset( $_REQUEST['_wp_http_referer'] ) ) {
			$parts = explode( 'fscf_form=', $_REQUEST['_wp_http_referer'] );
			if ( count( $parts ) == 2 ) {
				self::$current_form = absint($parts[1]);
			}
		}

		if ( ! is_numeric( self::$current_form )) {
			echo '<div id="message" class="error">';
			echo __( 'Internal Error: Invalid form number.', 'si-contact-form' );
			echo "</div>\n";
			self::$current_form = $form_num_default;
		}
		self::$form_option_name = "fs_contact_form" . self::$current_form;

		// Check for the current tab number
		if ( isset( $_REQUEST['fscf_tab'] ) && is_numeric( $_REQUEST['fscf_tab'] ) )
			self::$current_tab = absint($_REQUEST['fscf_tab']);
		else
			self::$current_tab = 1;
	}

	static function register_options_page() {
		// Adds link on admin 'Settings' menu
		add_options_page(
				__('Fast Secure Contact Form Options', 'si-contact-form'), // The text to the display in the browser when this menu item is active
				__('FS Contact Form', 'si-contact-form'), // The text for this menu item
				'manage_options', // Which type of users can see this menu
				// The slug should point to the main plugin file, not this one!
				'si-contact-form/si-contact-form.php', // __FILE__, // The unique ID - the slug - for this menu item
				'FSCF_Options::display_options'	  // The function used to render the menu for this page to the screen
		);
	}

	static function get_options() {
		if ( ! isset( self::$form_defaults ) )
			self::$form_defaults = FSCF_Util::set_defaults ( );
		
		if ( ! self::$global_options ) {
			self::$global_options = FSCF_Util::get_global_options();
		}
		
		if ( ! self::$form_options ) {
			self::$form_options = FSCF_Util::get_form_options(self::$current_form, true);
		}

		// See if the form name has changed--if so, update it in the list
		if ( ( self::$global_options['form_list'][self::$current_form] <> self::$form_options['form_name'] ) &&
				self::$form_options['form_name'] <> "" ) {
			self::$global_options['form_list'][self::$current_form] = self::$form_options['form_name'];
			update_option( 'fs_contact_global', self::$global_options );
		}

		if ( count( self::$form_options ) < count( self::$form_defaults ) ) {
			// add missing elements from the default form options array
			self::$form_options = array_merge( self::$form_defaults, self::$form_options );
		}
	}

	static function unload_options() {
		// Forces the reload of global and form options
		// Called by FSCF_Action::restore_settings()
		self::$global_options = false;
		self::$form_options = false;
	}

	static function initialize_options() {

		// Carry out form backup if requested
		if ( isset($_POST['ctf_action']) && esc_attr__('Backup Settings', 'si-contact-form') == $_POST['ctf_action'] )  FSCF_Action::backup_download();

		// Get the current form
		self::get_form_num();

		// Register settings sections
		
		add_settings_section(
				'fscf_basic_settings',	// ID used to identify this section and with which to register options
				__('Basic Settings', 'si-contact-form'), // Title to be displayed on the administration page
				'FSCF_Options::basic_settings_callback', // Callback used to render the description of the section
				'tab_page1'				// Page on which to add this section of options
		);

		add_settings_section(
				'fscf_email_settings',
				__('Email Settings', 'si-contact-form'),
				'FSCF_Options::email_settings_callback',
				'tab_page1'
		);

		add_settings_section(
				'fscf_field_settings',
				__('Field Settings', 'si-contact-form'),
				'FSCF_Options::field_settings_callback',
				'tab_page2'
		);

		add_settings_section(
				'fscf_style_settings',
				__('Style Settings', 'si-contact-form'),
				'FSCF_Options::style_settings_callback',
				'tab_page3'
		);

		add_settings_section(
				'fscf_label_settings',
				__('Field Label Settings', 'si-contact-form'),
				'FSCF_Options::label_settings_callback',
				'tab_page4'
		);

		add_settings_section(
				'fscf_tooltip_settings',
				__('Tooltip Label Settings', 'si-contact-form'),
				'FSCF_Options::tooltip_settings_callback',
				'tab_page4'
		);

		add_settings_section(
				'fscf_error_settings',
				__('Error Message Settings', 'si-contact-form'),
				'FSCF_Options::error_settings_callback',
				'tab_page4'
		);


 		add_settings_section(
				'fscf_captcha_settings',
				__('CAPTCHA Settings', 'si-contact-form'),
				'FSCF_Options::captcha_settings_callback',
				'tab_page5'
		);

		add_settings_section(
				'fscf_akismet_settings',
				__('Akismet Settings', 'si-contact-form'),
				'FSCF_Options::akismet_settings_callback',
				'tab_page5'
		);

		add_settings_section(
				'fscf_domain_settings',
				__('Domain Protect Settings', 'si-contact-form'),
				'FSCF_Options::domain_settings_callback',
				'tab_page5'
		);

		add_settings_section(
				'fscf_confirmation_email',
				__('Confirmation Email Settings', 'si-contact-form'),
				'FSCF_Options::confirmation_email_callback',
				'tab_page6'
		);

		add_settings_section(
				'fscf_redirect',
				__('Redirect Settings', 'si-contact-form'),
				'FSCF_Options::redirect_callback',
				'tab_page6'
		);


        add_settings_section(
				'fscf_advanced_form',
				__('Advanced Form Settings', 'si-contact-form'),
				'FSCF_Options::advanced_form_callback',
				'tab_page6'
		);

        add_settings_section(
				'fscf_advanced_email',
				__('Advanced Email Settings', 'si-contact-form'),
				'FSCF_Options::advanced_email_callback',
				'tab_page6'
		);

		add_settings_section(
				'fscf_silent_sending',
				__('Silent Remote Sending Settings', 'si-contact-form'),
				'FSCF_Options::silent_sending_callback',
				'tab_page6'
		);

		add_settings_section(
				'fscf_data_export',
				__('Data Export Settings', 'si-contact-form'),
				'FSCF_Options::data_export_callback',
				'tab_page6'
		);

        add_settings_section(
				'fscf_meeting_settings',
				__('vCita Online Scheduling Settings', 'si-contact-form'),
				'FSCF_Options::meeting_settings_callback',
				'tab_page7'
		);

        add_settings_section(
				'fscf_tools_settings',
				__('Tools and Backup', 'si-contact-form'),
				'FSCF_Options::tools_callback',
				'tab_page8'
		);
         // <form ends with here
         // the newsletter tab has its own <form
        add_settings_section(
				'fscf_newsletter_settings',
				__('Constant Contact Newsletter Settings', 'si-contact-form'),
				'FSCF_Options::newsletter_settings_callback',
				'tab_page9'
		);



		// Register the settings
		// The first parameter below is used in check_admin_referer in FSCF_Action functions
		// If you change it here, you must change it there as well
		register_setting( 'fs_contact_options', 'fs_contact_form' . self::$current_form, 'FSCF_Options::validate' );
	}

	/* ------------------------------------------------------------------------ * 
	 * ********** Display functions ********** 
	 * ------------------------------------------------------------------------ */	

	static function display_options() {
		// This displays the options menu in the admin area

		$tab_names = array(
        __('Basic Settings', 'si-contact-form'),
        __('Fields', 'si-contact-form'),
        __('Styles', 'si-contact-form'),
        __('Labels', 'si-contact-form'),
        __('Security', 'si-contact-form'),
        __('Advanced', 'si-contact-form'),
        __('Scheduling', 'si-contact-form'),
        __('Tools', 'si-contact-form'),
        __('Newsletter', 'si-contact-form'),
        );

		$num_tabs = count( $tab_names );

		// Process ctf_actions, if any
		if ( ! empty($_POST['ctf_action']) ) FSCF_Action::do_ctf_action( );
		
		// Load the options into the options array
		self::get_options();
		// The update_lang function receives the array by ref., so it can be changed
		FSCF_Util::update_lang(self::$form_options);
		FSCF_Util::update_lang(self::$form_defaults);
		self::set_fld_array();

		// Create a header in the default WordPress 'wrap' container
		?>
		<div class="wrap">

		<script type="text/javascript">
		// Set up tabs for options page (selected supports jQuery ui pre-1.9)
		jQuery(function() { 
			jQuery( "#fscf-tabs" ).tabs({ active: <?php echo esc_js(self::$current_tab)-1; ?>, selected: <?php echo esc_js(self::$current_tab)-1; ?> });

			}); 
		</script>

		<?php echo "\n"; ?>
		
		<div class="fscf_statbox">
		<?php
		// Display plugin ratings
			if ( function_exists( 'get_transient' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

				// First, try to access the data, check the cache.
				if ( false === ($api = get_transient( 'si_contact_form_info' )) ) {
					// The cache data doesn't exist or it's expired.

					$api = plugins_api( 'plugin_information', array( 'slug' => 'si-contact-form' ) );

					if ( !is_wp_error( $api ) ) {
						// cache isn't up to date, write this fresh information to it now to avoid the query for xx time.
						$myexpire = 60 * 15; // Cache data for 15 minutes
						set_transient( 'si_contact_form_info', $api, $myexpire );
					}
				}
				if ( !is_wp_error( $api ) ) {
					$plugins_allowedtags = array( 'a' => array( 'href' => array( ), 'title' => array( ), 'target' => array( ) ),
						'abbr' => array( 'title' => array( ) ), 'acronym' => array( 'title' => array( ) ),
						'code' => array( ), 'pre' => array( ), 'em' => array( ), 'strong' => array( ),
						'div' => array( ), 'p' => array( ), 'ul' => array( ), 'ol' => array( ), 'li' => array( ),
						'h1' => array( ), 'h2' => array( ), 'h3' => array( ), 'h4' => array( ), 'h5' => array( ), 'h6' => array( ),
						'img' => array( 'src' => array( ), 'class' => array( ), 'alt' => array( ) ) );

					//Sanitize HTML
					foreach ( (array) $api->sections as $section_name => $content )
						$api->sections[$section_name] = wp_kses( $content, $plugins_allowedtags );
					foreach ( array( 'version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug' ) as $key )
						$api->$key = wp_kses( $api->$key, $plugins_allowedtags );

					if ( !empty( $api->downloaded ) ) {
						echo sprintf( __( 'Downloaded %s times.', 'si-contact-form' ), number_format_i18n( $api->downloaded ) );
					}
					?>

					<?php if ( !empty( $api->rating ) ) { ?>
						<div class="fcs-star-holder" title="<?php echo esc_attr( sprintf( __( '(Average rating based on %s ratings)', 'si-contact-form' ), number_format_i18n( $api->num_ratings ) ) ); ?>">
							<div class="fcs-star fcs-star-rating" style="width: <?php echo esc_attr( $api->rating ) ?>px"></div>
							<div class="fcs-star fcs-star5"><img src="<?php echo FSCF_URL . 'includes/star.png'; ?>" alt="<?php esc_attr_e( '5 stars', 'si-contact-form' ) ?>" /></div>
							<div class="fcs-star fcs-star4"><img src="<?php echo FSCF_URL . 'includes/star.png'; ?>" alt="<?php esc_attr_e( '4 stars', 'si-contact-form' ) ?>" /></div>
							<div class="fcs-star fcs-star3"><img src="<?php echo FSCF_URL . 'includes/star.png'; ?>" alt="<?php esc_attr_e( '3 stars', 'si-contact-form' ) ?>" /></div>
							<div class="fcs-star fcs-star2"><img src="<?php echo FSCF_URL . 'includes/star.png'; ?>" alt="<?php esc_attr_e( '2 stars', 'si-contact-form' ) ?>" /></div>
							<div class="fcs-star fcs-star1"><img src="<?php echo FSCF_URL . 'includes/star.png'; ?>" alt="<?php esc_attr_e( '1 star', 'si-contact-form' ) ?>" /></div>
						</div>
						<small><?php echo sprintf( __( '(Average rating based on %s ratings)', 'si-contact-form' ), number_format_i18n( $api->num_ratings ) ); ?> <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/si-contact-form?rate=5#postform"> <?php _e( 'rate', 'si-contact-form' ) ?></a></small>
						<br />
						<?php
					}
				} // if ( !is_wp_error($api)
			}// end if (function_exists('get_transient'


			if ( isset( $api->version ) ) {
				if ( version_compare( $api->version, FSCF_VERSION, '>' ) ) {
					echo '<div id="message" class="updated">';
					echo '<a href="' . admin_url( 'plugins.php' ) . '">' . sprintf( __( 'A newer version of Fast Secure Contact Form is available: %s', 'si-contact-form' ), $api->version ) . '</a>';
					echo "</div>\n";
				} else {
					echo sprintf( __( 'Version %s (up to date)', 'si-contact-form' ), FSCF_VERSION );
				}
			}  // end div fscf_statbox ?>
		</div>

<p>
<a href="https://wordpress.org/plugins/si-contact-form/changelog/" target="_blank"><?php _e('Changelog', 'si-contact-form'); ?></a> |
<a href="http://www.fastsecurecontactform.com/faq-wordpress-version" target="_blank"><?php _e('FAQ', 'si-contact-form'); ?></a> |
<a href="https://wordpress.org/support/plugin/si-contact-form" target="_blank"><?php _e('Support', 'si-contact-form'); ?></a> |
<a href="https://wordpress.org/support/plugin/si-contact-form/reviews/?rate=5#new-post" target="_blank"><?php _e('Rate This', 'si-contact-form'); ?></a> |
<a href="https://www.fastsecurecontactform.com/donate" target="_blank"><?php _e('Donate', 'si-contact-form'); ?></a>
</p>

<?php
  /* --- vCita Header Error Messages - Start --- */

  if (self::$global_options['vcita_show_disable_msg'] == 'true') {
      // Put visible notification that vCita was removed.
      echo '<div class="fsc-success">'. __('vCita Meeting Scheduler has been disabled.', 'si-contact-form');
      echo '</div><div style="clear:both;display:block"></div>';
      self::$global_options = FSCF_Util::get_global_options();
      self::$global_options['vcita_show_disable_msg'] = 'false';
      update_option( 'fs_contact_global', self::$global_options );
  } else {
      FSCF_Process::vcita_print_admin_page_notification(self::$form_options, self::$global_options);
  }
  
  /* --- vCita Header Error Messages - End --- */

        // action hook for database extension menu
        do_action( 'fsctf_menu_links' );
?>

		<h2><?php _e('Fast Secure Contact Form Settings', 'si-contact-form'); ?></h2>
		<?php settings_errors();
		// Display form select control

		// Has a preview been selected?
		$preview = ( isset($_POST['ctf_action']) && __('Preview Form', 'si-contact-form') == $_POST['ctf_action'] ) ? true : false;
		?>
		<div class="fscf_left">
        <form id="fscf_form_control" action="<?php echo admin_url( 'options-general.php?page=si-contact-form/si-contact-form.php&amp;fscf_form='
				. self::$current_form).'&fscf_tab='.self::$current_tab; ?>" method="post" name="previewform">
            <?php wp_nonce_field( 'fs_contact_options-options', 'fs_options' );
			// The value of the ctf_action field will be set by javascript when needed ?>
			<input type="hidden" name="ctf_action" id="ctf_action" value="<?php
			( $preview ? _e('Preview Form', 'si-contact-form') : _e('Edit Form', 'si-contact-form') ) ?>" />
			<div class="fscf_select_form"><strong><?php _e('Select a Form', 'si-contact-form'); ?>: </strong>
			<select id="form_select" name="<?php echo self::$current_form; ?>" onchange="fscf_set_form('<?php _e('Add Form', 'si-contact-form'); ?>');">
			<?php // above was onchange="setForm(this.form)"
			// Display forms select list
			foreach ( self::$global_options['form_list'] as $key => $val ) {
				echo '<option value="' . esc_attr($key) . '"';
				if ( (int) self::$current_form == $key )
					echo ' selected="selected"';
				echo '>' .sprintf( __( 'Form %d: %s', 'si-contact-form' ), esc_html($key), esc_html($val) ) . "</option>\n";
			}
			echo '<option value="0">' . esc_html(__('Add a New Form', 'si-contact-form')) . "</option>\n";
			?>
			</select>
			<span class="submit">
				&nbsp;<input id="preview" class="button-primary" type="submit" value="<?php
				// When submit button is pressed, this will override the value of the hidden field
				// named ctf_action
				if ( $preview ) _e('Edit Form', 'si-contact-form');
				else _e('Preview Form', 'si-contact-form');
				?>" name="ctf_action" />
			</span>

			</div>
		</form>
		</div>
		<div id="ctf-loading">
		<?php echo '<img src="'.plugins_url( 'si-contact-form/includes/ctf-loading.gif' ).'" width="32" height="32" alt="'.esc_attr(__('Loading...', 'si-contact-form')).'" />';
		?></div>
		<div class='fscf_clear'></div>

		<?php
		// If Preview is selected, preview the form.  Otherwise display the settings menu
		if ( $preview ) {
			echo FSCF_Display::process_short_code( array('form' => self::$current_form ) );
		}
		else {
		?>

		<form id="fscf-optionsform" name="fscf-optionsform" class="fscf_clear" action="options.php" method="post" enctype="multipart/form-data">
              <?php wp_nonce_field( 'fs_contact_options-options', 'fs_options' ); ?>
			<div>
			<input type="hidden" name="form-changed" id="form-changed" value="0"/>
			<input type="hidden" id="cur_tab" name="current_tab" value="<?php echo self::$current_tab; ?>"/>
			<input type="hidden" id="admin_url" value="<?php echo admin_url(); ?>"/>
			</div><div id="fscf-tabs">
			<ul id="fscf-tab-list">
			<?php
			// Display the tab labels
			$i = 1;
			for ( $i = 1; $i <= $num_tabs; $i++ ) {
				echo '<li id="fscf-tab'.$i.'"';
				// select the current tab
				echo '><a href="#fscf-tabs-' . $i . '">' . esc_html($tab_names[$i - 1]) . '</a></li> ';
			}
			?>
			</ul>

			<?php
			// Display the tab contents
			for ( $i = 1; $i <= $num_tabs; $i++ ) {
				echo '<div id="fscf-tabs-' . $i . '">';

				settings_fields( 'fs_contact_options' );
				do_settings_sections( 'tab_page' . $i );
				if ( $i < $num_tabs ) {
//					submit_button();
                  // XXX if moving tabs around, you have may have to change 8 to a diff number, also make a change in  fscf-scripts-admin.js  if (tabId < 8) {
                  if ($i != 8) { // skip tab 8 for the tools tab
					?>
					<p class="submit">
					<input id="submit<?php echo $i; ?>" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
					</p>
					<?php
                   }
				}
				echo ' </div>';
			}
			?>
			</div>
		<!-- </form> -->
		<?php
		?>
	</div>

   <p><strong><?php _e('WordPress plugins by Mike Challis:', 'si-contact-form') ?></strong></p>
<ul>
<li><a href="https://wordpress.org/plugins/si-contact-form/" target="_blank"><?php echo __('Fast Secure Contact Form', 'si-contact-form'); ?></a></li>
<li><a href="https://wordpress.org/plugins/fast-secure-recaptcha/" target="_blank"><?php echo __('Fast Secure reCAPTCHA', 'si-contact-form'); ?></a></li>
<li><a href="https://wordpress.org/plugins/si-captcha-for-wordpress/" target="_blank"><?php echo __('SI CAPTCHA Anti-Spam', 'si-contact-form'); ?></a></li>
<li><a href="https://wordpress.org/plugins/visitor-maps/" target="_blank"><?php echo __('Visitor Maps and Who\'s Online', 'si-contact-form'); ?></a></li>
</ul>

		<?php
		}
	}	// end function display_options()

	/* ------------------------------------------------------------------------ *
	 * ********** Section Callbacks **********
	 * ------------------------------------------------------------------------ */

	static function basic_settings_callback() {
//		echo "This is the basic settings section"; ?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
		<?php // See if old settings have been imported
		if ( ! empty(self::$global_options['import_msg']) ) {
			if ( self::$global_options['import_success'] ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Settings have been imported from a previous version. Please check over the settings to be sure that everything is all right. Also, please go to the Tools tab and click "Reset default style settings on all forms" so that all the new style changes are set for your imported forms.', 'si-contact-form' );
				$msg_class = 'fsc-notice';
			} else {
				echo '<div id="message" class="error fade"><p>' . __( 'A previous version was detected, but settings could not be imported because it is too old. Installed as new.', 'si-contact-form' );
				$msg_class = 'fsc-error';
			}
			echo '<br />('. __('You can dismiss this message on the Basic Settings tab', 'si-contact-form').')</p></div>';
			echo '<div class="' . $msg_class . '"><p>';
			?>
		<input name="fs_dismiss_import_msg" id="fs_dismiss_import_msg" type="checkbox" value="true" />
		<label for="fs_dismiss_import_msg"><?php _e('Dismiss message about import of settings', 'si-contact-form'); ?></label>
			<?php
			echo  '</p></div>';
		}
		?>
		<div class="fscf_right">
		<?php
		// Display an ad if the user hasn't donated
		if ( 'true' != self::$global_options['donated'] ) {
			echo '<div class="fscf_ad">';
			echo '<div>
		<h3>' . __('Donate', 'si-contact-form') . '</h3>'
		. __('Please donate to keep this plugin FREE', 'si-contact-form') . '<br />'
		. __('If you find this plugin useful to you, please consider making a small donation to help contribute to my time invested and to further development. Thanks for your kind support!', 'si-contact-form') . ' '
		. '- <a style="cursor:pointer;" title="' . esc_attr__('More from Mike Challis', 'si-contact-form')
		. '" onclick="toggleVisibility(\'si_contact_mike_challis_tip\')"><br />' .  __('You have 1 message from Mike Challis', 'si-contact-form') .'</a>
	<br /> <br />
	</div>
	<a href="https://www.fastsecurecontactform.com/donate">
	<img src="' . FSCF_URL . 'includes/btn_donate_LG.gif" width="92" height="26"/></a>
	<br /><br /></div>';

		}

		?>
		<div class="fscf_ad">
		<?php
        if ( 'true' != self::$global_options['donated'] ) {
          _e('Enabling this setting removes this donation message.', 'si-contact-form'); echo '<br /><br />';
        }
        ?>
        <input name="fs_contact_donated" id="fs_contact_donated" type="checkbox" <?php if( self::$global_options['donated'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_donated"><?php _e('I have donated to help contribute to the development of this awesome plugin. This checkbox makes the donate button go away', 'si-contact-form');?></label>
		</div></div>

		<div class="fscf_tip" id="si_contact_mike_challis_tip">
		<img src="<?php echo plugins_url( 'si-contact-form/includes/si-contact-form.jpg' ); ?>" class="fscf_left fscf_img" width="250" height="185" alt="Mike Challis" /><br />
		<?php _e('Mike Challis says: "If you are satisfied with this plugin, please consider making a donation.', 'si-contact-form'); ?>
		<?php echo ' '; _e('Suggested donation: $50, $25, $20, $15, $10, $5. Donations can be made with your PayPal account, or securely using any of the major credit cards. If you are not able to donate, that is OK. Please also review my plugin."', 'si-contact-form'); ?>
		 <br /><a href="https://wordpress.org/support/plugin/si-contact-form/reviews/?rate=5#new-post" target="_blank"><?php _e('Review this plugin now', 'si-contact-form'); ?></a>.
		<br /><br />
		<a style="cursor:pointer;" title="Close" onclick="toggleVisibility('si_contact_mike_challis_tip');"><?php _e('Close this message', 'si-contact-form'); ?></a>

		<div class="fscf_clear_left"></div>
        <br />
		</div>

		<div class="fscf_tab_content">
		<p><strong><?php _e('Usage:', 'si-contact-form'); ?></strong>
		<?php _e('Add the shortcode in a Page, Post, or Text Widget', 'si-contact-form'); ?>.
		<a href="<?php echo FSCF_URL . 'screenshot-4.gif'; ?>" target="_new"><?php _e('help', 'si-contact-form'); ?></a>
		<br /><br />
		<?php _e('Shortcode for this form:', 'si-contact-form'); echo "<br />[si-contact-form form='". self::$current_form ."']"; ?>
		</p>

		<?php _e('These are the basic settings.  If you want to create a simple contact form, with default settings, you only need to fill out the form label and enter an email address below.', 'si-contact-form'); ?>
		<br /><br />

        <label for="fs_contact_form_name"><?php echo sprintf(__('Form %d label', 'si-contact-form'),self::$current_form) ?>:</label><br />
		<input name="<?php echo self::$form_option_name;?>[form_name]" id="fs_contact_form_name" type="text"
			   value="<?php echo esc_attr(self::$form_options['form_name']);  ?>" size="35" />
		<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_form_name_tip');"><?php _e('help', 'si-contact-form'); ?></a>
		<div class="fscf_tip" id="si_contact_form_name_tip">
		<?php _e('Enter a name for your form. This is not shown on the form, it just helps you keep track of what you are using it for.', 'si-contact-form'); ?>
		</div>

		<div><br />
        <label for="fs_contact_welcome"><?php _e('Welcome introduction', 'si-contact-form'); ?>:</label><br />
        <textarea rows="6" cols="70" name="<?php echo self::$form_option_name;?>[welcome]" id="fs_contact_welcome"><?php echo esc_textarea(self::$form_options['welcome']); ?></textarea>
        <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_welcome_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        </div><div class="fscf_tip" id="si_contact_welcome_tip">
        <?php _e('This is printed before the form. HTML is allowed.', 'si-contact-form');?>
        </div>


        </div>

		</fieldset>
		<?php
		}

	static function email_settings_callback() {
		// Display email settings
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
		<?php

		// checks for properly configured Email To: addresses in options.
		$ctf_contacts = array( );
		$ctf_contacts_test = trim( self::$form_options['email_to'] );
		$ctf_contacts_error = 0;
		if ( !preg_match( "/,/", $ctf_contacts_test ) ) {
			if ( FSCF_Util::validate_email( $ctf_contacts_test ) ) {
				// user1@example.com
				$ctf_contacts[] = array( 'CONTACT'	 => __( 'Webmaster', 'si-contact-form' ), 'EMAIL'		 => $ctf_contacts_test );
			}
		} else {
			$ctf_ct_arr = explode( "\n", $ctf_contacts_test );
			if ( is_array( $ctf_ct_arr ) ) {
				foreach ( $ctf_ct_arr as $line ) {
					// echo '|'.$line.'|' ;
					list($key, $value) = preg_split( '#(?<!\\\)\,#', $line ); //string will be split by "," but "\," will be ignored
					$key = trim( str_replace( '\,', ',', $key ) ); // "\," changes to ","
					$value = trim( $value );
					if ( $key != '' && $value != '' ) {
						if ( !preg_match( "/;/", $value ) ) {
							// just one email here
							// Webmaster,user1@example.com
							$value = str_replace( '[cc]', '', $value );
							$value = str_replace( '[bcc]', '', $value );
							if ( FSCF_Util::validate_email( $value ) ) {
								$ctf_contacts[] = array( 'CONTACT'	 => $key, 'EMAIL'		 => $value );
							} else {
								$ctf_contacts_error = 1;
							}
						} else {
							// multiple emails here (additional ones will be Cc:)
							// Webmaster,user1@example.com;user2@example.com;user3@example.com;[cc]user4@example.com;[bcc]user5@example.com
							$multi_cc_arr = explode( ";", $value );
							$multi_cc_string = '';
							foreach ( $multi_cc_arr as $multi_cc ) {
								$multi_cc_t = str_replace( '[cc]', '', $multi_cc );
								$multi_cc_t = str_replace( '[bcc]', '', $multi_cc_t );
								if ( FSCF_Util::validate_email( $multi_cc_t ) ) {
									$multi_cc_string .= "$multi_cc,";
								} else {
									$ctf_contacts_error = 1;
								}
							}
							if ( $multi_cc_string != '' ) {  // multi cc emails
								$ctf_contacts[] = array( 'CONTACT'	 => $key, 'EMAIL'		 => rtrim( $multi_cc_string, ',' ) );
							}
						}
					}
				} // end foreach
			} // end if (is_array($ctf_ct_arr) ) {
		} // end else
		//print_r($ctf_contacts);
		?>
		
		<?php
		if ( empty( $ctf_contacts ) || $ctf_contacts_error ) {
			echo '<div id="message" class="error">';
			echo __( 'ERROR: Misconfigured "Email To" address.', 'si-contact-form' );
			echo "</div>\n";
			echo '<div class="fsc-error">' . __( 'ERROR: Misconfigured "Email To" address.', 'si-contact-form' ) . '</div>' . "\n";
		}

		if ( !function_exists( 'mail' ) ) {
			echo '<div class="fsc-error">' . __( 'Warning: Your web host has the mail() function disabled. PHP cannot send email.', 'si-contact-form' );
			echo ' ' . __( 'Have them fix it. Or you can install the "WP Mail SMTP" plugin and configure it to use SMTP.', 'si-contact-form' ) . '</div>' . "\n";
		}
		?>
		<label for="fs_contact_email_to"><?php _e( 'Email To', 'si-contact-form' ); ?>:</label><br />
		<textarea rows="6" cols="70" name="<?php echo self::$form_option_name;?>[email_to]" id="fs_contact_email_to"><?php echo esc_html( self::$form_options['email_to'] ); ?></textarea>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_to_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_to_tip">
		<?php _e( 'Email address the messages are sent to (your email). For basic operation, just enter an email address or accept the suggested address already filled in for you.', 'si-contact-form' ); ?><br /><br />
		<?php _e( 'If you add multiple email addresses, a drop down list will be created on the contact form. Each contact can have a name and an email address separated by a comma. Separate each contact by pressing enter. If you need to add more than one contact, follow this example:', 'si-contact-form' ); ?><br /><?php _e( 'Email address the messages are sent to (your email). For basic operation, just enter an email address. If you add multiple email addresses, a drop down list will be created on the contact form. Each contact can have a name and an email address separated by a comma. Separate each contact by pressing enter. If you need to add more than one contact, follow this example:', 'si-contact-form' ); ?><br />
		<?php _e( 'If you need to use a comma in the name, escape it with a back slash, like this: \,', 'si-contact-form' ); ?><br />
		Webmaster,user1@example.com<br />
		Sales,user2@example.com<br /><br />

		<?php echo __( 'You can have multiple emails per contact using [cc]Carbon Copy. Separate each email with a semicolon. Follow this example:', 'si-contact-form' ); ?><br />
		Sales,user3@example.com;user4@example.com;user5@example.com<br /><br />

		<?php echo __( 'You can specify [cc]Carbon Copy or [bcc]Blind Carbon Copy by using tags. Separate each email with a semicolon. Follow this example:', 'si-contact-form' ); ?><br />
		Sales,user3@example.com;[cc]user1@example.com;[cc]user2@example.com;[bcc]user3@example.com;[bcc]user4@example.com
		</div>
		<br />


  		<?php
		if ( self::$form_options['email_bcc'] != '' ) {
			$bcc_fail = 0;
			if ( !preg_match( "/,/", self::$form_options['email_bcc'] ) ) {
				// just one email here
				// user1@example.com
				if ( ! FSCF_Util::validate_email( self::$form_options['email_bcc'] ) ) {
					$bcc_fail = 1;
				}
			} else {
				// multiple emails here
				// user1@example.com,user2@example.com
				$bcc_arr = explode( ",", self::$form_options['email_bcc'] );
				foreach ( $bcc_arr as $b_cc ) {
					if ( ! FSCF_Util::validate_email( $b_cc ) ) {
						$bcc_fail = 1;
						break;
					}
				}
			}
			if ( $bcc_fail ) {
				echo '<div id="message" class="error">';
				echo __( 'ERROR: Misconfigured "Bcc Email" address.', 'si-contact-form' );
				echo "</div>\n";
				echo '<div class="fsc-error">' . __( 'ERROR: Misconfigured "Bcc Email" address.', 'si-contact-form' ) . '</div>' . "\n";
			}
		}
		?>
        <br />

		<label for="fs_contact_email_bcc"><?php _e( 'Email Bcc address (optional)', 'si-contact-form' ); ?>:</label><br />
		<input name="<?php echo self::$form_option_name; ?>[email_bcc]" id="fs_contact_email_bcc" type="text" value="<?php echo esc_attr( self::$form_options['email_bcc'] ); ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_bcc_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_bcc_tip">
		<?php _e( 'Email address(s) to receive Bcc (Blind Carbon Copy) messages. You can send to multiple or single, both methods are acceptable:', 'si-contact-form' ); ?>
		<br />
		user1@example.com<br />
		user1@example.com,user2@example.com
		</div>
		<br />
        <br />

		<label for="fs_contact_email_subject"><?php _e( 'Email Subject Prefix', 'si-contact-form' ) ?>:</label><br />
		<input name="<?php echo self::$form_option_name; ?>[email_subject]" id="fs_contact_email_subject" type="text" value="<?php echo esc_attr( self::$form_options['email_subject'] ); ?>" size="55" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_subject_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_subject_tip">
		<?php _e( 'This will become a prefix of the subject for the email you receive.', 'si-contact-form' ); ?>
		<?php _e( 'Listed below is an optional list of field tags for fields you can add to the subject.', 'si-contact-form' ) ?><br />
		<?php _e( 'Example: to include the name of the form sender, include this tag in the email Subject Prefix:', 'si-contact-form' ); ?> [from_name]<br />
		<?php _e( 'Available field tags:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_subj_arr as $i )
			echo "[$i]<br />";
		?>
		</span>
		</div>
		<br />



		<?php

		if ( self::$form_options['email_from'] != '' ) {
			$from_fail = 0;
			if ( !preg_match( "/,/", self::$form_options['email_from'] ) ) {
				// just one email here
				// user1@example.com
				if ( !FSCF_Util::validate_email( self::$form_options['email_from'] ) ) {
					$from_fail = 1;
				}
			} else {
				// name and email here
				// webmaster,user1@example.com
				list($key, $value) = explode( ",", self::$form_options['email_from'] );
				$key = trim( $key );
				$value = trim( $value );
				if ( ! FSCF_Util::validate_email( $value ) ) {
					$from_fail = 1;
				}
			}

			if ( $from_fail ) {
				echo '<div id="message" class="error">';
				echo __( 'ERROR: Misconfigured "Return-path address".', 'si-contact-form' );
				echo "</div>\n";
				echo '<div class="fsc-error">' . __( 'ERROR: Misconfigured "Return-path address".', 'si-contact-form' ) . '</div>' . "\n";
			}
		}
		?>

        <br />
		<label for="fs_contact_email_from"><?php _e( 'Return-path address (recommended)', 'si-contact-form' ); ?>:</label> <br /><?php _e( 'Set this to a real email address on the SAME domain as your web site.', 'si-contact-form' ); ?><br />
        <?php _e( 'For best results, the "Email To" and the "Return-path address" should be separate email addresses on the SAME DOMAIN as your web site.', 'si-contact-form' ); ?><br />
		<input name="<?php echo self::$form_option_name;?>[email_from]" id="fs_contact_email_from" type="text" value="<?php echo esc_attr( self::$form_options['email_from'] ); ?>" size="40" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_from_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_from_tip">
        <?php _e('Return-path address sets an email header for the address the messages are sent from. Some web hosts do not allow PHP to send email unless the Return-path email address header is set. It must be set to a real email address on your site domain, or mail will NOT SEND! (They do this to help prevent spam.)', 'si-contact-form' ); ?>
        <?php _e('This setting is required by Yahoo, AOL, Comcast, Dreamhost, GoDaddy, and most others now.', 'si-contact-form' ); ?>
		<br />
		<?php _e( 'Enter just an email: user1@example.com', 'si-contact-form' ); ?><br />
		<?php _e( 'Or enter name and email: webmaster,user1@example.com', 'si-contact-form' ); ?>
		</div>
		<br />
        <br />
					
		<?php
		if ( self::$form_options['email_from_enforced'] == 'true' && self::$form_options['email_from'] == '' ) {
			echo '<div class="fsc-error">';
			echo __( 'Warning: Enabling this setting requires the "Return-path address" setting above to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
		<input name="<?php echo self::$form_option_name;?>[email_from_enforced]" id="fs_contact_email_from_enforced" type="checkbox" <?php if ( self::$form_options['email_from_enforced'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_from_enforced"><?php _e( 'Enable when web host requires "Mail From" strictly tied to site. (recommended, <a href="http://www.fastsecurecontactform.com/yahoo-com-dmarc-policy" target="_new">see FAQ</a>)', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_from_enforced_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_from_enforced_tip">
        <?php _e( 'This setting is for DMARC Compliance and is required by Yahoo, AOL, Comcast, Dreamhost, GoDaddy, and most others now.', 'si-contact-form' ); ?><br />
		<?php _e( 'The email will appear to be from your site email address, but because the email header "Reply-to" is set as the form user\'s email address, you should be able to just hit reply and email back to the real sender. Also you will see the sender address in the message content, so it is still possible to send mail to them if the "Reply-to" is ignored by your email program.', 'si-contact-form' ) ?>
		</div>
		<br />
        <br />

		<label for="fs_contact_email_reply_to"><?php _e( 'Custom Reply To (optional, rarely needed)', 'si-contact-form' ); ?>:</label><br />
		<input name="<?php echo self::$form_option_name;?>[email_reply_to]" id="fs_contact_email_reply_to" type="text" value="<?php echo esc_attr( self::$form_options['email_reply_to'] ); ?>" size="40" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_reply_to_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_reply_to_tip">
		<?php _e( 'Leave this setting blank for most forms because the "reply to" is set automatically to the user email. Only use this setting if you are using the form for a mailing list and you do NOT want the reply going to the form user.', 'si-contact-form' ); ?>
		<?php _e( 'Defines the email address that is automatically inserted into the "To:" field when a user replies to an email message.', 'si-contact-form' ); ?>
		<br />
		<?php _e( 'Enter just an email: user1@example.com', 'si-contact-form' ); ?><br />
		</div>
		<br />
        <br />
        <strong><?php _e( 'More email settings are located on the Advanced tab', 'si-contact-form' ); ?></strong><br />

		</fieldset>
		<?php
	}		// email_settings_callback

	static function field_settings_callback() {
//		echo "This is the field settings section";
		$field_type_array = array(
			'text'				 => __( 'text', 'si-contact-form' ),
			'textarea'			 => __( 'textarea', 'si-contact-form' ),
			'checkbox'			 => __( 'checkbox', 'si-contact-form' ),
			'checkbox-multiple'	 => __( 'checkbox-multiple', 'si-contact-form' ),
			'radio'				 => __( 'radio', 'si-contact-form' ),
			'select'			 => __( 'select', 'si-contact-form' ),
			'select-multiple'	 => __( 'select-multiple', 'si-contact-form' ),
			'attachment'		 => __( 'attachment', 'si-contact-form' ),
			'date'				 => __( 'date', 'si-contact-form' ),
			'time'				 => __( 'time', 'si-contact-form' ),
			'email'				 => __( 'email', 'si-contact-form' ),
			'url'				 => __( 'url', 'si-contact-form' ),
			'hidden'			 => __( 'hidden', 'si-contact-form' ),
			'password'			 => __( 'password', 'si-contact-form' ),
			'fieldset'			 => __( 'fieldset(box-open)', 'si-contact-form' ),
			'fieldset-close'	 => __( 'fieldset(box-close)', 'si-contact-form' )
		);
		$select_type_fields = array(
			'checkbox-multiple',
			'select',
			'select-multiple',
			'radio'
		);

		// Display the field options ?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group fscf_field_settings">
        <div>
        <?php if( empty(self::$new_field_added) ) { ?>
        <div class="fscf_right" style="padding:7px;"><input type="button" class="button-primary" name="new_field" value="<?php esc_attr_e('Add New Field', 'si-contact-form'); ?>" onclick="fscf_add_field('<?php esc_attr_e('Add Field', 'si-contact-form'); ?>');" /></div>
        <?php  } ?>
        <p class="submit">
		<input id="submit2" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
        </div>
		<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_extra_fields_tip');">
		<strong><?php _e('View instructions for fields', 'si-contact-form'); ?></strong></a>
		<div class="fscf_tip" id="si_contact_extra_fields_tip"><br />

        <strong><?php _e('Instructions for how to use Fields:', 'si-contact-form'); ?></strong>
		<blockquote><ul>
			<li><?php _e('Below are the settings for individual form fields.  Click on the "Show Details" button to the right of the field name to see the details for that field. ', 'si-contact-form');  ?></li>
			<li><?php _e('You can change the order in which the fields appear on the form.  Simply click on a field somewhere outside any of the text boxes and drag it into the position you desire.  When you click Save Changes, the new field order will be saved.', 'si-contact-form'); ?></li>
			<li><?php _e('You can also add extra fields of your own design for phone number, company name, etc. ', 'si-contact-form');  ?>
			<?php _e("To create an extra field, just click on the 'Add New Field' button on the right. Then choose the settings, including whether you want the field to be required or not. There is a checkbox to temporarily disable a field. If you don't need it any more, you can permanently delete a field with the Delete Field button.", 'si-contact-form'); ?></li>
			<li><?php _e('Field Labels must be unique.  The Labels on the default standard fields (Name, Email, Subject, Message) cannot be changed here, but you can change the label that displays on the form on the Labels tab.', 'si-contact-form'); ?></li>
			</ul></blockquote>

            <p><strong><?php _e('Field types:', 'si-contact-form'); ?></strong></p>
			<blockquote>

            <p><strong><?php _e('Text and Textarea fields:', 'si-contact-form'); ?></strong><br />
			<?php _e('The text field is for single line text entry. The textarea field is for multiple line text entry.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Checkbox field:', 'si-contact-form'); ?></strong><br />
			<?php _e('This is a single checkbox.  The field name is displayed next to the checkbox.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Checkbox-multiple, Radio, Select, and Select-multiple fields:', 'si-contact-form'); ?></strong><br />
			<?php _e('These allow the user to select from a list of options.', 'si-contact-form'); ?>
			<?php _e('The options are entered in the "Select options" box, one per line.  You can also set the default selection with "Default option."', 'si-contact-form'); ?>
			<?php _e('You can use select-multiple and checkbox-multiple to allow the user to select more than one option from the list.', 'si-contact-form'); ?>
			<?php _e('By default, radio and checkboxes are displayed vertically, one per line. To make them display horizontally, check the "Inline" box.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Attachment field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The attachment field is used to allow users to attach a file upload to the form. The attachment is sent with your email, then deleted from the server after the email is sent. You can add multiple attachment fields.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Date field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The date field shows a calendar pop-up to allow the user to select a date. This ensures that a date entry is in a standard format every time.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Time field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The time field is used to allow a time entry field with hours, minutes, and AM/PM. The time field ensures that a time entry is in a standard format.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Email field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The email field is used to allow an email address entry field. The email field ensures that a email entry is in a valid email format.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('URL field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The URL field is used to allow a URL entry field. The URL field ensures that a URL entry is in a valid URL format.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Hidden field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The hidden field is used if you need to pass a hidden value from the form to the email message. The hidden field does not show on the page. You must set the field name and default value.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Password field:', 'si-contact-form'); ?></strong><br />
			<?php _e('The password field is used for a text field where what is entered shows up as dots on the screen. The email you receive will have the entered value fully visible.', 'si-contact-form'); ?></p>

            <p><strong><?php _e('Fieldset:', 'si-contact-form'); ?></strong><br />
			<?php _e('The fieldset(box-open) is used to draw a box around related form elements. The field name is used for a title (legend) for the group.', 'si-contact-form'); ?>
			<br />

            <?php _e('The fieldset(box-close) is used to close a box around related form elements. A label is not required for this type. If you do not close a fieldset box, it will close automatically when you add another fieldset box.', 'si-contact-form'); ?></p>
			</blockquote>

			<p><strong><?php echo __('Field Properties:', 'si-contact-form'); ?></strong></p>
			<blockquote>

            <p><i><?php echo __('Some of these field properties apply only to certain field types.  Properties irrelevant for the selected field type will be ignored.', 'si-contact-form'); ?>
			</i></p>

            <p><strong><?php echo __('Tag:', 'si-contact-form'); ?></strong><br />
			<?php echo __('The field Tag is used to identify the field for email settings, shortcodes, and query variables.  Field tags must be unique.  If you leave the Tag entry blank, one will be generated for you based on the field name.  If you change a field name, you might want to change the tag to match.  Or just delete the tag, and a new one will be generated for you.', 'si-contact-form'); ?></p>

			<p><strong><?php echo __('Default:', 'si-contact-form'); ?></strong><br />
            <?php echo __('Use to pre-fill a value for the field. For select and radio fields, enter the number of the option to pre-select (1 = first item, etc.).  For select-multiple and checkbox-multiple, enter the list item number(s) to pre-select separated by commas.  For a checkbox, enter "1" to pre-check the box.', 'si-contact-form'); ?>
            <?php echo ' '; echo __('For a date field, you can enter any date in the configured format. Or to show today\'s date as default, just put the word today in brackets. example: [today].', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Default as placeholder:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Check this setting if you want the default text to be a placeholder inside the form field. The placeholder is a short hint that is displayed in the input field before the user enters a value. Works with the following input types only: name, email, subject, message, text, textarea, url, and password. This setting is sometimes used along with the "Hide label" setting.', 'si-contact-form'); ?>
            <?php echo ' <br /><br />'; echo __('When using "Default as placeholder" setting with "Enable double email entry" setting enabled. The "Default" setting should be in this example format: "Email==Re-enter Email". Separate words with == separators.', 'si-contact-form'); ?>
            <?php echo ' <br /><br />'; echo __('When using "Default as placeholder" setting with "First Name, Last Name" setting enabled. The "Default" setting should be in this example format: "First Name==Last Name". Separate words with == separators.', 'si-contact-form'); ?>
            <?php echo ' <br /><br />'; echo __('When using "Default as placeholder" setting with "First Name, Middle Name, Last Name" setting enabled. The "Default" setting should be in this example format: "First Name==Middle Name==Last Name". Separate words with == separators.', 'si-contact-form'); ?>
            </p>

            <p><strong><?php echo __('Hide label:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Check this setting if you want to hide the field label on the form. This is sometimes used along with the "Default as placeholder" setting.', 'si-contact-form'); ?></p>


			<p><strong><?php echo __('Select options:', 'si-contact-form'); ?></strong><br />
			<?php echo __('List of options for select, select-multiple, radio, and checkbox-multiple field types.  Type the options, one per line. This entry is required for these field types.', 'si-contact-form'); ?>
            <?php echo ' '; echo __('The first option of a select field type can be in brackets to indicate that it must be selected, example: [Please select].', 'si-contact-form'); ?>
            <?php echo ' '; echo __('If you add options as a key==value set (use == to separate) the value will show on the form and the key will show in the email.', 'si-contact-form'); ?>
            </p>

			<p><strong><?php echo __('Inline:', 'si-contact-form'); ?></strong><br />
            <?php echo __('If checked, checkboxes and radio buttons appear horizontally on one line instead of vertically one per line.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Max length:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Use to limit the number of allowed characters for a text field. The limit will be checked when the form is posted. Can be used for text, textarea, and password field types.', 'si-contact-form'); ?>
			<?php echo __(' This will not change the size of the field on the form.  To change that, use the size attribute (see below), or your add a width attribute to the "Input text fields" setting on the Styles tab.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Required field:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Check this setting if you want the field to be required when the form is posted. Can be used for any field type.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Disable field:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Check this setting if you do not want the field to appear on the form. Can be used for any field type.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Attributes:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Use to insert input field attributes. Example: To make a text field readonly, set to: readonly="readonly"  To set the size of a field to 15 characters, use size=15. Can be used for any field type.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Validation regex:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Use a regular expression to validate if form input is in a specific format. Example: If you want numbers in a text field type but do not allow text, use this regex: /^\d+$/ Can be used for text, textarea, date and password field types.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Regex fail message:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Use to customize a message to alert the user when the form fails to validate a regex after post. Example: Please only enter numbers. For use with validation regex only.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Label CSS/Input CSS:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Use to style individual form fields with CSS. CSS class names or inline style code are both acceptable. Note: If you do not need to style fields individually, you should use the CSS settings on the Styles tab instead.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('HTML before/after field:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Use the HTML before/after field to print some HTML before or after a field on the form. This is for the form display only, not email. HTML is allowed.', 'si-contact-form'); ?></p>

            <p><strong><?php echo __('Follow previous field:', 'si-contact-form'); ?></strong><br />
			<?php echo __('Check this setting if you want this field to show on the form following the previous field on the same line. For example, you could put state, and zip on one line. This feature seems to be limited to two fields, and it is only compatible with the "labels on top" style on a wide theme.', 'si-contact-form'); ?></p>
		</blockquote>
		</div>
		<div class="clear"></div>

		<?php

        // none of the field slugs can be the same as a post type rewrite_slug
        // or you will get "page not found" when posting the form with that field filled in
        self::get_post_types_slugs();
        $slug_list = array();
        foreach ( self::$form_options['fields'] as $key => $field ) {
          $slug_list[] = $field['slug'];
        }
        $bad_slug_list = array();
        foreach (self::$post_types_slugs as $key => $slug) {
            if ( in_array( strtolower( $slug ), $slug_list ) ) {
              echo '<div id="message" class="error">';
			  echo sprintf( __( 'Warning: one of your field tags conflicts with the post type redirect tag "%s". To automatically correct this, click the <b>Save Changes</b> button.', 'si-contact-form' ), $slug );
			  echo "</div>\n";
              $bad_slug_list[] = $slug;
            }
        }

         // fill in any missing defaults
        $field_opt_defaults = array(
          'hide_label'	 => 'false',
          'placeholder'	 => 'false',
         );
        $placeholder_error = 0;
        $name_format_error = 0;
        $email_format_error = 0;
        $dup_field_error = 0;
		$field_names = array();
        $fields_count = count(self::$form_options['fields']);
		foreach ( self::$form_options['fields'] as $key => $field ) {
			$field_opt_name = self::$form_option_name . '[fields][' . $key . ']';

            // fill in any missing field options defaults
		    foreach ( $field_opt_defaults as $dfkey => $dfval ) {
                if ( !isset($field[$dfkey]) || empty($field[$dfkey]) )
				      $field[$dfkey] = $dfval;
		    }
			?>

			<fieldset class="fscf_field" id="field-<?php echo $key+1; ?>">

			<legend><b><?php
            $label_changed = 0;
            // are there label overrides for standard field names? standard field labels can be renamed on the labels tab
            if ( FSCF_NAME_FIELD == $field['standard'] ) {
                    if (self::$form_options['title_name'] != '') { $label_changed = 1; echo self::$form_options['title_name']; }else{ echo esc_html($field['label']); }
			} else if ( FSCF_EMAIL_FIELD == $field['standard'] ) {
                    if (self::$form_options['title_email'] != '') { $label_changed = 1; echo self::$form_options['title_email']; }else{
                    //echo esc_html($field['label']);
                    echo 'Email:';        // correction for old forms where it was Email Address:
                    $field['label'] = 'Email:';
                    }
            } else if ( FSCF_SUBJECT_FIELD == $field['standard'] ) {
                    if (self::$form_options['title_subj'] != '') { $label_changed = 1; echo self::$form_options['title_subj']; }else{ echo esc_html($field['label']); }
            } else if ( FSCF_MESSAGE_FIELD == $field['standard'] ) {
                    if (self::$form_options['title_mess'] != '') { $label_changed = 1; echo self::$form_options['title_mess']; }else{ echo esc_html($field['label']); }
			} else {
                    echo esc_html($field['label']);
            }
            ?></b> <?php
            if ( '0' != $field['standard'] ) {
                if ($label_changed)
                _e('(standard field name was changed on the Labels tab)', 'si-contact-form');
                else
                 _e('(standard field)', 'si-contact-form');
             } ?></legend>

			<input name="<?php echo $field_opt_name.'[standard]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_standard'; ?>" type="hidden" 
				   value="<?php echo esc_attr($field['standard']);  ?>" />
			<input name="<?php echo $field_opt_name.'[delete]' ?>" id="delete-<?php echo +$key+1; ?>" type="hidden"
				   value="false" />
			<?php
            // special notices

			// new field added message
			if ( !empty(self::$new_field_added) && $fields_count == $key+1 ) {
				// A new field was added, show a message
				echo '<div class="fsc-notice">' . self::$new_field_added . '</div>' . "\n";
                self::$new_field_key = $key+1;
			}
            if ( in_array( strtolower( $field['slug'] ), $bad_slug_list ) ) {
              echo '<div class="fsc-error">' . sprintf( __( 'Warning: one of your field tags conflicts with the post type redirect tag "%s". To automatically correct this, click the <b>Save Changes</b> button.', 'si-contact-form' ), $field['slug'] )  . '</div>' . "\n";
            }


            // warn if placeholder is missing the Default text
			if ( $field['placeholder'] == 'true' && $field['default'] == '' ) {
               if (!$placeholder_error) {
				  echo '<div class="updated">';
				  echo __( 'Caution: "Default as placeholder" setting requires "Default" setting to be filled in. Correct this on the Fields tab and click <b>Save Changes</b>', 'si-contact-form' );
				  echo "</div>\n";
                }
				echo '<div class="fsc-notice">' . __( 'Caution: "Default as placeholder" setting requires "Default" setting to be filled in. Correct this in the field details and click <b>Save Changes</b>', 'si-contact-form' ) . '</div>' . "\n";
                $placeholder_error = 1;
			}

           // warn if name default not in proper format
          if ( FSCF_NAME_FIELD == $field['standard'] ) {
                $name_format_array = array(
					'name'					 => __( 'Name', 'si-contact-form' ),
					'first_last'			 => __( 'First Name, Last Name', 'si-contact-form' ),
					'first_middle_i_last'	 => __( 'First Name, Middle Initial, Last Name', 'si-contact-form' ),
					'first_middle_last'		 => __( 'First Name, Middle Name, Last Name', 'si-contact-form' ),
				);
             if ( $field['default'] != '' && self::$form_options['name_format'] == 'first_last' ) {
                  if ( !preg_match('/^(.*)(==)(.*)$/', $field['default'], $matches) )
                      $name_format_error = 'First Name==Last Name';
             } else if ( $field['default'] != '' && self::$form_options['name_format'] == 'first_middle_last' ) {
                  if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $field['default'], $matches) )
                       $name_format_error = 'First Name==Middle Name==Last Name';
             } else if ( $field['default'] != '' && self::$form_options['name_format'] == 'first_middle_i_last' ) {
                  if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $field['default'], $matches) )
                       $name_format_error = 'First Name==Middle Initial==Last Name';
             }
             if ($name_format_error) {
                $this_name_format = $name_format_array[self::$form_options['name_format']];
                echo '<div class="updated">';
			    echo sprintf( __( 'Caution: Name field format "%s" requires the "Default" setting to be in this example format: %s. Separate words with == separators, or empty the "Default" setting. Correct this on the Fields tab and click <b>Save Changes</b>', 'si-contact-form'), $this_name_format, $name_format_error  );
			    echo "</div>\n";
			    echo '<div class="fsc-notice">' . sprintf( __( 'Caution: Name field format "%s" requires the "Default" setting to be in this example format: %s. Separate words with == separators, or empty the "Default" setting. Correct this in the field details and click <b>Save Changes</b>', 'si-contact-form'), $this_name_format, $name_format_error ) . '</div>' . "\n";
             }
          }

          // warn if double email default not in proper format
          if ( FSCF_EMAIL_FIELD == $field['standard'] && 'true' == self::$form_options['double_email'] && $field['default'] != '' ) {
              if ( !preg_match('/^(.*)(==)(.*)$/', $field['default'], $matches) ) {
               echo '<div class="updated">';
			   echo __( 'Caution: When "Enable double email entry" setting is enabled, the "Default" setting should be in this example format: Email==Re-enter Email. Separate words with == separators, or empty the "Default" setting. Correct this on the Fields tab and click <b>Save Changes</b>', 'si-contact-form');
			   echo "</div>\n";
			   echo '<div class="fsc-notice">' . __( 'Caution: "When Enable double email entry" setting is enabled, the "Default" setting should be in this example format: Email==Re-enter Email. Separate words with == separators, or empty the "Default" setting. Correct this in the field details and click <b>Save Changes</b>', 'si-contact-form') . '</div>' . "\n";
             }
          }

			// Make sure field names are unique
			if ( in_array( $field['label'], $field_names ) ) {
				// We have a duplicate field label, display an error message
                if (!$dup_field_error) {
				  echo '<div class="updated">';
				  echo __( 'Caution: Duplicate field label. Now you must change the field label on the Fields tab and click <b>Save Changes</b>', 'si-contact-form' );
				  echo "</div>\n";
                }
				echo '<div class="fsc-notice">' . __( 'Caution: Duplicate field label. Change the field label and click <b>Save Changes</b>', 'si-contact-form' ) . '</div>' . "\n";
                $dup_field_error = 1;
			}
			$field_names[] = $field['label'];
			$k = +$key + 1;
			?>
			<label for="fs_contact_field<?php echo +$key+1; ?>_label"><?php _e('Label:', 'si-contact-form'); ?></label>
			<input name="<?php echo $field_opt_name.'[label]' ?>" id="fs_contact_field<?php echo +$key+1; ?>_label" type="text"
				   value="<?php echo esc_attr($field['label']);  ?>" size="70"
				   <?php if ( $field['standard'] > 0 ) echo ' readonly="readonly"'; ?>/>

			<label for="<?php echo 'fs_contact_field'. +$key+1 .'_type' ?>"><?php echo __('Field type:', 'si-contact-form'); ?></label>
			<?php // Disable field type select for name and message ?>
			<select id="<?php echo 'fs_contact_field'. +$key+1 .'_type' ?>"
				<?php 
				if ( FSCF_NAME_FIELD == $field['standard']  || FSCF_MESSAGE_FIELD == $field['standard'] ) {
					echo ' disabled="disabled">'; 
				} else {
					echo ' name="' . $field_opt_name.'[type]">'; 	
				}
				
			$selected = '';
			// Limit options for the Email and Subject fields
			if ( FSCF_EMAIL_FIELD == $field['standard'] ) {
				// Only allow 'text' and 'email' type options
				if ( $field['type'] == 'text' )  $selected = ' selected="selected"';
				echo '<option value="text"'.$selected.'>' . esc_html( __( 'text', 'si-contact-form' )) . '</option>'."\n";
				$selected = '';
				if ( $field['type'] == 'email' )  $selected = ' selected="selected"';
				echo '<option value="email"'.$selected.'>' . esc_html( __( 'email', 'si-contact-form' )) .'</option>'."\n";
			} else if ( FSCF_SUBJECT_FIELD == $field['standard'] ) {
				if ( $field['type'] == 'text' )  $selected = ' selected="selected"';
				echo '<option value="text"'.$selected.'>' . esc_html( __( 'text', 'si-contact-form' )) . '</option>'."\n";
				$selected = '';
				if ( $field['type'] == 'select' )  $selected = ' selected="selected"';
				echo '<option value="select"'.$selected.'>' . esc_html( __( 'select', 'si-contact-form' )) .'</option>'."\n";
			} else {
				foreach ( $field_type_array as $k => $v ) {
					if ( $field['type'] == "$k" )  $selected = ' selected="selected"';
					echo '<option value="'.esc_attr($k).'"'.$selected.'>'.esc_html($v).'</option>'."\n";
					$selected = '';
				}
			}
			?>
			</select>
			<?php 
			if ( FSCF_NAME_FIELD == $field['standard']  || FSCF_MESSAGE_FIELD == $field['standard'] ) {
				// Provide type field for disabled select lists ?>
				<input type="hidden" name="<?php echo $field_opt_name .'[type]'; ?>" value="<?php echo $field["type"]; ?>" />
			<?php } ?>
			&nbsp;&nbsp;
			<input name="fscf_show_fields" type="button" id="button<?php echo $key; ?>" value="<?php esc_attr_e( 'Show Details', 'si-contact-form' ); ?>" class="button" onclick="toggleVisibilitybutton('<?php echo $key; ?>')" />
			<?php if ( "true" == $field['disable'] ) echo '&nbsp;&nbsp<span class="fscf_warning_text">'. __('DISABLED', 'si-contact-form') .'</span>';
			?><br />
			<div id="field<?php echo $key; ?>" class="fscf_field_details">
            <?php
                if ( '0' != $field['standard'] ) {
                   _e('Standard field labels can be changed on the Labels tab.', 'si-contact-form'); echo '<br />';
                }
			    if ( FSCF_NAME_FIELD == $field['standard'] ) {
				// Add special fields for the Name field  ?>
				<label for="fs_contact_name_format"><?php _e( 'Name field format:', 'si-contact-form' ); ?></label>
				<select id="fs_contact_name_format" name="<?php echo self::$form_option_name; ?>[name_format]">
				<?php

				$selected = '';
				foreach ( $name_format_array as $k => $v ) {
					if ( self::$form_options['name_format'] == "$k" )
						$selected = ' selected="selected"';
					echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>' . "\n";
					$selected = '';
				}   ?>
				</select>
				<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_name_format_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
				<div class="fscf_tip" id="si_contact_name_format_tip">
				<?php _e( 'Select how the name field is formatted on the form.', 'si-contact-form' ); ?>
				</div>&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="<?php echo self::$form_option_name; ?>[auto_fill_enable]" id="fs_contact_auto_fill_enable" type="checkbox" <?php if ( self::$form_options['auto_fill_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
				<label for="fs_contact_auto_fill_enable"><?php _e( 'Enable auto form fill', 'si-contact-form' ); ?>.</label>
				<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_fill_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
				<div class="fscf_tip" id="si_contact_auto_fill_enable_tip">
					<?php _e( 'Auto form fill email address and name (username) on the contact form for logged in users who are not administrators.', 'si-contact-form' ); ?>
				</div>
				<br />
			<?php	
			} ?>

		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_slug' ?>"><?php echo __('Tag', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[slug]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_slug' ?>" type="text"
				  value="<?php echo esc_attr($field['slug']); ?>" <?php
				  if ( $field['standard'] != '0' ) echo ' readonly'; ?> size="45" />	
		   
		   &nbsp;&nbsp;&nbsp;<input name="<?php echo $field_opt_name.'[req]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_req' ?>" type="checkbox" 
			   <?php if( $field['req'] == 'true' || ( FSCF_EMAIL_FIELD == $field['standard'] && self::$form_options['double_email'] == 'true' ) ) echo 'checked="checked"'; ?> value="true" />
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_req' ?>"><?php _e('Required field', 'si-contact-form'); ?></label>&nbsp;&nbsp;

		   &nbsp;&nbsp;&nbsp;<input name="<?php echo $field_opt_name.'[disable]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_disable' ?>" type="checkbox" 
			   <?php if( $field['disable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_disable' ?>"><?php _e('Disable field', 'si-contact-form'); ?></label>

			&nbsp;&nbsp;&nbsp;<input name="<?php echo $field_opt_name.'[follow]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_follow' ?>" type="checkbox"
			   <?php if( $field['follow'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
			<label for="<?php echo 'fs_contact_field'. +$key+1 .'_follow' ?>"><?php _e('Follow previous field', 'si-contact-form'); ?></label><br />

		   <strong><?php echo __('Field modifiers', 'si-contact-form'); ?>:</strong>&nbsp;&nbsp;
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_default' ?>"><?php echo __('Default', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[default]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_default' ?>" type="text"
				  value="<?php echo esc_attr($field['default']);  ?>" size="45" />

           &nbsp;&nbsp;&nbsp;<input name="<?php echo $field_opt_name.'[hide_label]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_hide_label' ?>" type="checkbox"
			   <?php if( $field['hide_label'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
			<label for="<?php echo 'fs_contact_field'. +$key+1 .'_hide_label' ?>"><?php _e('Hide label', 'si-contact-form'); ?></label>

           &nbsp;&nbsp;&nbsp;<input name="<?php echo $field_opt_name.'[placeholder]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_placeholder' ?>" type="checkbox"
			   <?php if( $field['placeholder'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
			<label for="<?php echo 'fs_contact_field'. +$key+1 .'_placeholder' ?>"><?php _e('Default as placeholder', 'si-contact-form'); ?></label>


		   <div class="fscf-clear"></div>
			<div class="fscf_left">
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_options' ?>"><?php echo __('Select options', 'si-contact-form');
		   if ( in_array( $field['type'], $select_type_fields ) ) echo ' (Required)'; ?>:</label><br />
		   <textarea rows="6" cols="40" name="<?php echo $field_opt_name.'[options]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_options' ?>"><?php echo esc_textarea( trim($field['options']) ); ?></textarea></div>
		   &nbsp;&nbsp;&nbsp;<input name="<?php echo $field_opt_name.'[inline]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_inline' ?>" type="checkbox"
			   <?php if( $field['inline'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_inline' ?>"><?php _e('Inline', 'si-contact-form'); ?></label>&nbsp;&nbsp;&nbsp;

		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_max_len' ?>"><?php echo __('Max length', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[max_len]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_max_len' ?>" type="text" 
				  value="<?php echo esc_attr($field['max_len']);  ?>" size="2" />
		   
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_attributes' ?>"><?php echo __('Attributes', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[attributes]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_attributes' ?>" type="text" 
				  value="<?php echo esc_attr($field['attributes']);  ?>" size="20" />

		   <br /><label for="<?php echo 'fs_contact_field'. +$key+1 .'_regex' ?>"><?php echo __('Validation regex', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[regex]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_regex' ?>" type="text" 
				  value="<?php echo esc_attr($field['regex']);  ?>" size="20" /><br />

		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_regex_error' ?>"><?php echo __('Regex fail message', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[regex_error]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_regex_error' ?>" type="text" 
				  value="<?php echo esc_attr($field['regex_error']);  ?>" size="35" /><br />

		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_label_css' ?>"><?php echo __('Label CSS', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[label_css]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_label_css' ?>" type="text" 
				  value="<?php echo esc_attr($field['label_css']);  ?>" size="53" />

		   <br /><label for="<?php echo 'fs_contact_field'. +$key+1 .'_input_css' ?>"><?php echo __('Input CSS', 'si-contact-form'); ?>:</label>
		   <input name="<?php echo $field_opt_name.'[input_css]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_input_css' ?>" type="text" 
				  value="<?php echo esc_attr($field['input_css']);  ?>" size="53" /><br />

           <?php do_action('fs_contact_fields_extra_modifiers', $field_opt_name, $field, $key); ?>

		   <div class="clear"></div>
		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_notes' ?>"><?php _e('HTML before form field:', 'si-contact-form'); ?></label><br />
		   <textarea rows="2" cols="40" name="<?php echo $field_opt_name.'[notes]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_notes' ?>"><?php echo esc_textarea($field['notes']);  ?></textarea><br />

		   <label for="<?php echo 'fs_contact_field'. +$key+1 .'_notes_after' ?>"><?php _e('HTML after form field:', 'si-contact-form'); ?></label><br />
		   <textarea rows="2" cols="40" name="<?php echo $field_opt_name.'[notes_after]' ?>" id="<?php echo 'fs_contact_field'. +$key+1 .'_notes_after' ?>"><?php echo esc_textarea($field['notes_after']);  ?></textarea><br />

       <p class="submit">
		<input id="submit2" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
        <?php if ( '0' == $field['standard'] && empty(self::$new_field_added)  ) { ?>
				<input type="button" class="button-primary" name="<?php echo 'delte field-'.$key; ?>"
				   value="<?php esc_attr_e('Delete Field', 'si-contact-form'); ?>" onclick="fscf_delete_field('<?php echo $key+1; ?>')" />
		<?php }
        echo '</p>';

			if ( FSCF_EMAIL_FIELD == $field['standard'] ) {
				// Add extra field to the email field
				?>
				<br /><input name="<?php echo self::$form_option_name;?>[double_email]" id="fs_contact_double_email" type="checkbox" <?php if ( self::$form_options['double_email'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
				<label for="fs_contact_double_email"><?php _e( 'Enable double email entry required on the form.', 'si-contact-form' ); ?></label>
				<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_double_email_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
				<div class="fscf_tip" id="si_contact_double_email_tip">
				<?php _e( 'Requires users to enter email address in two fields to help reduce mistakes. Note: "Required field" will also be set.', 'si-contact-form' ) ?>
				</div>
				<br />
				<?php
			}		   
		   ?>
			</div>
			</fieldset>
			<?php
		} // end foreach 
		
		// Settings affecting all fields
		?>
        <div>
        <?php if( empty(self::$new_field_added) ) { ?>
		<div class="fscf_right" style="padding:7px;"><input type="button" class="button-primary" name="new_field" value="<?php esc_attr_e('Add New Field', 'si-contact-form'); ?>" onclick="fscf_add_field('<?php esc_attr_e('Add Field', 'si-contact-form'); ?>');" /></div>
        <?php  } ?>
        <p class="submit">
		<input id="submit2" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
        </div>

		<p><strong><?php _e('General Field Settings', 'si-contact-form'); ?></strong></p>
		<label for="fs_contact_date_format"><?php _e( 'Date field - Date format:', 'si-contact-form' ); ?></label>
		<select id="fs_contact_date_format" name="<?php echo self::$form_option_name; ?>[date_format]">
			<?php
			$selected = '';
			$cal_date_array = array(
				'mm/dd/yyyy' => __( 'mm/dd/yyyy', 'si-contact-form' ),
				'dd/mm/yyyy' => __( 'dd/mm/yyyy', 'si-contact-form' ),
				'mm-dd-yyyy' => __( 'mm-dd-yyyy', 'si-contact-form' ),
				'dd-mm-yyyy' => __( 'dd-mm-yyyy', 'si-contact-form' ),
				'mm.dd.yyyy' => __( 'mm.dd.yyyy', 'si-contact-form' ),
				'dd.mm.yyyy' => __( 'dd.mm.yyyy', 'si-contact-form' ),
				'yyyy/mm/dd' => __( 'yyyy/mm/dd', 'si-contact-form' ),
				'yyyy-mm-dd' => __( 'yyyy-mm-dd', 'si-contact-form' ),
				'yyyy.mm.dd' => __( 'yyyy.mm.dd', 'si-contact-form' ),
			);
			foreach ( $cal_date_array as $k => $v ) {
				if ( self::$form_options['date_format'] == "$k" )
					$selected = ' selected="selected"';
				echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>' . "\n";
				$selected = '';
			}
			?>
		</select>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_date_format_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_date_format_tip">
		<?php _e( 'Use to set the date format for the date field.', 'si-contact-form' ); ?>
		</div>
		<br />

		<label for="fs_contact_cal_start_day"><?php _e( 'Date field - Calendar Start Day of the Week', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[cal_start_day]" id="fs_contact_cal_start_day" type="text" value="<?php echo absint( self::$form_options['cal_start_day'] ); ?>" size="3" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_cal_start_day_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_cal_start_day_tip">
		<?php _e( 'Use to set the day the week the date field calendar will start on: 0(Sun) to 6(Sat).', 'si-contact-form' ); ?>
		</div>
		<br />

		<label for="fs_contact_time_format"><?php _e( 'Time field - Time format:', 'si-contact-form' ); ?></label>
		<select id="fs_contact_time_format" name="<?php echo self::$form_option_name; ?>[time_format]">
			<?php
			$selected = '';
			$time_format_array = array(
				'12' => __( '12 Hour', 'si-contact-form' ),
				'24' => __( '24 Hour', 'si-contact-form' ),
			);
			foreach ( $time_format_array as $k => $v ) {
				if ( self::$form_options['time_format'] == "$k" )
					$selected = ' selected="selected"';
				echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>' . "\n";
				$selected = '';
			}
			?>
		</select>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_time_format_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_time_format_tip">
		<?php _e( 'Use to set the time format for the time field.', 'si-contact-form' ); ?>
		</div>
		<br />


		<label for="fs_contact_attach_types"><?php _e( 'Attached files acceptable types', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[attach_types]" id="fs_contact_attach_types" type="text" value="<?php echo esc_attr( self::$form_options['attach_types'] ); ?>" size="60" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_attach_types_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_attach_types_tip">
		<?php _e( 'Set the acceptable file types for the file attachment feature. Any file type not on this list will be rejected.', 'si-contact-form' ); ?>
		<?php _e( 'Separate each file type with a comma character. example:', 'si-contact-form' ); ?>
		doc,docx,pdf,txt,gif,jpg,jpeg,png
		</div>
		<br />

		<label for="fs_contact_attach_size"><?php _e( 'Attached files maximum size allowed', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[attach_size]" id="fs_contact_attach_size" type="text" value="<?php echo esc_attr( self::$form_options['attach_size'] ); ?>" size="30" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_attach_size_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_attach_size_tip">
			<?php _e( 'Set the acceptable maximum file size for the file attachment feature.', 'si-contact-form' ); ?><br />
			<?php
			_e( 'example: 1mb equals one Megabyte, 1kb equals one Kilobyte', 'si-contact-form' );
			$max_upload = (int) (ini_get( 'upload_max_filesize' ));
			$max_post = (int) (ini_get( 'post_max_size' ));
			$memory_limit = (int) (ini_get( 'memory_limit' ));
			$upload_mb = min( $max_upload, $max_post, $memory_limit );
			?><br />
		<?php _e( 'Note: Maximum size is limited to available server resources and various PHP settings. Very few servers will accept more than 2mb. Sizes under 1mb will usually have best results. examples:', 'si-contact-form' ); ?>
		500kb, 800kb, 1mb, 1.5mb, 2mb
		<?php _e( 'Note: If you set the value higher than your server can handle, users will have problems uploading big files. The form can time out and may not even show an error.', 'si-contact-form' ); ?>
			<b><?php _e( 'Your server will not allow uploading files larger than than:', 'si-contact-form' );
		echo " $upload_mb"; ?>mb</b>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name; ?>[textarea_html_allow]" id="fs_contact_textarea_html_allow" type="checkbox" <?php if ( self::$form_options['textarea_html_allow'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_textarea_html_allow"><?php _e( 'Enable users to send HTML code in the textarea extra field types.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_textarea_html_allow_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_textarea_html_allow_tip">
		<?php _e( 'This setting is disabled by default for better security. Enable only if you want users to be able to send HTML code in the textarea extra field types. HTML code allowed will be filtered and limited to a few safe tags only.', 'si-contact-form' ); ?>
		</div>
		<br />

	   <input name="<?php echo self::$form_option_name; ?>[preserve_space_enable]" id="fs_contact_preserve_space_enable" type="checkbox" <?php if ( self::$form_options['preserve_space_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
	   <label for="fs_contact_preserve_space_enable"><?php _e( 'Preserve Message field spaces.', 'si-contact-form' ); ?></label>
	   <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_preserve_space_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
	   <div class="fscf_tip" id="si_contact_preserve_space_enable_tip">
	   <?php _e( 'Normally the textarea fields will have all extra white space removed. Enabling this setting will allow all the textarea field white space to be preserved.', 'si-contact-form' ); ?>
	   </div>
	   <br />		
		
		</fieldset>

		<?php
	}

	static function style_settings_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>

        <strong><?php _e('Modifiable CSS Style Feature:', 'si-contact-form'); ?></strong>
        <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_css_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        <div class="fscf_tip" id="si_contact_css_tip">
        <?php _e('Use to adjust the alignments or add colors to the form elements.', 'si-contact-form'); ?><br />
        <?php _e('You can use inline css, or add a class property to be used by your own stylesheet.', 'si-contact-form'); ?><br />
         <?php _e('You can copy styles from one form to another on the Tools settings tab.', 'si-contact-form'); ?><br />
        <?php _e('Acceptable examples:', 'si-contact-form'); ?><br />
        text-align:left; color:#000000; background-color:#CCCCCC;<br />
        style="text-align:left; color:#000000; background-color:#CCCCCC;"<br />
        class="input"
        </div><br />


		<br />

        <input name="<?php echo self::$form_option_name;?>[border_enable]" id="si_contact_border_enable" type="checkbox" <?php if ( self::$form_options['border_enable'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
        <label for="<?php echo self::$form_option_name;?>[border_enable]"><?php _e('Enable a fieldset box around the form', 'si-contact-form') ?>.</label>
        <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_border_enable_tip');"><?php _e('help', 'si-contact-form'); ?></a>
		<div class="fscf_tip" id="si_contact_border_enable_tip">
		<?php _e('Enable to draw a fieldset box around all the form elements. You can change the fieldset box label and style in the settings below.', 'si-contact-form'); ?>
		</div>

        <br />
        <label for="<?php echo self::$form_option_name;?>[title_border]"><?php _e('Fieldset box label', 'si-contact-form'); ?>:</label><input name="<?php echo self::$form_option_name;?>[title_border]" id="si_contact_title_border" type="text" value="<?php echo esc_attr(self::$form_options['title_border']);  ?>" size="50" />
        <br />

        <br />

    <label for="<?php echo self::$form_option_name;?>[external_style]"><?php echo __('Select the method of delivering the form style:', 'si-contact-form'); ?></label>
      <select id="fscf_external_style" name="<?php echo self::$form_option_name;?>[external_style]">
<?php
$style_opt_array = array(
'false' => __('Internal Style Sheet CSS (default, edit below)', 'si-contact-form'),
'true' =>  __('External Style Sheet CSS (requires editing theme style.css)', 'si-contact-form'),
);
$selected = '';
foreach ($style_opt_array as $k => $v) {
 if (self::$form_options['external_style'] == "$k")  $selected = ' selected="selected"';
 echo '<option value="'.esc_attr($k).'"'.$selected.'>'.esc_html($v).'</option>'."\n";
 $selected = '';
}
?>
</select>



<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_external_style_tip');"><?php
if (self::$form_options['external_style'] == 'true') {
       echo '<br /><p>';  _e('View custom CSS', 'si-contact-form'); echo '</p>';
} else {
    _e('help', 'si-contact-form');
}
 ?></a>
<div class="fscf_tip" id="si_contact_external_style_tip"><br />
<?php _e('By default, the FSCF form styles are editable below when using "Internal Style Sheet CSS". The style is included inline in the form HTML.', 'si-contact-form');  echo '<br /><br />'; ?>
<?php _e('CSS experts will like the flexibility of using their own custom style sheet by enabling "External Style Sheet CSS",', 'si-contact-form'); echo ' '; ?>
<?php _e('then the FSCF CSS will NOT be included inline in the form HTML, and the custom CSS below must be included in the style.css of the theme. Be sure to remember this if you switch your theme later on.', 'si-contact-form'); ?>
<br /><br />
<?php _e('Premium themes may have added support for Fast Secure Contact Form style in the theme\'s style.css. Select "External Style Sheet CSS" when instructed by the theme\'s installation instructions.', 'si-contact-form'); echo ' '; ?>
<br /><br />
<?php _e('Note: if you use the setting "reset the alignment styles to labels on left(or top)", or "Reset the styles of labels, field inputs, buttons, and text", then the custom CSS below will reflect the changes. You would have to edit your custom CSS again to see the changes on the form.', 'si-contact-form'); echo ' '; ?>

<br />
<?php if (self::$form_options['external_style'] == 'true') { ?>
<br />

<strong><?php _e('External Style Sheet CSS for experts and theme builders:', 'si-contact-form'); ?></strong><br />
<pre>
/*------------------------------------------------*/
/*-----------[Fast Secure Contact Form]-----------*/
/*------------------------------------------------*/

/* Alignment DIVs */
.fscf-div-form              { <?php echo self::$form_options['form_style']; ?> }
.fscf-div-left-box          { <?php echo self::$form_options['left_box_style']; ?> }
.fscf-div-right-box         { <?php echo self::$form_options['right_box_style']; ?> }
.fscf-div-clear             { <?php echo self::$form_options['clear_style']; ?> }
.fscf-div-field-left        { <?php echo self::$form_options['field_left_style']; ?> }
.fscf-div-field-prefollow   { <?php echo self::$form_options['field_prefollow_style']; ?> }
.fscf-div-field-follow      { <?php echo self::$form_options['field_follow_style']; ?> }
.fscf-div-label             { <?php echo self::$form_options['title_style']; ?> }
.fscf-div-field             { <?php echo self::$form_options['field_div_style']; ?> }
.fscf-div-captcha-sm        { <?php echo self::$form_options['captcha_div_style_sm']; ?> }
.fscf-div-captcha-m         { <?php echo self::$form_options['captcha_div_style_m']; ?> }
.fscf-image-captcha         { <?php echo self::$form_options['captcha_image_style']; ?> }
.fscf-image-captcha-refresh { <?php echo self::$form_options['captcha_reload_image_style']; ?> }
.fscf-div-submit            { <?php echo self::$form_options['submit_div_style']; ?> }
.fscf-fieldset              { <?php echo self::$form_options['border_style']; ?> }

/* Styles of labels, fields and text */
.fscf-required-indicator { <?php echo self::$form_options['required_style']; ?> }
.fscf-required-text      { <?php echo self::$form_options['required_text_style']; ?> }
.fscf-hint-text          { <?php echo self::$form_options['hint_style']; ?> }
.fscf-div-error          { <?php echo self::$form_options['error_style']; ?> }
.fscf-div-redirecting    { <?php echo self::$form_options['redirect_style']; ?> }
.fscf-fieldset-field     { <?php echo self::$form_options['fieldset_style']; ?> }
.fscf-label              { <?php echo self::$form_options['label_style']; ?> }
.fscf-option-label       { <?php echo self::$form_options['option_label_style']; ?> }
.fscf-input-text         { <?php echo self::$form_options['field_style']; ?> }
.fscf-input-captcha      { <?php echo self::$form_options['captcha_input_style']; ?> }
.fscf-input-textarea     { <?php echo self::$form_options['textarea_style']; ?> }
.fscf-input-select       { <?php echo self::$form_options['select_style']; ?> }
.fscf-input-checkbox     { <?php echo self::$form_options['checkbox_style']; ?> }
.fscf-input-radio        { <?php echo self::$form_options['radio_style']; ?> }
.fscf-button-submit      { <?php echo self::$form_options['button_style']; ?> }
.fscf-button-reset       { <?php echo self::$form_options['reset_style']; ?> }
.fscf-button-vcita       { <?php echo self::$form_options['vcita_button_style']; ?> }
.fscf-button-div-vcita   { <?php echo self::$form_options['vcita_div_button_style']; ?> }
.fscf-powered-by         { <?php echo self::$form_options['powered_by_style']; ?> }

/* Placeholder Style - WebKit browsers - Safari, Chrome */
::-webkit-input-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }

/* Placeholder Style - Mozilla Firefox 4 - 18 */
:-moz-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }

/* Placeholder Style - Mozilla Firefox 19+ */
::-moz-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }

/* Placeholder Style - Internet Explorer 10+ */
:-ms-input-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }
</pre>
<?php } ?>
</div>

<?php
$readonly = '';
if( self::$form_options['external_style'] == 'true' ) {
  $readonly = 'readonly="readonly"';

  echo '<div class="updated">';
  echo __('Caution: "External Style Sheet CSS" is enabled in the Styles tab. This setting requires your theme\'s style.css to include the custom CSS. You can get the custom CSS on the Styles tab. Be sure your theme includes the custom CSS from this plugin, if it does not, then add the custom CSS to your theme or change the setting back to "Internal Style Sheet CSS".', 'si-contact-form');
  echo "</div><br />\n";

  echo '<div class="fsc-notice">';
  echo __('Note: "Internal Style Sheet CSS" fields below are not editable while "External Style Sheet CSS" is enabled.', 'si-contact-form');
  echo "</div>\n";
}

?>

<p><strong><?php _e('Alignment DIVs:', 'si-contact-form'); ?></strong>
<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_align_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        <div class="fscf_tip" id="si_contact_align_tip">
        <?php _e('Use to adjust the alignments of the form elements.', 'si-contact-form'); ?><br />
        <?php _e('You can also check "reset the alignment" to return to defaults and make the labels on top or to the left.', 'si-contact-form'); ?><br />
        <?php _e('You can use inline css, or add a class property to be used by your own stylesheet.', 'si-contact-form'); ?><br />
        <?php _e('Acceptable examples:', 'si-contact-form'); ?><br />
        text-align:left; color:#000000; background-color:#CCCCCC;<br />
        style="text-align:left; color:#000000; background-color:#CCCCCC;"<br />
        class="input"
        </div>
</p>

        <input name="fscf_reset_styles_top" id="si_contact_reset_styles_top" type="checkbox" value="true" />
        <label for="fscf_reset_styles_top"><?php _e('Reset the alignment styles to labels on top', 'si-contact-form') ?> <?php _e('(default, recommended)', 'si-contact-form') ?></label>
        <br />

        <input name="fscf_reset_styles_left" id="si_contact_reset_styles_left" type="checkbox" value="true" />
        <label for="fscf_reset_styles_left"><?php _e('Reset the alignment styles to labels on left', 'si-contact-form') ?> <?php _e('(sometimes less compatible)', 'si-contact-form') ?></label>
        <br />
        <br />

        <label for="<?php echo self::$form_option_name;?>[form_style]"><?php _e('Form DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[form_style]" id="si_contact_form_style" type="text" value="<?php echo esc_attr(self::$form_options['form_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_form_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_form_style_tip">
        <?php _e('Use to adjust the style in the form wrapping DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[left_box_style]"><?php _e('Left Box DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[left_box_style]" id="si_contact_left_box_style" type="text" value="<?php echo esc_attr(self::$form_options['left_box_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_left_box_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_left_box_style_tip">
        <?php _e('Use to adjust the style in the left box vCita container DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[right_box_style]"><?php _e('Right Box DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[right_box_style]" id="si_contact_right_box_style" type="text" value="<?php echo esc_attr(self::$form_options['right_box_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_right_box_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_right_box_style_tip">
        <?php _e('Use to adjust the style in the right box vCita container DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[clear_style]"><?php _e('Clear DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[clear_style]" id="si_contact_clear_style" type="text" value="<?php echo esc_attr(self::$form_options['clear_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_clear_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_clear_style_tip">
        <?php _e('Use to adjust the style in the form clear DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[field_left_style]"><?php _e('Field Left DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[field_left_style]" id="si_contact_field_left_style" type="text" value="<?php echo esc_attr(self::$form_options['field_left_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_field_left_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_field_left_style_tip">
        <?php _e('Use to adjust the style in the form Field Left DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[field_follow_style]"><?php _e('Field Follow DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[field_follow_style]" id="si_contact_field_follow_style" type="text" value="<?php echo esc_attr(self::$form_options['field_follow_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_field_follow_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_field_follow_style_tip">
        <?php _e('Use to adjust the style in the form Field Follow DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[field_prefollow_style]"><?php _e('Field Pre-Follow DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[field_prefollow_style]" id="si_contact_field_prefollow_style" type="text" value="<?php echo esc_attr(self::$form_options['field_prefollow_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_field_prefollow_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_field_prefollow_style_tip">
        <?php _e('Use to adjust the style in the form Field Pre-Follow DIV. This is a narrower DIV that is to the left of a follow field.', 'si-contact-form'); ?>
        </div>
        <br />
	
        <label for="<?php echo self::$form_option_name;?>[title_style]"><?php _e('Input labels alignment DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[title_style]" id="si_contact_title_style" type="text" value="<?php echo esc_attr(self::$form_options['title_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_title_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_title_style_tip">
        <?php _e('Use to adjust the style in the form input label alignment wrapping DIVs.', 'si-contact-form'); ?>
        </div>
        <br />
		
        <label for="<?php echo self::$form_option_name;?>[field_div_style]"><?php _e('Input fields alignment DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[field_div_style]" id="si_contact_field_div_style" type="text" value="<?php echo esc_attr(self::$form_options['field_div_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_div_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_div_style_tip">
        <?php _e('Use to adjust the style in the form input field alignment wrapping DIVs.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[captcha_div_style_sm]"><?php _e('Small CAPTCHA DIV', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[captcha_div_style_sm]" id="si_contact_captcha_div_style_sm" type="text" value="<?php echo esc_attr(self::$form_options['captcha_div_style_sm']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_captcha_div_style_sm_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_captcha_div_style_sm_tip">
        <?php _e('Use to adjust the style in the form Small CAPTCHA alignment wrapping DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[captcha_div_style_m]"><?php _e('Large CAPTCHA DIV', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[captcha_div_style_m]" id="si_contact_captcha_div_style_m" type="text" value="<?php echo esc_attr(self::$form_options['captcha_div_style_m']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_div_style_m_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_div_style_m_tip">
        <?php _e('Use to adjust the style in the form CAPTCHA alignment wrapping DIV.', 'si-contact-form'); ?>
        </div>
        <br />
		
        <label for="<?php echo self::$form_option_name;?>[captcha_image_style]"><?php _e('CAPTCHA image alignment', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[captcha_image_style]" id="si_contact_captcha_image_style" type="text" value="<?php echo esc_attr(self::$form_options['captcha_image_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_captcha_image_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_captcha_image_style_tip">
        <?php _e('Use to adjust the style for the CAPTCHA image alignment.', 'si-contact-form'); ?>
        </div>
        <br />	

        <label for="<?php echo self::$form_option_name;?>[captcha_reload_image_style]"><?php _e('CAPTCHA reload image alignment', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[captcha_reload_image_style]" id="si_contact_captcha_reload_image_style" type="text" value="<?php echo esc_attr(self::$form_options['captcha_reload_image_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_captcha_reload_image_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_captcha_reload_image_style_tip">
        <?php _e('Use to adjust the style for the CAPTCHA reload image alignment.', 'si-contact-form'); ?>
        </div>
        <br />			

        <label for="<?php echo self::$form_option_name;?>[submit_div_style]"><?php _e('Submit DIV', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[submit_div_style]" id="si_contact_submit_div_style" type="text" value="<?php echo esc_attr(self::$form_options['submit_div_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_submit_div_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_submit_div_style_tip">
        <?php _e('Use to adjust the style in the form submit button alignment wrapping DIV.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[border_style]"><?php _e('Form Fieldset Box', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[border_style]" id="si_contact_border_style" type="text" value="<?php echo esc_attr(self::$form_options['border_style']);  ?>"  />
		<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_border_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
		<div class="fscf_tip" id="si_contact_border_style_tip">
        <?php _e('Use to adjust the style of the fieldset box on form (if fieldset is enabled).', 'si-contact-form'); ?>
        </div>
        <br />

        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
<p><strong><?php _e('Style of labels, field inputs, buttons, and text:', 'si-contact-form'); ?></strong>
<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_styles_style_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        <div class="fscf_tip" id="si_contact_styles_style_tip">
        <?php _e('Use to adjust the style of the form labels, field inputs, buttons, and text.', 'si-contact-form'); ?><br />
        <?php _e('You can also check "reset the styles" to return to defaults.', 'si-contact-form'); ?><br />
        <?php _e('You can use inline css, or add a class property to be used by your own stylesheet.', 'si-contact-form'); ?><br />
        <?php _e('Acceptable examples:', 'si-contact-form'); ?><br />
        text-align:left; color:#000000; background-color:#CCCCCC;<br />
        style="text-align:left; color:#000000; background-color:#CCCCCC;"<br />
        class="input"
        </div>
</p>

        <input name="fscf_reset_styles_labels" id="si_contact_reset_styles_labels" type="checkbox" value="true" />
        <label for="fscf_reset_styles_labels"><?php _e('Reset the styles of labels, field inputs, buttons, and text', 'si-contact-form') ?></label>
        <br />
        <br />

        <label for="<?php echo self::$form_option_name;?>[required_style]"><?php _e('Required field indicator', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[required_style]" id="si_contact_required_style" type="text" value="<?php echo esc_attr(self::$form_options['required_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_required_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_required_style_tip">
        <?php _e('Use to adjust the style of the required field indicator.', 'si-contact-form'); ?>
        </div>
        <br />
		
        <label for="<?php echo self::$form_option_name;?>[required_text_style]"><?php _e('Required field text', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[required_text_style]" id="si_contact_required_text_style" type="text" value="<?php echo esc_attr(self::$form_options['required_text_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_required_text_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_required_text_style_tip">
        <?php _e('Use to adjust the style of the message that says a field is required.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[hint_style]"><?php _e('Hint messages', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[hint_style]" id="si_contact_hint_style" type="text" value="<?php echo esc_attr(self::$form_options['hint_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_hint_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_hint_style_tip">
        <?php _e('Use to adjust the style of small text hints like "enter your email again."', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[error_style]"><?php _e('Input validation messages', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[error_style]" id="si_contact_error_style" type="text" value="<?php echo esc_attr(self::$form_options['error_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_error_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_error_style_tip">
        <?php _e('Use to adjust the style of form input validation messages.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[redirect_style]"><?php _e('Redirecting message', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[redirect_style]" id="si_contact_redirect_style" type="text" value="<?php echo esc_attr(self::$form_options['redirect_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_redirect_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_redirect_style_tip">
        <?php _e('Use to adjust the style for the "redirecting" message shown after the form is sent.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[fieldset_style]"><?php _e('Field Fieldset Box', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[fieldset_style]" id="si_contact_fieldset_style" type="text" value="<?php echo esc_attr(self::$form_options['fieldset_style']);  ?>"  />
		<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_fieldset_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
		<div class="fscf_tip" id="si_contact_fieldset_style_tip">
        <?php _e('Use to adjust the style of the fieldset box on fields of fieldset field type.', 'si-contact-form'); ?>
        </div>
        <br />

			
        <label for="<?php echo self::$form_option_name;?>[label_style]"><?php _e('Field labels', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[label_style]" id="si_contact_label_style" type="text" value="<?php echo esc_attr(self::$form_options['label_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_label_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_label_style_tip">
        <?php _e('Use to adjust the style for the field labels.', 'si-contact-form'); ?>
        </div>
        <br />	

        <label for="<?php echo self::$form_option_name;?>[option_label_style]"><?php _e('Options labels', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[option_label_style]" id="si_contact_option_label_style" type="text" value="<?php echo esc_attr(self::$form_options['option_label_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_option_label_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_option_label_style_tip">
        <?php _e('Use to adjust the style for the checkbox and radio option labels.', 'si-contact-form'); ?>
        </div>
        <br />	
		
        <label for="<?php echo self::$form_option_name;?>[field_style]"><?php _e('Input text fields', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[field_style]" id="si_contact_field_style" type="text" value="<?php echo esc_attr(self::$form_options['field_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_field_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_field_style_tip">
        <?php _e('Use to adjust the style inside the form input text field types.', 'si-contact-form'); ?>
        </div>
        <br />
		
        <label for="<?php echo self::$form_option_name;?>[captcha_input_style]"><?php _e('Input text field CAPTCHA', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[captcha_input_style]" id="si_contact_captcha_input_style" type="text" value="<?php echo esc_attr(self::$form_options['captcha_input_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_captcha_input_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_captcha_input_style_tip">
        <?php _e('Use to adjust the style in the CAPTCHA code input text field.', 'si-contact-form'); ?>
        </div>
        <br />		
		
        <label for="<?php echo self::$form_option_name;?>[textarea_style]"><?php _e('Input textarea fields', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[textarea_style]" id="si_contact_textarea_style" type="text" value="<?php echo esc_attr(self::$form_options['textarea_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_textarea_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_textarea_style_tip">
        <?php _e('Use to adjust the style inside the form input textarea field types.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[select_style]"><?php _e('Input select fields', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[select_style]" id="si_contact_select_style" type="text" value="<?php echo esc_attr(self::$form_options['select_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_select_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_select_style_tip">
        <?php _e('Use to adjust the style inside the form select field types including subject, if enabled.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[checkbox_style]"><?php _e('Input checkbox fields', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[checkbox_style]" id="si_contact_checkbox_style" type="text" value="<?php echo esc_attr(self::$form_options['checkbox_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_checkbox_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_checkbox_style_tip">
        <?php _e('Use to adjust the style inside the form input checkbox field types.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[radio_style]"><?php _e('Input radio fields', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[radio_style]" id="si_contact_radio_style" type="text" value="<?php echo esc_attr(self::$form_options['radio_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_radio_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_radio_style_tip">
        <?php _e('Use to adjust the style inside the form input radio field types.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[placeholder_style]"><?php _e('Placeholder text', 'si-contact-form'); ?>:</label>
		<span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[placeholder_style]" id="si_contact_placeholder_style" type="text" value="<?php echo esc_attr(self::$form_options['placeholder_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_placeholder_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_placeholder_style_tip">
        <?php _e('Use to adjust the style of the placeholder text.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[button_style]"><?php _e('Submit button', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[button_style]" id="si_contact_button_style" type="text" value="<?php echo esc_attr(self::$form_options['button_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_button_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_button_style_tip">
        <?php _e('Use to adjust the style for the form submit button text.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[reset_style]"><?php _e('Reset button', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[reset_style]" id="si_contact_reset_style" type="text" value="<?php echo esc_attr(self::$form_options['reset_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_reset_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_reset_style_tip">
        <?php _e('Use to adjust the style for the form reset button text, if enabled.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[vcita_button_style]"><?php _e('vCita appointment button', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[vcita_button_style]" id="si_contact_vcita_button_style" type="text" value="<?php echo esc_attr(self::$form_options['vcita_button_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_vcita_button_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_vcita_button_style_tip">
        <?php _e('Use to adjust the style for the vcita schedule appointment button.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[vcita_div_button_style]"><?php _e('vCita appointment button DIV box', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[vcita_div_button_style]" id="si_contact_vcita_div_button_style" type="text" value="<?php echo esc_attr(self::$form_options['vcita_div_button_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_vcita_div_button_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_vcita_div_button_style_tip">
        <?php _e('Use to adjust the style for the vcita schedule appointment button DIV box.', 'si-contact-form'); ?>
        </div>
        <br />

        <label for="<?php echo self::$form_option_name;?>[powered_by_style]"><?php _e('"Powered by" message', 'si-contact-form'); ?>:</label>
        <span class="fscf_style_inline"><input class="fscf_style_text" <?php echo $readonly ?> name="<?php echo self::$form_option_name;?>[powered_by_style]" id="si_contact_powered_by_style" type="text" value="<?php echo esc_attr(self::$form_options['powered_by_style']);  ?>" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_powered_by_style_tip');"><?php _e('help', 'si-contact-form'); ?></a></span>
        <div class="fscf_tip" id="si_contact_powered_by_style_tip">
        <?php _e('Use to adjust the style for the "powered by" message link.', 'si-contact-form'); ?>
        </div>
        <br/>

       <input name="<?php echo self::$form_option_name;?>[aria_required]" id="si_contact_aria_required" type="checkbox" <?php if( self::$form_options['aria_required'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
       <label for="<?php echo self::$form_option_name;?>[aria_required]"><?php _e('Enable aria-required tags for screen readers', 'si-contact-form'); ?>.</label>
       <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_aria_required_tip');"><?php _e('help', 'si-contact-form'); ?></a>
       <div class="fscf_tip" id="si_contact_aria_required_tip">
       <?php _e('aria-required is a form input WAI ARIA tag. Screen readers use it to determine which fields are required. Enabling this is good for accessability, but will cause the HTML to fail the W3C Validation (there is no attribute "aria-required"). WAI ARIA attributes are soon to be accepted by the HTML validator, so you can safely ignore the validation error it will cause.', 'si-contact-form'); ?>
       </div>

</fieldset>
		<?php		
	}	// end style_settings_callback()

	static function label_settings_callback() {
//		echo "This is the field labels settings section";
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>

<strong><?php _e('Change field labels:', 'si-contact-form'); ?></strong>
        <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_text_fields_tip');"><?php _e('help', 'si-contact-form'); ?></a>
       <div class="fscf_tip" id="si_contact_text_fields_tip">
       <?php _e('Some people like to change the labels for the form. These fields can be filled in to override the standard labels.', 'si-contact-form'); ?>
       </div>
<br />

        <input name="<?php echo self::$form_option_name;?>[req_field_label_enable]" id="si_contact_req_field_label_enable" type="checkbox" <?php if ( self::$form_options['req_field_label_enable'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
        <label for="<?php echo self::$form_option_name;?>[req_field_label_enable]"><?php _e('Enable required field label on contact form:', 'si-contact-form') ?></label> <?php echo esc_html( (self::$form_options['tooltip_required'] != '') ? self::$form_options['req_field_indicator'] .self::$form_options['tooltip_required'] : self::$form_options['req_field_indicator'] . __('indicates required field', 'si-contact-form') ); ?><br />

        <label for="<?php echo self::$form_option_name;?>[tooltip_required]"><?php _e('indicates required field', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[tooltip_required]" id="si_contact_tooltip_required" type="text" value="<?php echo esc_attr(self::$form_options['tooltip_required']);  ?>" size="50" /><br />

        <input name="<?php echo self::$form_option_name;?>[req_field_indicator_enable]" id="si_contact_req_field_indicator_enable" type="checkbox" <?php if ( self::$form_options['req_field_indicator_enable'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
        <label for="<?php echo self::$form_option_name;?>[req_field_indicator_enable]"><?php _e('Enable required field indicators on contact form', 'si-contact-form') ?>.</label><br />

        <label for="<?php echo self::$form_option_name;?>[req_field_indicator]"><?php _e('Required field indicator:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[req_field_indicator]" id="si_contact_req_field_indicator" type="text" value="<?php echo esc_attr(self::$form_options['req_field_indicator']);  ?>" size="50" /><br />

         <label for="<?php echo self::$form_option_name;?>[title_dept]"><?php _e('Select a contact:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_dept]" id="si_contact_title_dept" type="text" value="<?php echo esc_attr(self::$form_options['title_dept']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_select]"><?php _e('Select', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_select]" id="si_contact_title_select" type="text" value="<?php echo esc_attr(self::$form_options['title_select']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_name]"><?php _e('Name:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_name]" id="si_contact_title_name" type="text" value="<?php echo esc_attr(self::$form_options['title_name']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_fname]"><?php _e('First Name:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_fname]" id="si_contact_title_fname" type="text" value="<?php echo esc_attr(self::$form_options['title_fname']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_lname]"><?php _e('Last Name:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_lname]" id="si_contact_title_lname" type="text" value="<?php echo esc_attr(self::$form_options['title_lname']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_mname]"><?php _e('Middle Name:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_mname]" id="si_contact_title_mname" type="text" value="<?php echo esc_attr(self::$form_options['title_mname']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_miname]"><?php _e('Middle Initial:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_miname]" id="si_contact_title_miname" type="text" value="<?php echo esc_attr(self::$form_options['title_miname']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_email]"><?php _e('Email:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_email]" id="si_contact_title_email" type="text" value="<?php echo esc_attr(self::$form_options['title_email']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_email2]"><?php _e('Re-enter Email:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_email2]" id="si_contact_title_email2" type="text" value="<?php echo esc_attr(self::$form_options['title_email2']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_subj]"><?php _e('Subject:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_subj]" id="si_contact_title_subj" type="text" value="<?php echo esc_attr(self::$form_options['title_subj']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_mess]"><?php _e('Message:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_mess]" id="si_contact_title_mess" type="text" value="<?php echo esc_attr(self::$form_options['title_mess']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_capt]"><?php _e('CAPTCHA Code:', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_capt]" id="si_contact_title_capt" type="text" value="<?php echo esc_attr(self::$form_options['title_capt']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_submit]"><?php _e('Submit', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_submit]" id="si_contact_title_submit" type="text" value="<?php echo esc_attr(self::$form_options['title_submit']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_submitting]"><?php _e('Submitting...', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_submitting]" id="si_contact_title_submitting" type="text" value="<?php echo esc_attr(self::$form_options['title_submitting']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_reset]"><?php _e('Reset', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_reset]" id="si_contact_title_reset" type="text" value="<?php echo esc_attr(self::$form_options['title_reset']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[title_areyousure]"><?php _e('Are you sure?', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[title_areyousure]" id="si_contact_title_areyousure" type="text" value="<?php echo esc_attr(self::$form_options['title_areyousure']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[text_message_sent]"><?php _e('Your message has been sent, thank you.', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[text_message_sent]" id="si_contact_text_message_sent" type="text" value="<?php echo esc_attr(self::$form_options['text_message_sent']);  ?>" size="50" /><br />
         <label for="<?php echo self::$form_option_name;?>[text_print_button]"><?php _e('View / Print your message', 'si-contact-form'); ?></label><input name="<?php echo self::$form_option_name;?>[text_print_button]" id="si_contact_text_print_button" type="text" value="<?php echo esc_attr(self::$form_options['text_print_button']);  ?>" size="50" /><br />
			
		</fieldset>
		<?php
	}
		
	static function tooltip_settings_callback() {
		// Tooltip Label Settings
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
		<p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
		<strong><?php _e('Change tooltips labels:', 'si-contact-form'); ?></strong>
		<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_text_tools_tip');"><?php _e('help', 'si-contact-form'); ?></a>
		<div class="fscf_tip" id="si_contact_text_tools_tip">
		<?php _e('Some people like to change the labels for the form. These fields can be filled in to override the standard labels.', 'si-contact-form'); ?>
		</div><br />

		<label for="<?php echo self::$form_option_name;?>[tooltip_captcha]"><?php _e('CAPTCHA', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[tooltip_captcha]" id="si_contact_tooltip_captcha" type="text" value="<?php echo esc_attr(self::$form_options['tooltip_captcha']);  ?>" size="50" /><br />
		<label for="<?php echo self::$form_option_name;?>[tooltip_refresh]"><?php _e('Refresh', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[tooltip_refresh]" id="si_contact_tooltip_refresh" type="text" value="<?php echo esc_attr(self::$form_options['tooltip_refresh']);  ?>" size="50" /><br />
		<label for="<?php echo self::$form_option_name;?>[tooltip_filetypes]"><?php _e('Acceptable file types: %s.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[tooltip_filetypes]" id="si_contact_tooltip_filetypes" type="text" value="<?php echo esc_attr(self::$form_options['tooltip_filetypes']);  ?>" size="50" /><br />
		<label for="<?php echo self::$form_option_name;?>[tooltip_filesize]"><?php _e('Maximum file size: %s.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[tooltip_filesize]" id="si_contact_tooltip_filesize" type="text" value="<?php echo esc_attr(self::$form_options['tooltip_filesize']);  ?>" size="50" />

		</fieldset>
		<?php
	}
		
	static function error_settings_callback() {
		// Error Message Settings
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
		<strong><?php _e('Change error messages:', 'si-contact-form'); ?></strong>
		<a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_error_fields_tip');"><?php _e('help', 'si-contact-form'); ?></a>
		<div class="fscf_tip" id="si_contact_error_fields_tip">
		<?php _e('Some people like to change the error messages for the form. These fields can be filled in to override the standard error messages.', 'si-contact-form'); ?>
		</div><br />

		<label for="<?php echo self::$form_option_name;?>[error_correct]"><?php _e('Please make corrections below and try again.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_correct]" id="si_contact_error_correct" type="text" value="<?php echo esc_attr(self::$form_options['error_correct']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_contact_select]"><?php _e('Selecting a contact is required.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_contact_select]" id="si_contact_error_contact_select" type="text" value="<?php echo esc_attr(self::$form_options['error_contact_select']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_subject]"><?php _e('Selecting a subject is required.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_subject]" id="si_contact_error_subject" type="text" value="<?php echo esc_attr(self::$form_options['error_subject']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_name]"><?php _e('Your name is required.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_name]" id="si_contact_error_name" type="text" value="<?php echo esc_attr(self::$form_options['error_name']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_field]"><?php _e('This field is required.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_field]" id="si_contact_error_field" type="text" value="<?php echo esc_attr(self::$form_options['error_field']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_select]"><?php _e('At least one item in this field is required.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_select]" id="si_contact_error_select" type="text" value="<?php echo esc_attr(self::$form_options['error_select']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_email]"><?php _e('A proper email address is required.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_email]" id="si_contact_error_email" type="text" value="<?php echo esc_attr(self::$form_options['error_email']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_email_check]"><?php _e('Not a proper email address.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_email_check]" id="si_contact_error_email_check" type="text" value="<?php echo esc_attr(self::$form_options['error_email_check']);  ?>" size="50" /><br />

	    <label for="<?php echo self::$form_option_name;?>[error_email2]"><?php _e('The two email addresses did not match.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_email2]" id="si_contact_error_email2" type="text" value="<?php echo esc_attr(self::$form_options['error_email2']);  ?>" size="50" /><br />

        <label for="<?php echo self::$form_option_name;?>[error_url]"><?php _e('Invalid URL.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_url]" id="si_contact_error_url" type="text" value="<?php echo esc_attr(self::$form_options['error_url']);  ?>" size="50" /><br />

        <label for="<?php echo self::$form_option_name;?>[error_date]"><?php _e('Please select a valid date in this format: %s.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_date]" id="si_contact_error_date" type="text" value="<?php echo esc_attr(self::$form_options['error_date']);  ?>" size="50" /><br />

        <label for="<?php echo self::$form_option_name;?>[error_time]"><?php _e('The time selections are incomplete, select all or none.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_time]" id="si_contact_error_time" type="text" value="<?php echo esc_attr(self::$form_options['error_time']);  ?>" size="50" /><br />


        <label for="<?php echo self::$form_option_name;?>[error_maxlen]"><?php _e('Maximum of %d characters exceeded.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_maxlen]" id="si_contact_error_maxlen" type="text" value="<?php echo esc_attr(self::$form_options['error_maxlen']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_captcha_blank]"><?php _e('Please complete the CAPTCHA.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_captcha_blank]" id="si_contact_error_captcha_blank" type="text" value="<?php echo esc_attr(self::$form_options['error_captcha_blank']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_captcha_wrong]"><?php _e('That CAPTCHA was incorrect.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_captcha_wrong]" id="si_contact_error_captcha_wrong" type="text" value="<?php echo esc_attr(self::$form_options['error_captcha_wrong']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_spambot]"><?php _e('Possible spam bot. Try again.', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_spambot]" id="si_contact_error_spambot" type="text" value="<?php echo esc_attr(self::$form_options['error_spambot']);  ?>" size="50" /><br />

		<label for="<?php echo self::$form_option_name;?>[error_input]"><?php _e('Invalid Input - Spam?', 'si-contact-form'); ?></label>
		<input name="<?php echo self::$form_option_name;?>[error_input]" id="si_contact_error_input" type="text" value="<?php echo esc_attr(self::$form_options['error_input']);  ?>" size="50" /><br />

		</fieldset>
		<?php
	}
	
	static function confirmation_email_callback() {

		self::set_fld_array();
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>

		<input name="<?php echo self::$form_option_name; ?>[auto_respond_enable]" id="si_contact_auto_respond_enable" type="checkbox" <?php if ( self::$form_options['auto_respond_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_enable]"><?php _e( 'Enable confirmation email message.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_enable_tip">
		<?php _e( 'Enable when you want the form to automatically send a confirmation email message to the user who submitted the form.', 'si-contact-form' ); ?>
		</div>
		<br />
			
		<?php
		if ( self::$form_options['auto_respond_enable'] == 'true' && (self::$form_options['auto_respond_from_name'] == '' || self::$form_options['auto_respond_from_email'] == '' || self::$form_options['auto_respond_reply_to'] == '' || self::$form_options['auto_respond_subject'] == '' || self::$form_options['auto_respond_message'] == '') ) {
			echo '<div class="fsc-notice">';
			echo __( 'Warning: Enabling this setting requires all the confirmation email fields below to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		if ( ! self::$autoresp_ok && self::$form_options['auto_respond_enable'] == 'true' && self::$form_options['auto_respond_from_name'] != '' && self::$form_options['auto_respond_from_email'] != '' && self::$form_options['auto_respond_reply_to'] != '' && self::$form_options['auto_respond_subject'] != '' && self::$form_options['auto_respond_message'] != '' ) {
			echo '<div class="fsc-error">';
			echo __( 'Warning: You have disabled the email address field on the Fields tab, you will not be able to reply to emails and the confirmation email (if enabled) will not work.', 'si-contact-form' );
			echo "</div>\n";
		}
		if ( ! self::$autoresp_ok ) {
			echo '<div id="message" class="updated">';
			echo __( 'Warning: You have disabled the email address field on the Fields tab, you will not be able to reply to emails and the confirmation email (if enabled) will not work.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_from_name]"><?php _e( 'Confirmation email "From" name', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[auto_respond_from_name]" id="si_contact_auto_respond_from_name" type="text" value="<?php echo esc_attr( self::$form_options['auto_respond_from_name'] ); ?>" size="40" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_from_name_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_from_name_tip">
		<?php _e( 'This sets the name in the "from" field when the confirmation email is sent.', 'si-contact-form' ); ?>
		</div>
		<br />
			
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_from_email]"><?php _e( 'Confirmation email "From" address', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[auto_respond_from_email]" id="si_contact_auto_respond_from_email" type="text" value="<?php echo esc_attr( self::$form_options['auto_respond_from_email'] ); ?>" size="40" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_from_email_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_from_email_tip">
		<?php _e( 'This sets the "from" email address when the confirmation email is sent. If confirmation emails are not sent, then set this setting to a real email address on the same web domain as your web site. (Same applies to the "Email-From" setting on this page)', 'si-contact-form' ); ?>
		</div>
		<br />
			
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_reply_to]"><?php _e( 'Confirmation email "Reply To" address', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[auto_respond_reply_to]" id="si_contact_auto_respond_reply_to" type="text" value="<?php echo esc_attr( self::$form_options['auto_respond_reply_to'] ); ?>" size="40" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_reply_to_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_reply_to_tip">
		<?php _e( 'This sets the "reply to" email address when the confirmation emails are sent.', 'si-contact-form' ); ?>
		</div>
		<br />
			
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_subject]"><?php _e( 'Confirmation email subject', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[auto_respond_subject]" id="si_contact_auto_respond_subject" type="text" value="<?php echo esc_attr( self::$form_options['auto_respond_subject'] ); ?>" size="40" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_subject_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_subject_tip">
		<?php _e( 'Type your confirmation email subject here, then enable it with the setting above.', 'si-contact-form' ); ?>
		<?php _e( 'Listed below is an optional list of field tags for fields you can add to the subject.', 'si-contact-form' ) ?><br />
		<?php _e( 'Example: to include the name of the form sender, include this tag in the confirmation email subject:', 'si-contact-form' ); ?> [from_name]<br />
		<?php _e( 'Available field tags:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_subj_arr as $i )
			echo "[$i]<br />";
		?>
		</span>
		</div>
		<br />
			
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_message]"><?php _e( 'Confirmation email message', 'si-contact-form' ); ?>:</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_message_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_message_tip">
		<?php _e( 'Type your confirmation email message here, then enable it with the setting above.', 'si-contact-form' ); ?>
		<?php _e( 'Listed below is an optional list of field tags for fields you can add to the confirmation email message.', 'si-contact-form' ) ?><br />
		<?php _e( 'Example: to include the name of the form sender, include this tag in the confirmation email message:', 'si-contact-form' ); ?> [from_name]<br />
		<?php _e( 'Available field tags:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i ) {
			if ( in_array( $i, array( 'message', 'full_message', 'akismet' ) ) )  // exclude these
				continue;
			echo "[$i]<br />";
		}
		?>
		</span>
		<?php _e( 'Note: If you add any extra fields, they will show up in this list of available tags.', 'si-contact-form' ); ?>
		<?php _e( 'Note: The message fields are intentionally disabled to help prevent spammers from using this form to relay spam.', 'si-contact-form' ); ?>
		<?php _e( 'Try to limit this feature to just using the name field to personalize the message. Do not try to use it to send a copy of what was posted.', 'si-contact-form' ); ?>
					
		</div><br />
		<textarea rows="3" cols="50" name="<?php echo self::$form_option_name; ?>[auto_respond_message]" id="si_contact_auto_respond_message"><?php echo esc_html( self::$form_options['auto_respond_message'] ); ?></textarea>
		<br />
			
		<input name="<?php echo self::$form_option_name; ?>[auto_respond_html]" id="si_contact_auto_respond_html" type="checkbox" <?php if ( self::$form_options['auto_respond_html'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[auto_respond_html]"><?php _e( 'Enable using HTML in confirmation email message.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_auto_respond_html_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_auto_respond_html_tip">
		<?php _e( 'Enable when you want to use HTML in the confirmation email message.', 'si-contact-form' );
		echo ' '; ?>
		<?php _e( 'Then you can use an HTML message. example:', 'si-contact-form' ); ?><br />
		&lt;html&gt;&lt;body&gt;<br />
		&lt;h1&gt;<?php _e( 'Hello World!', 'si-contact-form' ); ?>&lt;/h1&gt;<br />
		&lt;/body&gt;&lt;/html&gt;
		</div>

		</fieldset>
		<?php
	}	// end function email_confirmation_callback()

	static function redirect_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>

		<input name="<?php echo self::$form_option_name; ?>[redirect_enable]" id="si_contact_redirect_enable" type="checkbox" <?php if ( self::$form_options['redirect_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[redirect_enable]"><?php _e( 'Enable redirect after the message sends', 'si-contact-form' ); ?>.</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_redirect_enable_tip">
		<?php _e( 'If enabled: After a user sends a message, the web browser will display "message sent" for x seconds, then redirect to the redirect URL. This can be used to redirect to the blog home page, or a custom "Thank You" page.', 'si-contact-form' ); ?>
		</div>
		<br />

		<label for="<?php echo self::$form_option_name; ?>[redirect_seconds]"><?php _e( 'Redirect delay in seconds', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[redirect_seconds]" id="si_contact_redirect_seconds" type="text" value="<?php echo absint( self::$form_options['redirect_seconds'] ); ?>" size="3" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_seconds_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_redirect_seconds_tip">
		<?php _e( 'How many seconds the web browser will display "message sent" before redirecting to the redirect URL. Values of 0-60 are allowed.', 'si-contact-form' ); ?>
		</div>
		<br />

		<label for="<?php echo self::$form_option_name; ?>[redirect_url]"><?php _e( 'Redirect URL', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[redirect_url]" id="si_contact_redirect_url" type="text" value="<?php echo esc_attr( self::$form_options['redirect_url'] ); ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_url_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_redirect_url_tip">
		<?php _e( 'The form will redirect to this URL after success. This can be used to redirect to the blog home page, or a custom "Thank You" page.', 'si-contact-form' ); ?>
		<?php _e( 'Use FULL URL including http:// for best results.', 'si-contact-form' ); ?>  <?php _e( 'You can set to # for redirect to same page.', 'si-contact-form' ); ?>
		</div>
		<br />
		<?php
		if ( self::$form_options['redirect_query'] == 'true' && self::$form_options['redirect_enable'] != 'true' ) {
			echo '<div class="fsc-error">';
			echo __( 'Warning: Enabling this setting requires the "Enable redirect" to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
		<input name="<?php echo self::$form_option_name; ?>[redirect_query]" id="si_contact_redirect_query" type="checkbox" <?php if ( self::$form_options['redirect_query'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[redirect_query]"><?php _e( 'Enable posted data to be sent as a query string on the redirect URL.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_query_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_redirect_query_tip">
		<?php _e( 'If enabled: The posted data is sent to the redirect URL. This can be used to send the posted data via GET query string to a another form.', 'si-contact-form' ); ?>
		</div>
		<br />
		<a href="http://www.fastsecurecontactform.com/sending-data-by-query-string" target="_new"><?php echo __( 'FAQ: Posted data can be sent as a query string on the redirect URL', 'si-contact-form' ); ?></a>
		<br />
		<table style="border:none;" cellspacing="20">
		  <tr>
		  <td valign="bottom">

		  <label for="<?php echo self::$form_option_name; ?>[redirect_ignore]"><?php echo __( 'Query string fields to ignore', 'si-contact-form' ); ?>:</label>
		  <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_ignore_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		  <div class="fscf_tip" id="si_contact_redirect_ignore_tip">
		<?php _e( 'Optional list of field tags for fields you do not want included in the query string.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br /><br />
		<?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		</div>
		<textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[redirect_ignore]" id="si_contact_redirect_ignore"><?php echo self::$form_options['redirect_ignore']; ?></textarea>
		<br />

		</td><td valign="bottom">

		<label for="<?php echo self::$form_option_name; ?>[redirect_rename]"><?php echo __( 'Query string fields to rename', 'si-contact-form' ); ?>:</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_rename_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		<div class="fscf_tip" id="si_contact_redirect_rename_tip">
		<?php _e( 'Optional list of field tags for fields that need to be renamed for the query string.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br />
		<?php _e( 'Type the old field tag separated by the equals character, then type the new tag, like this: oldname=newname', 'si-contact-form' ); ?><br />
		<?php _e( 'Examples:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		from_name=name<br />
		from_email=email</span><br /><br />
		<?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		</div>
		<textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[redirect_rename]" id="si_contact_redirect_rename"><?php echo self::$form_options['redirect_rename']; ?></textarea>
		<br />

		</td><td valign="bottom">

		<label for="<?php echo self::$form_option_name; ?>[redirect_add]"><?php echo __( 'Query string key value pairs to add', 'si-contact-form' ); ?>:</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_add_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		<div class="fscf_tip" id="si_contact_redirect_add_tip">
		<?php _e( 'Optional list of key value pairs that need to be added.', 'si-contact-form' ) ?><br />
		<?php _e( 'Sometimes the outgoing connection will require fields that were not posted on your form.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br />
		<?php _e( 'Type the key separated by the equals character, then type the value, like this: key=value', 'si-contact-form' ); ?><br />
		<?php _e( 'Examples:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		account=3629675<br />
		newsletter=join<br />
		action=signup</span><br />
		</div>
		<textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[redirect_add]" id="si_contact_redirect_add"><?php echo self::$form_options['redirect_add']; ?></textarea>
		<br />
		</td></tr>
		</table>

		<?php
		if ( self::$form_options['redirect_email_off'] == 'true' && (self::$form_options['redirect_enable'] != 'true' || self::$form_options['redirect_query'] != 'true') ) {
			echo '<div class="fsc-error">';
			echo __( 'Warning: Enabling this setting requires the "Enable redirect" and "Enable posted data to be sent as a query string" to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>

		<?php
		if ( self::$form_options['redirect_email_off'] == 'true' && self::$form_options['redirect_enable'] == 'true' && self::$form_options['redirect_query'] == 'true' ) {
			?><div id="message" class="updated"><strong><?php echo __( 'Just a reminder: You have turned off email sending in the redirect settings below. If that is what you intended, then ignore this message.', 'si-contact-form' ); ?></strong></div><?php
			echo '<div class="fsc-notice">';
			echo __( 'Just a reminder: You have turned off email sending in the setting below. If that is what you intended, then ignore this message.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
		<input name="<?php echo self::$form_option_name; ?>[redirect_email_off]" id="si_contact_redirect_email_off" type="checkbox" <?php if ( self::$form_options['redirect_email_off'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[redirect_email_off]"><?php _e( 'Disable email sending (use only when required while you have enabled query string on the redirect URL).', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_redirect_email_off_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_redirect_email_off_tip">
		<?php _e( 'No email will be sent to you!! The posted data will ONLY be sent to the redirect URL. This can be used to send the posted data via GET query string to a another form. Note: the confirmation email will still be sent if it is enabled.', 'si-contact-form' ); ?>
		</div>
		<br />
		</fieldset>
		<?php
	}	// end function redirect_callback()


	static function advanced_form_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>

        <label for="fs_contact_submit_attributes"><?php _e('Submit button input attributes', 'si-contact-form'); ?>:</label>
        <input name="<?php echo self::$form_option_name; ?>[submit_attributes]" id="si_contact_submit_attributes" type="text" value="<?php echo esc_attr(self::$form_options['submit_attributes']);  ?>" size="60" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_submit_attributes_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        <div class="fscf_tip" id="si_contact_submit_attributes_tip">
        <?php _e('Use to add submit button input attributes.', 'si-contact-form'); ?>
        <?php _e('Useful for tracking a form submission with Google Analytics. example:', 'si-contact-form');
        echo ' onSubmit="pageTracker._trackEvent(\'Contact Form\',\'Submit\');"'; ?>
        </div>
        <br />

        <label for="fs_contact_form_attributes"><?php _e('Form action attributes', 'si-contact-form'); ?>:</label>
        <input name="<?php echo self::$form_option_name; ?>[form_attributes]" id="si_contact_form_attributes" type="text" value="<?php echo esc_attr(self::$form_options['form_attributes']);  ?>" size="60" />
        <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_form_attributes_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        <div class="fscf_tip" id="si_contact_form_attributes_tip">
        <?php _e('Use to add form action attributes.', 'si-contact-form'); ?>
        <?php _e('Useful for tracking a form submission with Google Analytics. example:', 'si-contact-form');
        echo ' onsubmit="_gaq.push([\'_trackEvent\', \'Contact\', \'SubmitForm\', \'Contacts\']);"'; ?>
        </div>
        <br />

		<input name="<?php echo self::$form_option_name; ?>[anchor_enable]" id="fs_contact_anchor_enable" type="checkbox" <?php if ( self::$form_options['anchor_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_anchor_enable"><?php _e( 'Enable an HTML anchor tag on the form POST URL.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_anchor_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_anchor_enable_tip">
		<?php _e( 'Enable if you want the form POST URL to include an HTML Anchor tag #FSContact1 that makes the page scroll to where your form is when you click submit. This is useful for long page content or when you have multiple forms on one page.', 'si-contact-form' ) ?>
		</div>
		<br />

        <input name="<?php echo self::$form_option_name; ?>[print_form_enable]" id="fs_contact_print_form_enable" type="checkbox" <?php if ( self::$form_options['print_form_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_print_form_enable"><?php _e( 'Enable to add a "view / print message" button after message sent.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_print_form_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_print_form_enable_tip">
		<?php _e( 'Enable if you want add a "view / print message" button after message sent. This feature will be skipped if the "redirect after the message sends" is also enabled.', 'si-contact-form' ); echo ' ';?>
 		<?php _e( 'Note: the message content will be viewable and could potentially expose privacy if the messages contain private information.', 'si-contact-form' ) ?>
		</div>
		<br />

		<input name="enable_php_sessions" id="si_contact_enable_php_sessions" type="checkbox" <?php if ( self::$global_options['enable_php_sessions'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
		<label for="enable_php_sessions"><?php _e( 'Enable PHP sessions.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_enable_php_sessions_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_enable_php_sessions_tip">
        <?php _e('Enables PHP session handling. Default is not enabled. You can enable this setting to use PHP sessions for shortcode attributes and the CAPTCHA. PHP Sessions must be supported by your web host or there may be session errors. This setting affects all forms.', 'si-contact-form'); ?>
		</div>
		<br />
		<?php
		if ( self::$global_options['enable_php_sessions'] != 'true' ) {
			$check_this_dir = WP_PLUGIN_DIR . '/si-contact-form/captcha/cache';
			if ( is_writable( $check_this_dir ) ) {
				//echo '<span style="color: green">OK - Writable</span> ' . substr(sprintf('%o', fileperms($check_this_dir)), -4);
			} else if ( !file_exists( $check_this_dir ) ) {
				echo '<span style="color: red;">';
				echo __( 'There is a problem with the directory', 'si-contact-form' );
				echo ' /captcha/cache/. ';
				echo __( 'The directory is not found, a <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">permissions</a> problem may have prevented this directory from being created.', 'si-contact-form' );
				echo ' ';
				echo __( 'Fixing the actual problem is recommended, but you can check this setting on the contact form options page: "Use PHP Sessions" and the captcha will work this way just fine (as long as PHP Sessions are supported by your web host).', 'si-contact-form');
				echo '</span><br />';
			} else {
				echo '<span style="color: red;">';
				echo __( 'There is a problem with the directory', 'si-contact-form' ) . ' /captcha/cache/. ';
				echo __( 'The directory Unwritable (<a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">fix permissions</a>)', 'si-contact-form' ) . '. ';
				echo __( 'Permissions are: ', 'si-contact-form' );
				echo ' ';
				echo substr( sprintf( '%o', fileperms( $check_this_dir ) ), -4 );
				echo ' ';
				echo __( 'Fixing this may require assigning 0755 permissions or higher (e.g. 0777 on some hosts. Try 0755 first, because 0777 is sometimes too much and will not work.)', 'si-contact-form' );
				echo ' ';
				echo __( 'Fixing the actual problem is recommended, but you can check this setting on the contact form options page: "Use PHP Sessions" and the captcha will work this way just fine (as long as PHP Sessions are supported by your web host).', 'si-contact-form');
				echo '</span><br />';
			}
		}
		?>

 		<input name="<?php echo self::$form_option_name; ?>[enable_reset]" id="fs_contact_enable_reset" type="checkbox" <?php if ( self::$form_options['enable_reset'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_enable_reset"><?php _e( 'Enable a "Reset" button on the form.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_enable_reset_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_enable_reset_tip">
		<?php _e( 'When a visitor clicks a reset button, the form entries are reset to the default values.', 'si-contact-form' ); ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name; ?>[enable_areyousure]" id="fs_contact_enable_confirm" type="checkbox" <?php if ( self::$form_options['enable_areyousure'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_enable_confirm"><?php _e( 'Enable an "Are you sure?" popup for the submit button.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_enable_areyousure_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_enable_areyousure_tip">
		<?php _e( 'When a visitor clicks the form submit button, a popup message will ask "Are you sure?". This message can be changed in the "change field labels" settings on the Styles/Labels tab.', 'si-contact-form' ); ?>
		</div>
		<br />


		<input name="<?php echo self::$form_option_name; ?>[enable_submit_oneclick]" id="fs_contact_enable_submit_oneclick" type="checkbox" <?php if ( self::$form_options['enable_submit_oneclick'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_enable_submit_oneclick"><?php _e( 'Enable to prevent double click on submit button.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_enable_submit_oneclick_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_enable_submit_oneclick_tip">
		<?php _e( 'This setting disables the Submit button after click, to prevent double click on button. Also prevents going back and submit the form again.', 'si-contact-form' );
        echo ' ';
        _e( 'Note: this setting is ignored if the "Are you sure?" popup for the submit button is enabled, or when you have filled in the Submit button input attributes setting with a "onclick" attribute.', 'si-contact-form' );
        ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name; ?>[enable_credit_link]" id="fs_contact_enable_credit_link" type="checkbox" <?php if ( self::$form_options['enable_credit_link'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
		<label for="fs_contact_enable_credit_link"><?php _e( 'Enable plugin credit link:', 'si-contact-form' ) ?></label> <?php echo __( 'Powered by', 'si-contact-form' ) . ' <a href="http://wordpress.org/extend/plugins/si-contact-form/" target="_new">' . __( 'Fast Secure Contact Form', 'si-contact-form' ); ?></a>
        <br />

        <div><br />
        <label for="fs_contact_after_form_note"><?php _e('After form additional HTML', 'si-contact-form'); ?>:</label><br />
        <textarea rows="3" cols="40" name="<?php echo self::$form_option_name;?>[after_form_note]" id="fs_contact_after_form_note"><?php echo esc_textarea(self::$form_options['after_form_note']); ?></textarea>
        <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_after_form_note_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        </div><div class="fscf_tip" id="si_contact_after_form_note_tip">
        <?php _e('This is printed after the form. HTML is allowed.', 'si-contact-form');?>
        </div>

        <div><br />
        <label for="fs_contact_success_page_html"><?php _e('Success page additional HTML', 'si-contact-form'); ?>:</label><br />
        <textarea rows="3" cols="40" name="<?php echo self::$form_option_name;?>[success_page_html]" id="fs_contact_success_page_html"><?php echo esc_textarea(self::$form_options['success_page_html']); ?></textarea>
        <a style="cursor:pointer;" title="<?php esc_attr_e('Click for Help!', 'si-contact-form'); ?>" onclick="toggleVisibility('si_contact_success_page_html_tip');"><?php _e('help', 'si-contact-form'); ?></a>
        </div><div class="fscf_tip" id="si_contact_success_page_html_tip">
        <?php _e('This is printed on the success page after the message sent text. Useful for tracking a conversion with Google Analytics. Put the Google Code for Conversion Page here. HTML is allowed.', 'si-contact-form');?>
        </div>

		</fieldset>
		<?php
	}	//


	static function advanced_email_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
      <label for="fs_contact_php_mailer_enable"><?php _e( 'Send Email function:', 'si-contact-form' ); ?></label>
	  <select id="fs_contact_php_mailer_enable" name="<?php echo self::$form_option_name; ?>[php_mailer_enable]">
	   <?php
	   $selected = '';
	   foreach ( array( 'wordpress' => esc_attr( __( 'WordPress', 'si-contact-form' ) ), 'php' => esc_attr( __( 'PHP', 'si-contact-form' ) ) ) as $k => $v ) {
		   if ( self::$form_options['php_mailer_enable'] == "$k" )
			   $selected = ' selected="selected"';
		   echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
		   $selected = '';
	   }
	   ?>
		</select>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_php_mailer_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_php_mailer_enable_tip">
		<?php _e( 'Emails are normally sent by the wordpress mail function. Other functions are provided for diagnostic uses.', 'si-contact-form' ); ?>
		<?php _e( 'If your form does not send any email, first try setting the "Email From" setting above because some web hosts do not allow PHP to send email unless the "From:" email address is on the same web domain.', 'si-contact-form' ); ?>
		<?php _e( 'Note: attachments are only supported when using the "WordPress" mail function.', 'si-contact-form' ); ?>
	   </div>
	   <br />



		<input name="<?php echo self::$form_option_name; ?>[email_html]" id="fs_contact_email_html" type="checkbox" <?php if ( self::$form_options['email_html'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_html"><?php _e( 'Enable to receive email as HTML instead of plain text.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_html_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_html_tip">
		<?php _e( 'Enable if you want the email message sent as HTML format. HTML format is desired if you want to avoid a 70 character line wordwrap when you copy and paste the email message. Normally the email is sent in plain text wordwrapped 70 characters per line to comply with most email programs.', 'si-contact-form' ) ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name; ?>[email_inline_label]" id="fs_contact_email_inline_label" type="checkbox" <?php if ( self::$form_options['email_inline_label'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_inline_label"><?php _e( 'Enable to have the email labels on same line as values.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_inline_label_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_inline_label_tip">
		<?php _e( 'Enable if you want the email labels on same line as values. Normally the email labels are on separate lines as the values.', 'si-contact-form' ) ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name; ?>[email_hide_empty]" id="fs_contact_email_hide_empty" type="checkbox" <?php if ( self::$form_options['email_hide_empty'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_hide_empty"><?php _e( 'Enable to skip names of non-required and unfilled-out fields in emails.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_hide_empty_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_hide_empty_tip">
		<?php _e( 'Enable if you want the email message to skip names of non-required and unfilled-out fields.', 'si-contact-form' ) ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name; ?>[email_keep_attachments]" id="fs_contact_email_keep_attachments" type="checkbox" <?php if ( self::$form_options['email_keep_attachments'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_keep_attachments"><?php _e( 'Enable to not delete email attachments from the server.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_keep_attachments_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_keep_attachments_tip">
		<?php _e( 'Enable if you want the email attachments to not be deleted automatically. They will stay in the /plugins/si-contact-form/attachments folder until you delete them.', 'si-contact-form' ); echo ' ';?>
 		<?php _e( 'Note: use this feature at your own risk, because storing the files there could potentially expose privacy if the files uploaded contain private information.', 'si-contact-form' ) ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name;?>[name_case_enable]" id="fs_contact_name_case_enable" type="checkbox" <?php if ( self::$form_options['name_case_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_name_case_enable"><?php _e( 'Enable upper case alphabet correction.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_name_case_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_name_case_enable_tip">
		<?php _e( 'Automatically corrects form input using a function knowing about alphabet case (example: correct caps on McDonald, or correct USING ALL CAPS).', 'si-contact-form' ); ?>
		<?php _e( 'Enable on English language only because it can cause accent character problems if enabled on other languages.', 'si-contact-form' ); ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name;?>[sender_info_enable]" id="fs_contact_sender_info_enable" type="checkbox" <?php if ( self::$form_options['sender_info_enable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_sender_info_enable"><?php _e( 'Enable sender information in email footer.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_sender_info_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_sender_info_enable_tip">
		<?php _e( 'Includes detailed information in the email about the sender. Such as IP Address, date, time, and which web browser they used.', 'si-contact-form' ); ?>
		<?php echo ' ';
		_e( 'Install the <a href="http://wordpress.org/extend/plugins/visitor-maps/">Visitor Maps plugin</a> to enable geolocation and then city, state, country will automatically be included.', 'si-contact-form' ); ?>
		</div>
		<br />

		<input name="<?php echo self::$form_option_name;?>[email_check_easy]" id="fs_contact_email_check_easy" type="checkbox" <?php if ( self::$form_options['email_check_easy'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_check_easy"><?php _e( 'Enable Internationalized Domain Names when checking for a valid email address.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_check_easy_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_check_easy_tip">
		<?php _e( 'Allows Internationalized Domain Names for email address validation. Because this relaxes the email validation check considerably, do not enable unless you have to allow Russian, Japanese, Chinese, etc. characters in the email address.', 'si-contact-form' ) ?>
		</div>
        <br />

		<input name="<?php echo self::$form_option_name;?>[email_check_dns]" id="fs_contact_email_check_dns" type="checkbox" <?php if ( self::$form_options['email_check_dns'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_email_check_dns"><?php _e( 'Enable checking DNS records for the domain name when checking for a valid email address.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_email_check_dns_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_email_check_dns_tip">
		<?php _e( 'Improves email address validation by checking that the domain of the email address actually has a valid DNS record.', 'si-contact-form' ) ?>
		</div>

		</fieldset>
		<?php
	}	//


	static function silent_sending_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
		<p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>
		<?php echo __( 'Posted form data can be sent silently to a remote form using the method GET or POST.', 'si-contact-form' ); ?>
		<br />
		<a href="http://www.fastsecurecontactform.com/send-form-data-elsewhere" target="_new"><?php echo __( 'FAQ: Send the posted form data to another site.', 'si-contact-form' ); ?></a>
		<br />
		<br />
			   
		<label for="<?php echo self::$form_option_name; ?>[silent_send]"><?php _e( 'Silent Remote Sending:', 'si-contact-form' ); ?></label>
		<select id="si_contact_silent_send" name="<?php echo self::$form_option_name; ?>[silent_send]">
		<?php
		$silent_send_array = array(
			'off'		 => esc_html( __( 'Off', 'si-contact-form' ) ),
			'get'		 => esc_html( __( 'Enabled: Method GET', 'si-contact-form' ) ),
			'post'		 => esc_html( __( 'Enabled: Method POST', 'si-contact-form' ) ),
		);
		$selected = '';
		foreach ( $silent_send_array as $k => $v ) {
			if ( self::$form_options['silent_send'] == "$k" )
				$selected = ' selected="selected"';
			echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
			$selected = '';
		}
		?>
		</select>
			
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_send_tip');">
					
		<?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_silent_send_tip">
		<?php _e( 'If enabled: After a user sends a message, the form can silently send the posted data to a third party remote URL. This can be used for a third party service such as a mailing list API.', 'si-contact-form' ); ?>
		<?php echo ' ';
		_e( 'Select method GET or POST based on the remote API requirement.', 'si-contact-form' ); ?>
		</div>
		<br />
					
		<?php
		if ( self::$form_options['silent_send'] != 'off' && self::$form_options['silent_url'] == '' ) {
			echo '<div class="fsc-error">';
			echo __( 'Warning: Enabling this setting requires the "Silent Remote URL" to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>

		<label for="<?php echo self::$form_option_name; ?>[silent_url]"><?php _e( 'Silent Remote URL', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[silent_url]" id="si_contact_silent_url" type="text" value="<?php echo self::$form_options['silent_url']; ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_url_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_silent_url_tip">
		<?php _e( 'The form will silently send the form data to this URL after success. This can be used for a third party service such as a mailing list API.', 'si-contact-form' ); ?>
		<?php _e( 'Use FULL URL including http:// for best results.', 'si-contact-form' ); ?>
		</div>
		<br />

		<table style="border:none;" cellspacing="20">
		  <tr>
		  <td valign="bottom">
			  
		  <label for="<?php echo self::$form_option_name; ?>[silent_ignore]"><?php echo __( 'Silent send fields to ignore', 'si-contact-form' ); ?>:</label>
		  <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_ignore_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		  <div class="fscf_tip" id="si_contact_silent_ignore_tip">
		<?php _e( 'Optional list of field tags for fields you do not want included.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br /><br />
		<?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		  </div>
		  <textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[silent_ignore]" id="si_contact_silent_ignore"><?php echo self::$form_options['silent_ignore']; ?></textarea>
		  <br />

		</td><td valign="bottom">
			 
		  <label for="<?php echo self::$form_option_name; ?>[silent_rename]"><?php echo __( 'Silent send fields to rename', 'si-contact-form' ); ?>:</label>
		  <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_rename_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		  <div class="fscf_tip" id="si_contact_silent_rename_tip">
		<?php _e( 'Optional list of field tags for fields that need to be renamed.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br />
		<?php _e( 'Type the old field tag separated by the equals character, then type the new tag, like this: oldname=newname', 'si-contact-form' ); ?><br />
		<?php _e( 'Examples:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		from_name=name<br />
		from_email=email</span><br /><br />
		<?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		  </div>
		  <textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[silent_rename]" id="si_contact_silent_rename"><?php echo self::$form_options['silent_rename']; ?></textarea>
		  <br />
				  
		  </td><td valign="bottom">
			  
		  <label for="<?php echo self::$form_option_name; ?>[silent_add]"><?php echo __( 'Silent send key value pairs to add', 'si-contact-form' ); ?>:</label>
		  <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_add_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		  <div class="fscf_tip" id="si_contact_silent_add_tip">
		<?php _e( 'Optional list of key value pairs that need to be added.', 'si-contact-form' ) ?><br />
		<?php _e( 'Sometimes the outgoing connection will require fields that were not posted on your form.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br />
		<?php _e( 'Type the key separated by the equals character, then type the value, like this: key=value', 'si-contact-form' ); ?><br />
		<?php _e( 'Examples:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		account=3629675<br />
		newsletter=join<br />
		action=signup</span><br />
		  </div>
		  <textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[silent_add]" id="si_contact_silent_add"><?php echo self::$form_options['silent_add']; ?></textarea>
		  <br />
		 </td></tr>
		 </table>

       	<label for="<?php echo self::$form_option_name; ?>[silent_conditional_field]"><?php _e( 'Silent Conditional Field (optional)', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[silent_conditional_field]" id="si_contact_silent_conditional_field" type="text" value="<?php echo self::$form_options['silent_conditional_field']; ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_conditional_field_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_silent_conditional_field_tip">
		<?php _e( 'Use this optional setting to conditionally disable silent sending unless this field tag and value are selected and submitted.', 'si-contact-form' ); ?><br />
		<?php _e( 'Example usage: Your form has a checkbox to "signup for our newsletter" with the tag "signup-newsletter". You do a silent send to MailChimp to sign up people to the newsletter but you want to disable the silent send if the checkbox is left unchecked.', 'si-contact-form' ); ?><br />
        <?php _e( 'For this example you will set the Silent Conditional Field to "signup-newsletter" and the Silent Conditional Value to "selected", this will match the field tag and value when the checkbox is selected on the form.', 'si-contact-form' ); ?><br />
        <?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br /><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		</div>
		<br />

       	<label for="<?php echo self::$form_option_name; ?>[silent_conditional_value]"><?php _e( 'Silent Conditional Value (optional)', 'si-contact-form' ); ?>:</label>
		<input name="<?php echo self::$form_option_name; ?>[silent_conditional_value]" id="si_contact_silent_conditional_value" type="text" value="<?php echo self::$form_options['silent_conditional_value']; ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_conditional_value_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_silent_conditional_value_tip">
		<?php _e( 'Use this optional setting to conditionally disable silent sending unless this field tag and value are selected and submitted.', 'si-contact-form' ); ?><br />
		<?php _e( 'Example usage: Your form has a checkbox to "signup for our newsletter" with the tag signup-newsletter. You do a silent send to MailChimp to sign up people to the newsletter but you want to disable the silent send if the checkbox is left unchecked.', 'si-contact-form' ); ?><br />
        <?php _e( 'For this example you will set the Silent Conditional Field to "signup-newsletter" and the Silent Conditional Value to "selected", this will match the field tag and value when the checkbox is selected on the form.', 'si-contact-form' ); ?><br />
        <?php _e( 'For checkbox field types use "selected" for this setting, for other field types put the value that shows up in the email when this field is selected.', 'si-contact-form' ); ?><br />
		</div>
		<br />
			 
		<?php
		if ( self::$form_options['silent_email_off'] == 'true' && (self::$form_options['silent_send'] == 'off' || self::$form_options['silent_url'] == '') ) {
			echo '<div class="fsc-error">';
			echo __( 'Warning: Enabling this setting requires the "Silent Remote Send" and "Silent Remote URL" to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
				   
		<?php
		if ( self::$form_options['silent_email_off'] == 'true' && self::$form_options['silent_send'] != 'off' ) {
			?><div id="message" class="updated"><strong><?php echo __( 'Just a reminder: You have turned off email sending in the Silent Remote Send settings below. This is just a reminder in case that was a mistake. If that is what you intended, then ignore this message.', 'si-contact-form' ); ?></strong></div><?php
			echo '<div class="fsc-error">';
			echo __( 'Just a reminder: You have turned off email sending in the setting below. This is just a reminder in case that was a mistake. If that is what you intended, then ignore this message.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
		<input name="<?php echo self::$form_option_name; ?>[silent_email_off]" id="si_contact_silent_email_off" type="checkbox" <?php if ( self::$form_options['silent_email_off'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[silent_email_off]"><?php _e( 'Disable email sending (use only when required while you have enabled silent remote sending).', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_silent_email_off_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_silent_email_off_tip">
		<?php _e( 'No email will be sent to you! The posted data will ONLY be sent to the silent remote URL. This can be used for a third party service such as a mailing list API. Note: the confirmation email will still be sent if it is enabled.', 'si-contact-form' ); ?>
		</div>
		<br />
					
		</fieldset>
		<?php
	}	// end funciton silent_sending_callback()

	
	static function data_export_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">

		<?php echo sprintf( __( 'Posted fields data can be exported to another plugin such as the <a href="%s" target="_new">Contact Form DB plugin</a>.', 'si-contact-form' ), 'http://www.fastsecurecontactform.com/save-to-database' ); ?>
		<br />
		<a href="http://www.fastsecurecontactform.com/save-to-database" target="_new"><?php echo __( 'FAQ: Save to a database or export to CSV file.', 'si-contact-form' ); ?></a>
		<br />

		<table style="border:none;" cellspacing="20">
		<tr>
		<td valign="bottom">
			  
		<label for="<?php echo self::$form_option_name; ?>[export_ignore]"><?php echo __( 'Data export fields to ignore', 'si-contact-form' ); ?>:</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_export_ignore_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		<div class="fscf_tip" id="si_contact_export_ignore_tip">
		<?php _e( 'Optional list of field tag for fields you do not want included in the data export.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br /><br />
		<?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		</div>

		<textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[export_ignore]" id="si_contact_export_ignore"><?php echo self::$form_options['export_ignore']; ?></textarea>
		<br />
		 </td><td valign="bottom">
			 
		<label for="<?php echo self::$form_option_name; ?>[export_rename]"><?php echo __( 'Data export fields to rename', 'si-contact-form' ); ?>:</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_export_rename_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		<div class="fscf_tip" id="si_contact_export_rename_tip">
		<?php _e( 'Optional list of field tags for fields that need to be renamed before data export.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br />
		<?php _e( 'Type the old field tag separated by the equals character, then type the new tag, like this: oldname=newname', 'si-contact-form' ); ?><br />
		<?php _e( 'Examples:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		from_name=name<br />
		from_email=email</span><br /><br />
		<?php _e( 'Available fields on this form:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		<?php
		// show available fields
		foreach ( self::$av_fld_arr as $i )
			echo "$i<br />";
		?>
		</span>
		</div>
		<textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[export_rename]" id="si_contact_export_rename"><?php echo self::$form_options['export_rename']; ?></textarea>
		<br />
		</td><td valign="bottom">
			  
		<label for="<?php echo self::$form_option_name; ?>[export_add]"><?php echo __( 'Data export key value pairs to add', 'si-contact-form' ); ?>:</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_export_add_tip');"><?php echo __( 'help', 'si-contact-form' ); ?></a><br />
		<div class="fscf_tip" id="si_contact_export_add_tip">
		<?php _e( 'Optional list of key value pairs that need to be added.', 'si-contact-form' ) ?><br />
		<?php _e( 'Sometimes the outgoing connection will require fields that were not posted on your form.', 'si-contact-form' ) ?><br />
		<?php _e( 'Start each entry on a new line.', 'si-contact-form' ); ?><br />
		<?php _e( 'Type the key separated by the equals character, then type the value, like this: key=value', 'si-contact-form' ); ?><br />
		<?php _e( 'Examples:', 'si-contact-form' ); ?>
		<span style="margin: 2px 0" dir="ltr"><br />
		account=3629675<br />
		newsletter=join<br />
		action=signup</span><br />
		</div>
		<textarea rows="4" cols="25" name="<?php echo self::$form_option_name; ?>[export_add]" id="si_contact_silent_add"><?php echo self::$form_options['export_add']; ?></textarea>
		<br />
		</td></tr>
		</table>
				   
		<?php
		if ( self::$form_options['export_email_off'] == 'true' ) {
			?><div id="message" class="updated"><strong><?php echo __( 'Just a reminder: You have turned off email sending in the data export settings below. This is just a reminder in case that was a mistake. If that is what you intended, then ignore this message.', 'si-contact-form' ); ?></strong></div><?php
			echo '<div class="fsc-notice">';
			echo __( 'Just a reminder: You have turned off email sending in the setting below. This is just a reminder in case that was a mistake. If that is what you intended, then ignore this message.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>
		<input name="<?php echo self::$form_option_name; ?>[export_email_off]" id="si_contact_export_email_off" type="checkbox" <?php if ( self::$form_options['export_email_off'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[export_email_off]"><?php _e( 'Disable email sending (optional).', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_export_email_off_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_export_email_off_tip">
		<?php _e( 'No email will be sent to you! The posted data will ONLY be sent to the data export. Note: the confirmation email will still be sent if it is enabled.', 'si-contact-form' ); ?>
		</div>
		<?php
	}	// end function data_export_callback()


  	static function captcha_settings_callback() {

		$captcha_url_cf = FSCF_Util::get_captcha_url_cf();

		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
        <p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
		</p>

        <strong><?php _e( 'Secure Image CAPTCHA', 'si-contact-form' ); ?></strong> <br /><br />

		<input name="<?php echo self::$form_option_name; ?>[captcha_enable]" id="si_contact_captcha_enable" type="checkbox" <?php if ( self::$form_options['captcha_enable'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[captcha_enable]"><?php _e( 'Enable Secure Image CAPTCHA.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_captcha_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_captcha_enable_tip">
		<?php _e( 'Limits spam by requiring that the user pass a Secure Image CAPTCHA test before posting. No key required', 'si-contact-form' ) ?>
		</div>
		<br />
        <br />

       	<strong><?php _e( 'Google reCAPTCHA V2', 'si-contact-form' ); ?></strong> <br /><br />

        <input name="<?php echo self::$form_option_name; ?>[recaptcha_enable]" id="si_contact_recaptcha_enable" type="checkbox" <?php if ( self::$form_options['recaptcha_enable'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[recaptcha_enable]"><?php _e( 'Enable reCAPTCHA V2.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_recaptcha_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_recaptcha_enable_tip">
		<?php _e( 'Limits spam by requiring that the user pass a Google reCAPTCHA V2 test before posting. Protect your website from spam and abuse while letting real people pass through with ease. Requires registering free Google reCAPTCHA keys for this site.', 'si-contact-form' ) ?>
		</div>
		<br />


 		<?php
		if ( self::$form_options['recaptcha_enable']  == 'true' && (self::$global_options['recaptcha_public_key'] == '' || self::$global_options['recaptcha_secret_key'] == '') ) {
			echo '<div class="error notice">';
			echo __( 'Warning: reCAPTCHA V2 API key(s) missing. Enabling reCAPTCHA V2 on the Security tab requires the keys to also be set.', 'si-contact-form' );
			echo "</div>\n";
		}
		?>

		<label for="recaptcha_public_key"><?php _e( 'reCAPTCHA V2 Site Key', 'si-contact-form' ); ?>:</label>
		<input name="recaptcha_public_key" id="si_contact_recaptcha_public_key" type="text" value="<?php echo self::$global_options['recaptcha_public_key']; ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_recaptcha_public_key_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_recaptcha_public_key_tip">
		<?php _e( 'The key in the HTML code your site serves to users.', 'si-contact-form' ); ?>
		</div>
		<br />

        <label for="recaptcha_secret_key"><?php _e( 'reCAPTCHA V2 Secret Key', 'si-contact-form' ); ?>:</label>
		<input name="recaptcha_secret_key" id="si_contact_recaptcha_secret_key" type="text" value="<?php echo self::$global_options['recaptcha_secret_key']; ?>" size="50" />
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_recaptcha_secret_key_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_recaptcha_secret_key_tip">
		<?php _e( 'For communication between your site and Google.  Be sure to keep it a secret.', 'si-contact-form' ); ?>
		</div>
		<br />

        <?php _e( '<a href="https://www.google.com/recaptcha/intro/" target="_new">Get reCAPTCHA V2 keys for your site here</a>', 'si-contact-form' ); ?><br />
        <?php _e( 'Note: Do not copy any google HTML code to your site HTML. Google instructions might say to, but this plugin does all that for you!', 'si-contact-form' ); ?>

        	<br />
         <label for="<?php echo self::$form_option_name; ?>[recaptcha_dark]"><?php _e( 'reCAPTCHA Theme: dark or light?', 'si-contact-form' ); ?></label>
			<select id="si_contact_recaptcha_dark" name="<?php echo self::$form_option_name; ?>[recaptcha_dark]">
			<?php
			$recaptcha_dark_array = array(
				'false' => __( 'Light theme', 'si-contact-form'  ),
				'true'	=> __( 'Dark theme', 'si-contact-form'  ),
			);
			$selected = '';
			foreach ( $recaptcha_dark_array as $k => $v ) {
				if ( self::$form_options['recaptcha_dark'] == "$k" )
					$selected = ' selected="selected"';
				echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>' . "\n";
				$selected = '';
			}
			?>
			</select>
			<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_recaptcha_dark_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
			<div class="fscf_tip" id="si_contact_recaptcha_dark_tip">
			<?php _e( 'The color theme of the reCAPTCHA widget.', 'si-contact-form' ); ?>
			</div>
          <br /> <br />

         <strong><?php _e( 'Optional', 'si-contact-form' ); ?></strong>

        <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_optional_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_optional_tip">
		<?php _e( 'The settings below apply to both Secure Image CAPTCHA or reCAPTCHA when enabled.', 'si-contact-form' ) ?>
		</div>
		<br /><br />

      	<input name="<?php echo self::$form_option_name; ?>[captcha_perm]" id="si_contact_captcha_perm" type="checkbox" <?php if ( self::$form_options['captcha_perm'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[captcha_perm]"><?php _e( 'Hide any CAPTCHA for', 'si-contact-form' ); ?>
		<strong><?php _e( 'registered', 'si-contact-form' ); ?></strong> <?php __( 'users who can', 'si-contact-form' ); ?>:</label>
		<?php self::si_contact_captcha_perm_dropdown( self::$form_option_name . '[captcha_perm_level]', self::$form_options['captcha_perm_level'] ); ?>
		<br />

        <input name="<?php echo self::$form_option_name; ?>[captcha_small]" id="si_contact_captcha_small" type="checkbox" <?php if ( self::$form_options['captcha_small'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[captcha_small]"><?php _e( 'Enable smaller CAPTCHA.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_captcha_small_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_captcha_small_tip">
		<?php _e( 'Makes the Secure Image CAPTCHA or the Google reCAPTCHA smaller.', 'si-contact-form' ) ?>
		</div>
		<br />

        <input name="<?php echo self::$form_option_name; ?>[honeypot_enable]" id="si_contact_honeypot_enable" type="checkbox" <?php if ( self::$form_options['honeypot_enable'] == 'true' ) echo ' checked="checked" '; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[honeypot_enable]"><?php _e( 'Enable honeypot spambot trap.', 'si-contact-form' ); ?></label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_honeypot_enable_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_honeypot_enable_tip">
		<?php _e( 'Enables hidden empty field honyepot trap for spam bots. For best results, do not enable unless you have a spam problem. May not do much. Do not use with cache plugins.', 'si-contact-form' ) ?>
		</div>
		<br />

		</fieldset>
		<?php
	}	// end function captcha_settings_callback()

	static function si_contact_captcha_perm_dropdown($select_name, $checked_value='') {
			// choices: Display text => permission_level
			$choices = array (
					 __('All registered users', 'si-contact-form') => 'read',
					 __('Edit posts', 'si-contact-form') => 'edit_posts',
					 __('Publish Posts', 'si-contact-form') => 'publish_posts',
					 __('Moderate Comments', 'si-contact-form') => 'moderate_comments',
					 __('Administer site', 'si-contact-form') => 'level_10'
					 );
			// print the <select> and loop through <options>
			echo '<select name="' . esc_attr($select_name) . '" id="' . esc_attr($select_name) . '">' . "\n";
			foreach ($choices as $text => $capability) :
					if ($capability == $checked_value) $checked = ' selected="selected" ';
					echo "\t". '<option value="' . esc_attr($capability) . '"' . $checked . '>'.esc_html($text)."</option>\n";
					$checked = '';
			endforeach;
			echo "\t</select>\n";
	} // end function si_contact_captcha_perm_dropdown

	static function akismet_settings_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">


		<strong><?php _e( 'Akismet Spam Prevention', 'si-contact-form' ); ?></strong>

		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_akismet_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_akismet_tip">
		<?php _e( 'Akismet is a WordPress spam prevention plugin. When Akismet is installed and active, this form will be checked with Akismet to help prevent spam.', 'si-contact-form' ) ?>
		</div>
		<br /><br />

		<?php
		$akismet_installed = 0;
		if ( self::$form_options['akismet_disable'] == 'false' ) {
			if ( is_callable( array( 'Akismet', 'verify_key' ) ) || function_exists( 'akismet_verify_key' ) ) {
				if ( ! isset( self::$form_options['akismet_check'] ) ) {
					echo '<span style="background-color:#99CC99;">' . __( 'Akismet is installed.', 'si-contact-form' ) . '</span>';
					$akismet_installed = 1;
				}
				// Has an Akismet check been requested?
				if ( strpos($_SERVER['REQUEST_URI'],'akismet_check') !== false ) {

					$key_status = 'failed';
					$key = get_option( 'wordpress_api_key' );
					if ( empty( $key ) ) {
						$key_status = 'empty';
					} else {
                        if ( is_callable( array( 'Akismet', 'verify_key' ) ) )
						    $key_status = Akismet::verify_key( $key );  // akismet 3.xx
                        else
                             $key_status = akismet_verify_key( $key );  // akismet 2.xx
					}
					if ( $key_status == 'valid' ) {
						$akismet_installed = 1;
						?><div id="message" class="updated"><strong><?php echo __( 'Akismet is enabled and the key is valid. This form will be checked with Akismet to help prevent spam', 'si-contact-form' ); ?></strong></div><?php
						echo '<div class="fsc-notice">' . __( 'Akismet is installed and the key is valid. This form will be checked with Akismet to help prevent spam.', 'si-contact-form' ) . '</strong></div>';
					} else if ( $key_status == 'invalid' ) {
						?><div id="message" class="error"><strong><?php echo __( 'Akismet plugin is enabled but key needs to be activated', 'si-contact-form' ); ?></strong></div><?php
						echo '<div class="fsc-error">' . __( 'Akismet plugin is installed but key needs to be activated.', 'si-contact-form' ) . '</div>';
					} else if ( !empty( $key ) && $key_status == 'failed' ) {
						?><div id="message" class="error"><strong><?php echo __( 'Akismet plugin is enabled but key failed to verify', 'si-contact-form' ); ?></strong></div><?php
						echo '<div class="fsc-error">' . __( 'Akismet plugin is installed but key failed to verify.', 'si-contact-form' ) . '</div>';
					} else {
						?><div id="message" class="error"><strong><?php echo __( 'Akismet plugin is installed but key has not been entered.', 'si-contact-form' ); ?></strong></div><?php
						echo '<div class="fsc-error">' . __( 'Akismet plugin is installed but key has not been entered.', 'si-contact-form' ) . '</div>';
					}
				}
				?>
				<br />
				  <input name="<?php echo self::$form_option_name; ?>[akismet_check]" id="si_contact_akismet_check" type="checkbox" value="true" />
				  <label for="<?php echo self::$form_option_name; ?>[akismet_check]"><?php _e( 'Check this and click "Save Changes" to determine if Akismet key is active.', 'si-contact-form' ); ?></label>
				<br />
				<?php echo '<a href="' . admin_url( "options-general.php?page=akismet-key-config" ) . '">' . __( 'Configure Akismet', 'si-contact-form' ) . '</a>'; ?>
				<?php
			} else {
				echo '<div class="fsc-notice">' . __( 'Akismet plugin is not installed or is deactivated.', 'si-contact-form' ) . '</div>';
			}
		} else {
			echo '<div class="fsc-notice">' . __( 'Akismet is turned off for this form.', 'si-contact-form' ) . '</div>';
		}
		if ( self::$form_options['akismet_disable'] == 'false' ) {
			?>
			<br />
			<label for="<?php echo self::$form_option_name; ?>[akismet_send_anyway]"><?php _e( 'What should happen if Akismet determines the message is spam?', 'si-contact-form' ); ?></label>
			<select id="si_contact_akismet_send_anyway" name="<?php echo self::$form_option_name; ?>[akismet_send_anyway]">
			<?php
			$akismet_send_anyway_array = array(
				'false' => __( 'Block spam messages', 'si-contact-form'  ),
				'true'	=> __( 'Tag as spam and send anyway', 'si-contact-form'  ),
			);
			$selected = '';
			foreach ( $akismet_send_anyway_array as $k => $v ) {
				if ( self::$form_options['akismet_send_anyway'] == "$k" )
					$selected = ' selected="selected"';
				echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>' . "\n";
				$selected = '';
			}
			?>
			</select>
			<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_akismet_send_anyway_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
			<div class="fscf_tip" id="si_contact_akismet_send_anyway_tip">
			<?php _e( 'If you select "block spam messages". If Akismet determines the message is spam: An error will display "Invalid Input - Spam?" and the form will not send.', 'si-contact-form' ); ?>
			<?php echo ' ';
			_e( 'If you select "tag as spam and send anyway". If Akismet determines the message is spam: The message will send and the subject wil begin with "Akismet: Spam". This way you can have Akismet on and be sure not to miss a message.', 'si-contact-form' ); ?>
			</div>
			<?php
		} else {
			echo '<input name="' . self::$form_option_name . '[akismet_send_anyway]" type="hidden" value="' . self::$form_options['akismet_send_anyway'] . '" />';
		}
		?>
		<br />
		<input name="<?php echo self::$form_option_name; ?>[akismet_disable]" id="si_contact_akismet_disable" type="checkbox" <?php if ( self::$form_options['akismet_disable'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="<?php echo self::$form_option_name; ?>[akismet_disable]"><?php _e( 'Turn off Akismet for this form.', 'si-contact-form' ); ?></label>

		</fieldset>
		<?php
	}	// end function akismet_settings_callback()



    static function domain_settings_callback() {
		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">


		<input name="<?php echo self::$form_option_name;?>[domain_protect]" id="fs_contact_domain_protect" type="checkbox" <?php if ( self::$form_options['domain_protect'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
		<label for="fs_contact_domain_protect"><?php _e( 'Enable Form Post security by requiring domain name match for', 'si-contact-form' ); ?>
		<?php
		$uri = parse_url( get_option( 'home' ) );
		$blogdomain = preg_replace( "/^www\./i", '', $uri['host'] );
		echo " $blogdomain ";
		?><?php _e( '(recommended).', 'si-contact-form' ); ?>
		</label>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_domain_protect_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_domain_protect_tip">
		<?php _e( 'Helps prevent automated spam bots posting from off-site forms. If you have multiple domains for your site, list additional names below.', 'si-contact-form' ) ?>
		</div>
		<br />
        <br />

		<label for="fs_contact_domain_protect_names"><?php _e( 'Additional allowed domain names(optional)', 'si-contact-form' ); ?>:</label><br />
		<textarea rows="6" cols="30" name="<?php echo self::$form_option_name;?>[domain_protect_names]" id="fs_contact_domain_protect_names"><?php echo esc_textarea( trim(self::$form_options['domain_protect_names']) ); ?></textarea>
		<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('si_contact_domain_protect_names_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
		<div class="fscf_tip" id="si_contact_domain_protect_names_tip">
		<?php _e( 'List additional domains for your site, one per line, without http:// and without www. Example: mywebsite.com', 'si-contact-form' ); ?><br /><br />
		</div>
		<br />

		</fieldset>
		<?php
	}	// end function akismet_settings_callback()




	static function meeting_settings_callback() {
		// don't use form tags, this is already inside a form
	  // prevent stuck condition with blank uid
	  if ( self::$form_options['vcita_approved'] == 'true' && empty( self::$form_options['vcita_uid'] ) )
	        self::$form_options['vcita_approved'] = 'false'; ?>

	  <input name="<?php echo self::$form_option_name;?>[vcita_approved]" id="si_contact_vcita_approved" type="hidden" value="<?php echo esc_attr( self::$form_options['vcita_approved']); ?>" />
	  <input name="<?php echo self::$form_option_name;?>[vcita_uid]" id="si_contact_vcita_uid" type="hidden" value="<?php echo esc_attr( self::$form_options['vcita_uid']); ?>" />
	  <input name="<?php echo self::$form_option_name;?>[vcita_email]" id="si_contact_vcita_email" type="hidden" value="<?php echo esc_attr( self::$form_options['vcita_email']); ?>" />

	  <div class="clear"></div>

	  <fieldset class="fscf_settings_group">
	    <div class="vcita_options_container">

	      <input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />

	      <?php if ( self::$form_options['vcita_approved'] != 'true') : ?>
	      <div class="scheduler_not_conected_note"><?php _e('Note: Your scheduler is not connected to your email yet - first name and last name are required','si-contact-form'); ?></div><br />
	      <?php endif; ?>

	       <p>
	         <?php _e('Enhance your Contact Form with Online Scheduling.','si-contact-form'); ?>
	         <br>
	         <?php _e('Let your clients book appointments and request services online.','si-contact-form'); ?> <a target="_blank" href="http://<?php echo self::$global_options['vcita_site'] ?>/integrations/fast_secure/how_it_works"><?php _e('Watch how it works','si-contact-form'); ?></a>
	       </p>
	       <b><?php _e('Scheduling requests should be sent to:','si-contact-form'); ?></b></br>
	           <?php if ( self::$form_options['vcita_approved'] == 'true') : ?>

	             <?php
	              // For users who registered through FSCF we send an auth_token param along with the link
	              // So the user won't see a login screen when he still does not have a password
	              $auth_token_param = isset( self::$form_options['auth_token'] ) ? '&auth_token=' . self::$form_options['auth_token'] : ''; ?>

	             <?php FSCF_Process::vcita_disable_init_msg(self::$form_options, self::$global_options) ?>
	             <span style="vertical-align: middle;"><?php echo esc_attr(self::$form_options['vcita_email']); ?></span>
	             <a onclick="toggleVisibility('change_account_box');" class="vcita-help"><?php _e('Change/Add Email Address','si-contact-form'); ?></a>
	             <div id="change_account_box" style="display:none;margin-top:6px;">
	               <input style='display:none;' id='vcita_change_email_action' type='submit' name='vcita_change_email_action'/>
	               <?php _e('To change/add email address go to','si-contact-form'); ?>
	               <a href='http://<?php echo self::$global_options['vcita_site'] ?>/settings?section=profile&email=<?php echo self::$form_options['vcita_email'] ?>' target='_blank'><?php _e('Profile Settings','si-contact-form'); ?></a>
	               <div style="margin-top:8px;"><?php _e('If you changed your email address please update the new address below so it will be reflected in the plugin','si-contact-form'); ?></div>
	               <div style="margin-top:3px;">
	                 New email
	                 <input name="<?php echo self::$form_option_name;?>[vcita_changed_email]" id="si_contact_vcita_changed_email" type='text'/>
	                 <a title="<?php _e('Change Email', 'si-contact-form'); ?>" target="_blank" onclick="document.getElementById('vcita_change_email_action').click();return false;" class="vcita-help button-primary no-save-changes"><?php _e('Update Email', 'si-contact-form'); ?></a>
	                 <a onclick="toggleVisibility('change_account_box');" class="vcita-help">Cancel</a>
	               </div>
	             </div>
	             <input style='display:none;' id='vcita_disconnect_button' type='submit' name='vcita_disconnect'/>
	           <?php else : ?>

	             <label class="vcita-btn-label" for="<?php echo self::$form_option_name;?>[vcita_email_new]"><?php _e('Email:', 'si-contact-form') ?></label>
	             <input name="<?php echo self::$form_option_name;?>[vcita_email_new]" id="si_contact_vcita_email_new" type="text" value="<?php echo esc_attr(self::$form_options['vcita_email_new']); ?>"  />
	             &nbsp;
	             <a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('contact_vcita_email_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a>
	             <div class="fscf_tip" id="contact_vcita_email_tip" >
	               <?php _e('Your email and name will only be used to send you meeting requests and additional communication from vCita, and will not be shared with your clients or third party.', 'si-contact-form'); ?>
	             </div>
	             <div style="margin-top:5px;">
	               <label class="vcita-btn-label" for="<?php echo self::$form_option_name;?>[vcita_first_name]"><?php _e('First Name:', 'si-contact-form') ?></label>
	               <input name="<?php echo self::$form_option_name;?>[vcita_first_name]" id="si_contact_vcita_first_name" type="text" value="<?php echo esc_attr(self::$form_options['vcita_first_name']); ?>"  />
	             </div>
	             <div style="margin-top:5px;">
	               <label class="vcita-btn-label" for="<?php echo self::$form_option_name;?>[vcita_last_name]"><?php _e('Last Name:', 'si-contact-form') ?></label>
	               <input name="<?php echo self::$form_option_name;?>[vcita_last_name]" id="si_contact_vcita_last_name" type="text" value="<?php echo esc_attr(self::$form_options['vcita_last_name']); ?>"  />
	             </div>
	           <?php endif ?>

	         <br /><br />
	         <b><?php _e('Scheduling Button Settings','si-contact-form'); ?></b>
	         <div class="vcita_inner_box">
	           <p>
	           <?php if ( self::$form_options['vcita_approved'] == 'true') : ?>
	            <a href='http://<?php echo self::$global_options['vcita_site'] ?>/settings?section=services&email=<?php echo self::$form_options['vcita_email'] ?><?php echo $auth_token_param; ?>' target='_blank' class="button-primary"><?php _e('Set Your scheduling Options','si-contact-form'); ?></a>
	            <?php else: ?>
	            <a class="button-secondary button-disabled" disabled><?php _e('Set Your scheduling Options','si-contact-form'); ?></a>
	           <?php endif; ?>
	           </p>
	           <br />
	           <input name="<?php echo self::$form_option_name;?>[vcita_scheduling_button]" type="checkbox" class="vcita-chkbox" id="si_contact_vcita_scheduling_button" <?php if ( self::$form_options['vcita_scheduling_button'] == 'true' ) echo 'checked="checked"'; ?> value="true" />
	           <label class="vcita-label" for="si_contact_vcita_scheduling_button"><?php _e('Add a scheduling button to your form', 'si-contact-form') ?></label>
	            &nbsp;<a style="cursor:pointer;" title="<?php esc_attr_e( 'Click for Help!', 'si-contact-form' ); ?>" onclick="toggleVisibility('scheduling_button_tip');"><?php _e( 'help', 'si-contact-form' ); ?></a><br />

	           <div class="scheduling_button_box">
	             <span class="fscf_tip" style="display:none;" id="scheduling_button_tip">"<?php _e('Schedule an appointment" button will be added next to your contact form','si-contact-form'); ?><br /></span>
	             <br /><label for="<?php echo self::$form_option_name;?>[vcita_scheduling_button_label]"><?php _e('Button Label:', 'si-contact-form'); ?></label>
	             <input style="width:189px;" name="<?php echo self::$form_option_name;?>[vcita_scheduling_button_label]" id="si_contact_vcita_scheduling_button_label" type="text" value="<?php echo esc_attr(self::$form_options['vcita_scheduling_button_label']); ?>" /><br />
	             <br /><?php _e('Button CSS can be edited in the Styles tab.','si-contact-form'); ?>
	             <br /><br />
	           </div>
	           <br />
	           <input style='display:none;' id='vcita_disable_button' type='submit' name='vcita_disable'/>
	           <br />
	         </div>

	      <?php if ( self::$form_options['vcita_approved'] == 'true' )  : ?>
	        <div class="privacy_box">
	          <a title="<?php _e('Change Account', 'si-contact-form'); ?>" target="_blank" onclick="confirmChangeAccount()"><?php _e('Change Account', 'si-contact-form'); ?></a>
	          <a title="<?php _e('More about vCita', 'si-contact-form'); ?>" target="_blank" href="http://<?php echo self::$global_options['vcita_site'] ?>/integrations/fast_secure/more_from_vcita"><?php _e('More about vCita', 'si-contact-form'); ?></a>
	          <a title="<?php _e('Help/Faq', 'si-contact-form'); ?>" href="https://support.vcita.com/entries/96236698-Add-Appointment-Booking-and-Online-Scheduling-Button-to-Fast-Secure-Contact-Form" target='_blank'><?php _e('Help/Faq', 'si-contact-form'); ?></a>
	        </div>
	      <?php else : ?>
	        <br/><br/>
	        <div class="privacy_box">
	          <a title="<?php _e('Privacy Policy', 'si-contact-form'); ?>" target="_blank" href="http://<?php echo self::$global_options['vcita_site'] ?>/about/privacy_policy"><?php _e('Privacy Policy', 'si-contact-form'); ?></a>
	           <a title="<?php _e('Click for Help!', 'si-contact-form'); ?>" href="https://support.vcita.com/entries/96236698-Add-Appointment-Booking-and-Online-Scheduling-Button-to-Fast-Secure-Contact-Form" target="_blank"><?php _e('Help', 'si-contact-form'); ?></a>
	        </div>
	      <?php endif ?>


	  </div>
  </fieldset>
    <?php
  } // end function meeting_settings_callback()


	static function newsletter_settings_callback() {


		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">
<?php
  // Remotely fetch, cache, and display HTML ad for the Fast Secure Contact Form Newsletter plugin addon.
if (!function_exists('sicf_ctct_admin_form')) { // skip if the plugin is already installed and activated
        $kws_ad = self::kws_get_remote_ad();
        $kws_ad = str_replace( 'class="updated"', '', $kws_ad);
        echo $kws_ad;

} else {
   	    // hook for constant contact settings
	    do_action( 'fsctf_newsletter_tab' );
}
?>

		</fieldset>
		<?php
	}	// end function newsletter_settings_callback()


	
	static function tools_callback() {
//		echo "This is the backup/export settings section";
		$form_num = self::$current_form;


		?>
		<div class="clear"></div>
		<fieldset class="fscf_settings_group">

		<input type="hidden" id="tools-admin-url" value="<?php echo admin_url( "options-general.php?page=si-contact-form/si-contact-form.php&amp;fscf_form=$form_num&amp;fscf_tab=8" ); ?>" />
		<?php // Display a warning if the form has unsaved changes  ?>
			<div class="fsc-notice fscf-save-notice"><p>
			<?php _e( 'Warning: You have unsaved changes.  To avoid losing your changes, you should changes before using the tools on this page.', 'si-contact-form' ); ?>
				</p><p class="submit">
			<input id="submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes', 'si-contact-form'); ?>" onclick="document.pressed=this.value" name="submit" />
			</p>
			</div>

			<fieldset class="fscf_settings_group">
			<legend><strong><?php _e( 'Send a Test Email', 'si-contact-form' ); ?></strong></legend>
		<?php _e( 'If you are not receiving email from your form, try this test because it can display troubleshooting information.', 'si-contact-form' ); ?><br />
		<?php _e( 'There are settings you can use to try to fix email delivery problems, see this FAQ for help:', 'si-contact-form' ); ?>
		 <a href="http://www.fastsecurecontactform.com/email-does-not-send" target="_blank"><?php _e( 'FAQ', 'si-contact-form' ); ?></a><br />
		<?php _e( 'Type an email address here and then click Send Test to generate a test email.', 'si-contact-form' ); ?>
		<?php
		if ( !function_exists( 'mail' ) ) {
			echo '<div class="fsc-error">' . __( 'Warning: Your web host has the mail() function disabled. PHP cannot send email.', 'si-contact-form' );
			echo ' ' . __( 'Have them fix it. Or you can install a WordPress SMTP plugin and configure it to use SMTP.', 'si-contact-form' ) . '</div>' . "\n";
		}
		?>
		<br />
		<label for="si_contact_to"><?php _e( 'To:', 'si-contact-form' ); ?></label>
		<input type="text" name="si_contact_to" id="si_contact_to" value="" size="40" class="code" />
		<p style="padding:0px;" class="submit">
		<input type="submit" name="ctf_action" value="<?php _e( 'Send Test', 'si-contact-form' ); ?>" onclick="document.pressed=this.value" />
		</p>
		</fieldset>

		<br />

		<fieldset class="fscf_settings_group">
		<legend><strong><?php _e( 'Copy Settings', 'si-contact-form' ); ?></strong></legend>
		<?php _e( 'This tool can copy your contact form settings from this form number to any of your other forms.', 'si-contact-form' ); ?><br />
		<?php _e( 'Use to copy just the style settings, or all the settings from this form.', 'si-contact-form' ); ?><br />
		<?php _e( 'It is a good idea to backup all forms with the backup tool before you use this copy tool. Changes are permanent!', 'si-contact-form' ); ?><br />
			
		<label for="si_contact_copy_what"><?php echo __( 'What to copy:', 'si-contact-form' ); ?></label>
		<select id="si_contact_copy_what" name="si_contact_copy_what">
		<?php
		$copy_what_array = array(
			'all'	 => sprintf( __( 'Form %d - all settings', 'si-contact-form' ), self::$current_form ),
			'styles' => sprintf( __( 'Form %d - style settings', 'si-contact-form' ), self::$current_form ),
		);
		$selected = '';
		foreach ( $copy_what_array as $k => $v ) {
			if ( isset( $_POST['si_contact_copy_what'] ) && $_POST['si_contact_copy_what'] == "$k" )
				$selected = ' selected="selected"';
			echo '<option value="' .esc_attr($k) . '"' . $selected . '>' . esc_html($v) . '</option>' . "\n";
			$selected = '';
		}
		?>
		</select>
			
		<label for="si_contact_destination_form"><?php echo sprintf( __( 'Select a form to copy form %d settings to:', 'si-contact-form' ), self::$current_form ); ?></label>
		<select id="si_contact_destination_form" name="si_contact_destination_form">
		<?php
		echo '<option value="all">' . esc_html( __( 'All Forms', 'si-contact-form' ) ) . '</option>' . "\n";
		$selected = '';
		foreach ( self::$global_options['form_list'] as $k => $v ) {
			if ( isset( $_POST['si_contact_destination_form'] ) && $_POST['si_contact_destination_form'] == "$k" )
				$selected = ' selected="selected"';
            $this_form = (self::$current_form == $k) ? __('(this form) ', 'si-contact-form'): '';
			echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . $this_form . __('Form', 'si-contact-form'). ' '.($k) . ': ' . esc_html($v) . "</option>\n";
			$selected = '';
		}
		?>
		</select>
			
		<input type="hidden" name="si_contact_this_form" id="si_contact_this_form" value="<?php echo self::$current_form ?>"  />
		<p style="padding:0px;" class="submit">

		<input type="submit" name="ctf_action" onclick="document.pressed=this.value" value="<?php esc_attr_e( 'Copy Settings', 'si-contact-form' ); ?>" />
		</p>
		</fieldset>

		<br />

		<fieldset class="fscf_settings_group">

		<legend><strong><?php _e( 'Backup Settings', 'si-contact-form' ); ?></strong></legend>
		<?php _e( 'This tool can save a backup of your contact form settings.', 'si-contact-form' ); ?><br />
		<?php _e( 'Use to transfer one, or all, of your forms from one site to another. Or just make a backup to save.', 'si-contact-form' ); ?><br />
		<label for="si_contact_backup_type"><?php _e( 'Select a form to backup:', 'si-contact-form' ); ?></label>
		<select id="si_contact_backup_type" name="si_contact_backup_type">
		<?php
		echo '<option value="all">' . esc_html( __( 'All Forms', 'si-contact-form' ) ) . '</option>' . "\n";
		$selected = '';
		foreach ( self::$global_options['form_list'] as $k => $v ) {
			if ( isset( $_POST['si_contact_backup_type'] ) && $_POST['si_contact_backup_type'] == "$k" )
				$selected = ' selected="selected"';
            $this_form = (self::$current_form == $k) ? __('(this form) ', 'si-contact-form'): '';
			echo '<option value="' . esc_attr($k) . '"' . $selected . '>' . $this_form . __('Form', 'si-contact-form'). ' '.($k) . ': ' . esc_html($v) . "</option>\n";
			$selected = '';
		}

		?>
		</select>

		<p style="padding:0px;" class="submit">
		<input type="submit" name="ctf_action" value="<?php esc_attr_e( 'Backup Settings', 'si-contact-form' ); ?>" onclick="document.pressed=this.value" />
		</p>
		</fieldset>

		<br />

		<fieldset class="fscf_settings_group">
			
		<legend><strong><?php _e( 'Restore Settings', 'si-contact-form' ); ?></strong></legend>
		<?php _e( 'This tool can restore a backup of your contact form settings. If you have previously made a backup, you can restore one or all your forms.', 'si-contact-form' ); ?><br />
		<?php _e( 'It is a good idea to backup all forms with the backup tool before you restore any. Changes are permanent!', 'si-contact-form' ); ?><br />
		<label for="si_contact_restore_backup_type"><?php _e( 'Select a form to restore:', 'si-contact-form' ); ?></label>
			
		<select id="si_contact_restore_backup_type" name="si_contact_restore_type">
		<?php
		echo '<option value="all">' . esc_html( __( 'All Forms', 'si-contact-form' ) ) . '</option>' . "\n";
		$selected = '';
		foreach ( self::$global_options['form_list'] as $k => $v ) {
			if ( isset( $_POST['si_contact_backup_type'] ) && $_POST['si_contact_backup_type'] == "$k" )
				$selected = ' selected="selected"';

            $this_form = (self::$current_form == $k) ? __('(this form) ', 'si-contact-form'): '';
			echo '<option value="' . esc_attr($k) . '"' . $selected . '>' .$this_form .__('Form', 'si-contact-form'). ' '.($k) . ': ' . esc_html($v) . "</option>\n";

			$selected = '';
		}
		?>
		</select>
		<br />
			
		<label for="si_contact_backup_file"><?php _e( 'Upload Backup File:', 'si-contact-form' ); ?></label>
		<input style="text-align:left; margin:0;" type="file" id="si_contact_backup_file" name="si_contact_backup_file" value=""  size="20" />

		<p style="padding:0px;" class="submit">
		<input type="submit" name="ctf_action" onclick="document.pressed=this.value" value="<?php esc_attr_e( 'Restore Settings', 'si-contact-form' ); ?>" />
		</p>
			
		</fieldset>

		<br />
		
		<fieldset class="fscf_settings_group">
		<legend><strong><?php _e( 'Reset and Delete', 'si-contact-form' ); ?></strong></legend>
		<strong><?php _e('These options will permanantly affect all tabs on this form. (Form 1 cannot be deleted).', 'si-contact-form' ); ?></strong>
		<br /><br/>

		<input type="button" name="reset" value="<?php esc_attr_e( 'Reset Form', 'si-contact-form' ); ?>" onclick="fscf_reset_form()" />
		<?php _e('Reset this form to the default values.', 'si-contact-form' ); ?>
		<br/><br />

		<?php if ( self::$current_form <> 1 ) { ?>
			<input type="button" name="delete" value="<?php esc_attr_e( 'Delete Form', 'si-contact-form' ); ?>" onclick="fscf_delete_form(<?php echo self::$current_form; ?>)" />
			<?php _e('Delete this form permanently.', 'si-contact-form' ); ?>
			<br/><br/>
		<?php } ?>

        <input type="button" name="reset_all_styles" value="<?php esc_attr_e( 'Reset Styles on all forms', 'si-contact-form' ); ?>" onclick="fscf_reset_all_styles()" />
		<?php _e('Reset default style settings on all forms.', 'si-contact-form' ); ?>

        <br /><br />

        <?php
        $old_global_options = get_option('si_contact_form_gb');
			  if ($old_global_options) {
        ?>
        <input type="button" name="import_old_forms" value="<?php esc_attr_e( 'Import forms from 3.xx version', 'si-contact-form' ); ?>" onclick="fscf_import_old_forms()" />
		<?php _e('Note: this button will replace the current 4.xx settings and forms with your old version 3.xx ones!', 'si-contact-form' ); ?>

        <?php } else {?>

          <?php _e('3.xx version forms are not available for import, the import button is disabled.', 'si-contact-form' ); ?>

        <?php } ?>

		</fieldset>
		</fieldset>
        </form>
		<?php

			// new field added message javascript focus
			if ( !empty(self::$new_field_added)  ) {
            ?>
                <script type="text/javascript" language="JavaScript">
                  document.forms['fscf-optionsform'].elements['fs_contact_field<?php echo self::$new_field_key; ?>_label'].focus();
                </script>
            <?php
			}

	} // end function backup_settings_callback()

	/* ------------------------------------------------------------------------ * 
	 * Display support  functions
	 * ------------------------------------------------------------------------ */
	
	static function vcita_update_details($text) {
      
    self::$global_options = FSCF_Util::get_global_options();
    if(!isset($text['vcita_scheduling_button']))
      $text['vcita_scheduling_button'] = 'false';

    if (isset($_POST['vcita_disconnect'])) {
      $text = FSCF_Process::vcita_disconnect_form($text);
    } else if (isset($_POST['vcita_disable'])) {
    	$text['vcita_scheduling_button'] = 'false';
    	self::$global_options['vcita_dismiss'] = 'true';
    	self::$global_options['vcita_show_disable_msg'] = 'true';
    	update_option( 'fs_contact_global', self::$global_options );
    } else if (isset($_POST['vcita_change_email_action']) && $text['vcita_changed_email'] != "") {
      $text['vcita_email'] = trim($text['vcita_changed_email']);
    } else if(self::$form_options['vcita_approved'] == 'false' && isset($text['vcita_email_new']) && trim($text['vcita_email_new']) != "" && isset($text['vcita_first_name']) && trim($text['vcita_first_name']) != "" && isset($text['vcita_last_name']) && trim($text['vcita_last_name']) != ""){
        $text['vcita_approved'] = 'true';
        $text['vcita_email'] = trim($text['vcita_email_new']);

        $text = FSCF_Process::vcita_create_or_validate_user($text, self::$global_options);
        if($text['vcita_confirmed'] != 'true'){
    	    self::$global_options['vcita_initialized'] = 'true';
    	    update_option( 'fs_contact_global', self::$global_options );
        }
    }
    
    return ( $text );
  } // end function vcita_update_details()


	/* ------------------------------------------------------------------------ *
	 * Validate and Default setup functions
	 * ------------------------------------------------------------------------ */


	static function validate( $text ) {
		// Wordpress will call this function when the settings form is submitted
		// $text contains the POST options array from the form
		global $fscf_special_slugs;		// List of reserved slug names

		self::$global_options = FSCF_Util::get_global_options();
		self::$form_defaults = FSCF_Util::set_defaults();
		if ( ! isset(self::$form_options)) self::$form_options = FSCF_Util::get_form_options ( self::$current_form, false );

		// See if 'donated' status has changed.  If so, update global settings.
		// if the POST variable fs_contact_donated exists, then the checkbox was checked
		$donated =  ( isset($_POST['fs_contact_donated']) ) ? 'true' : 'false';
		if ( $donated <> self::$global_options['donated'] ) {
			self::$global_options['donated'] = $donated;
		}

		if ( isset($_POST['fs_dismiss_import_msg']) ) {
			self::$global_options['import_msg'] = false;
		}
		
		// Update global options array based on value of enable_php_sessions
        // if the POST variable enable_php_session, then the checkbox was checked
		$php_sessions = ( isset($_POST['enable_php_sessions']) ) ? 'true' : 'false';
		if ( $php_sessions <> self::$global_options['enable_php_sessions'] ) {
			self::$global_options['enable_php_sessions'] = $php_sessions;
		}

        // vcita_auto_install
        if ( ! empty($_POST['vcita_auto_install']) && ($_POST['vcita_auto_install'] == 'true' || $_POST['vcita_auto_install'] == 'false'))
          self::$global_options['vcita_auto_install'] = $_POST['vcita_auto_install'];

        // vcita_dismiss
        if ( ! empty($_POST['vcita_dismiss']) && ($_POST['vcita_dismiss'] == 'true' || $_POST['vcita_dismiss'] == 'false'))
          self::$global_options['vcita_dismiss'] = $_POST['vcita_dismiss'];

        // recaptcha keys are a global setting so all forms will have it filled in
        if ( ! empty($_POST['recaptcha_public_key']))
           self::$global_options['recaptcha_public_key'] = sanitize_text_field( $_POST['recaptcha_public_key'] );

        if ( ! empty($_POST['recaptcha_secret_key']))
           self::$global_options['recaptcha_secret_key'] = sanitize_text_field( $_POST['recaptcha_secret_key'] );

        update_option( 'fs_contact_global', self::$global_options );

		// Trim trailing spaces
		FSCF_Util::trim_array($text);

		// Special processing for certain form fields
		if ( '' == $text['email_to'] )
			$text['email_to'] = self::$form_defaults['email_to']; // use default if empty
		$text['redirect_seconds'] = ( is_numeric( $text['redirect_seconds'] ) && $text['redirect_seconds'] < 61 ) ? absint( $text['redirect_seconds'] ) : self::$form_defaults['redirect_seconds'];
		if ( '' == $text['redirect_url'] )
			$text['redirect_url'] = self::$form_defaults['redirect_url']; // use default if empty
		if ( ! preg_match( '/^[0-6]?$/', $text['cal_start_day'] ) )
			$text['cal_start_day'] = self::$form_defaults['cal_start_day'];
		$text['attach_types'] = str_replace( '.', '', $text['attach_types'] );
		if ( '' == $text['attach_size'] || ! preg_match( '/^([[0-9.]+)([kKmM]?[bB])?$/', $text['attach_size'] ) )
			$text['attach_size'] = self::$form_defaults['attach_size'];
		if ( '' == $text['auto_respond_from_name'] )
			$text['auto_respond_from_name'] = self::$form_defaults['auto_respond_from_name']; // use default if empty
		if ( '' == $text['auto_respond_from_email'] || !FSCF_Util::validate_email( $text['auto_respond_from_email'] ) )
			$text['auto_respond_from_email'] = self::$form_defaults['auto_respond_from_email']; // use default if empty
		if ( $text['auto_respond_reply_to'] == '' || !FSCF_Util::validate_email( $text['auto_respond_reply_to'] ) )
			$text['auto_respond_reply_to'] = self::$form_defaults['auto_respond_reply_to']; // use default if empty
	   //	$text['field_size'] = ( is_numeric( $text['field_size'] ) && $text['field_size'] > 14 ) ? absint( $text['field_size'] ) : self::$form_defaults['field_size']; // use default if empty
		//$text['captcha_field_size'] = ( is_numeric( $text['captcha_field_size'] ) && $text['captcha_field_size'] > 4 ) ? absint( $text['captcha_field_size'] ) : self::$form_defaults['captcha_field_size'];
		//$text['text_cols'] = absint( $text['text_cols'] );
		//$text['text_rows'] = absint( $text['text_rows'] );

        if( !empty($text['domain_protect_names']) )
                   $text['domain_protect_names'] = self::clean_textarea($text['domain_protect_names']);

        if( !empty($text['email_to']) )
                   $text['email_to'] = self::clean_textarea($text['email_to']);

		
		// Use default style settings if styles are empty
		if ( ! isset(self::$style_defaults) ) self::$style_defaults = FSCF_Util::set_style_defaults();
		foreach ( self::$style_defaults as $key => $val ) {
			//if ( '' == $text[$key] ) // caused error on import settings from some older versions
             if ( !isset($text[$key]) || empty($text[$key]) )
				$text[$key] = $val;
		}

		// Do we need to reset all styles top this form?
		if ( isset( $_POST['fscf_reset_styles'] ) ) {
			// reset styles feature
			$text = FSCF_Action::copy_styles( self::$form_defaults, $text );
		}
		
		if ( isset( $_POST['fscf_reset_styles_top'] ) ) {
			$style_resets_arr = array(
			   //	'border_enable'			 => 'false',
				
				// reset labels on top
				
                // Alignment DIVs
			    'form_style'           => 'width:99%; max-width:555px;',   // Form DIV, how wide is the form DIV
                'left_box_style'       => 'float:left; width:55%; max-width:270px;',   // left box DIV, container for vcita
                'right_box_style'      => 'float:left; width:235px;',   // right box DIV, container for vcita
                'clear_style'          => 'clear:both;',   // clear both
		        'field_left_style'     => 'clear:left; float:left; width:99%; max-width:550px; margin-right:10px;',   // field left
                'field_prefollow_style' => 'clear:left; float:left; width:99%; max-width:250px; margin-right:10px;',   // field pre follow
		        'field_follow_style'   => 'float:left; padding-left:10px; width:99%; max-width:250px;',   // field follow
			    'title_style'          => 'text-align:left; padding-top:5px;', // Input labels alignment DIV
			    'field_div_style'      => 'text-align:left;',   // Input fields alignment DIV
			    'captcha_div_style_sm' => 'width:175px; height:50px; padding-top:2px;',  // Small CAPTCHA DIV
			    'captcha_div_style_m'  => 'width:250px; height:65px; padding-top:2px;',  // Large CAPTCHA DIV
			    'captcha_image_style'  => 'border-style:none; margin:0; padding:0px; padding-right:5px; float:left;', // CAPTCHA image alignment
			    'captcha_reload_image_style' => 'border-style:none; margin:0; padding:0px; vertical-align:bottom;', // CAPTCHA reload image alignment
			    'submit_div_style'     => 'text-align:left; clear:both; padding-top:15px;', // Submit DIV
                'border_style'         => 'border:1px solid black; width:99%; max-width:550px; padding:10px;', // style of the fieldset box (if enabled)
				
			);

			// reset left styles feature
			foreach ( $style_resets_arr as $key => $val ) {
				$text[$key] = $val;
			}
		}	// end reset styles top		

		if ( isset( $_POST['fscf_reset_styles_left'] ) ) {
			$style_resets_arr = array(
				//'border_enable'			 => 'false',
				
				// reset labels on left
				
				// Alignment DIVs
				'form_style'			 => 'width:655px;', // how wide is the form DIV
                'left_box_style'         => 'float:left; width:450px;',   // left box DIV, container for vcita
                'right_box_style'        => 'float:left; width:235px;',   // right box DIV, container for vcita
                'clear_style'            => 'clear:both;',   // clear both
		        'field_left_style'       => 'clear:left; float:left; margin-right:10px;',   // field left
                'field_prefollow_style'  => 'clear:left; float:left; margin-right:10px;',   // field pre follow
		        'field_follow_style'     => 'float:left; padding-left:10px;',   // field follow
				'title_style'			 => 'width:138px; float:left; clear:left; text-align:right; padding-top:8px; padding-right:10px;', // Input labels alignment DIV
				'field_div_style'		 => 'text-align:left; float:left; padding-top:10px;', // Input fields alignment DIV 
				'captcha_div_style_sm'	 => 'float:left; width:162px; height:50px; padding-top:5px;', // Small CAPTCHA DIV 
				'captcha_div_style_m'	 => 'float:left; width:362px; height:65px; padding-top:5px;', // Large CAPTCHA DIV 
			    'captcha_image_style'    => 'border-style:none; margin:0; padding:0px; padding-right:5px; float:left;', // CAPTCHA image alignment
			    'captcha_reload_image_style' => 'border-style:none; margin:0; padding:0px; vertical-align:bottom;', // CAPTCHA reload image alignment				
				'submit_div_style'		 => 'padding-left:146px; float:left; clear:left; text-align:left; padding-top:15px;', // Submit DIV
			    'border_style'           => 'border:1px solid black; width:99%; max-width:450px; padding:10px;', // style of the fieldset box (if enabled)
				
			);

			// reset left styles feature
			foreach ( $style_resets_arr as $key => $val ) {
				$text[$key] = $val;
			}
		}	// end reset styles left

		if ( isset( $_POST['fscf_reset_styles_labels'] ) ) {
			$style_resets_arr = array(
				'border_enable'			 => 'false',
				
				// reset labels only
				
				// Style of labels, fields and text
				'required_style'		 => 'text-align:left;', // required field indicator
                'required_text_style'    => 'text-align:left;',   // required field text 				
			    'hint_style'             => 'font-size:x-small; font-weight:normal;', // small text hints like please enter your email again 
				'error_style'			 => 'text-align:left; color:red;', // Input validation messages
                'redirect_style'         => 'text-align:left;', // Redirecting message
			   	'fieldset_style'         => 'border:1px solid black; width:97%; max-width:500px; padding:10px;', // style of the fieldset box (for fields)
                'label_style'			 => 'display:inline;', // Field labels 
  			    'option_label_style'     => 'display:inline;', // Options labels
				
				'field_style'			 => 'text-align:left; margin:0; width:99%; max-width:250px;', // Input text fields  (out of place here?)  
				'captcha_input_style'	 => 'text-align:left; margin:0; width:50px;', // CAPTCHA input field
 			    'textarea_style'         => 'text-align:left; margin:0; width:99%; max-width:250px; height:120px;', // Input Textarea 
				'select_style'			 => 'text-align:left;',	// Input Select
 			    'checkbox_style'         => 'width:22px; height:32px;', // Input checkbox
                'radio_style'            => 'width:22px; height:32px;', // Input radio
                'placeholder_style'      => 'opacity:0.6; color:#333333;', // placeholder style

				'button_style'			 => 'cursor:pointer; margin:0;', // Submit button 
				'reset_style'			 => 'cursor:pointer; margin:0;', // Reset button 
				'vcita_button_style'     => 'text-decoration:none; display:block; text-align:center; background:linear-gradient(to bottom, #ed6a31 0%, #e55627 100%); color:#fff !important; padding:10px;', // vCita button
                'vcita_div_button_style' => 'border-left:1px dashed #ccc; margin-top:25px; height:50px; padding:8px 20px;', // vCita button div box
				'powered_by_style'		 => 'font-size:x-small; font-weight:normal; padding-top:5px; text-align:center;', // the "powered by" link


			);

			// reset label styles feature
			foreach ( $style_resets_arr as $key => $val ) {
				$text[$key] = $val;
			}
		}	// end reset styles left
		
		// List of all checkbox settings names (except for checkboxes in fields)
		$checkboxes = array ( 'email_from_enforced', 'preserve_space_enable', 'double_email',
			 'name_case_enable' , 'sender_info_enable', 'domain_protect', 'email_check_dns', 'email_check_easy',
			 'email_html', 'akismet_disable', 'captcha_enable', 'recaptcha_enable', 'recaptcha_dark', 'akismet_send_anyway',
			 'captcha_small','email_hide_empty', 'email_keep_attachments','print_form_enable',
			 'captcha_perm', 'honeypot_enable', 'redirect_enable', 'redirect_query', 'redirect_email_off',
			 'silent_email_off', 'export_email_off', 'ex_fields_after_msg', 'email_inline_label',
			 'textarea_html_allow', 'enable_areyousure', 'enable_submit_oneclick', 'auto_respond_enable', 'auto_respond_html',
			 'req_field_indicator_enable', 'req_field_label_enable', 'border_enable', 'anchor_enable',
			 'aria_required', 'auto_fill_enable', 'enable_reset', 'enable_credit_link'
		);

		// Set missing checkbox values to 'false' because these boxes were unchecked
		// html form checkboxes do not return anything in POST if unchecked
//		$text = array_merge($unchecked, $text);
		foreach ( $checkboxes as $checkbox ) {
			if ( ! isset($text[$checkbox]) ) $text[$checkbox] = 'false';
		}

		// Sanitize settings fields
		$html_fields = array( 'welcome', 'after_form_note', 'req_field_indicator', 'text_message_sent', 'success_page_html' );
		if ( 'true' == $text['auto_respond_html'] ) $html_fields[] = 'auto_respond_message';
        if ( defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML ) // do not allow unfiltered HTML
            $html_fields = array( );
		foreach ( $text as $key => $value ) {
			if ( is_string($value) ) {
				if ( in_array( $key, $html_fields ) ) {
                    $text[$key] = $value;
				}
				else $text[$key] = strip_tags( $value );
			}
		}
		
		// Process contact form fields
		$slug_list = $fscf_special_slugs;
		// The $special_slugs list is also used in FSCF_Display::get_query_parms()
//		$special_slugs = array( 'f_name', 'm_name', 'mi_name', 'l_name', 'email2', 'mailto_id', 'subject_id' );
		$select_type_fields = array(
			'checkbox-multiple',
			'select',
			'select-multiple',
			'radio'
		);

        // none of the field slugs can be the same as a post type rewrite_slug
        // or you will get "page not found" when posting the form with that field filled in
        self::get_post_types_slugs();
        $slug_list = array();
        if( isset(self::$form_options) && (!empty(self::$form_options['fields']))  ) {
           foreach ( self::$form_options['fields'] as $key => $field ) {
             $slug_list[] = $field['slug'];
           }
        }
        $bad_slugs = array();
        foreach (self::$post_types_slugs as $key => $slug) {
            if ( in_array( strtolower( $slug ), $slug_list ) )
               $bad_slugs[] = $slug;
        }

		foreach ( $text['fields'] as $key => $field ) {
			if ( isset( $field['delete'] ) && "true" == $field['delete'] ) {
				// Delete the field
				unset( $text['fields'][$key] );

			} else {
				unset( $text['fields']['$key']['delete'] );  // Don't need to keep this
				// Add 'false' to any missing checkboxes for fields
				if ( ! isset($field['req'])) $text['fields'][$key]['req'] = 'false';
				if ( ! isset($field['disable'])) $text['fields'][$key]['disable'] = 'false';
				if ( ! isset($field['follow'])) $text['fields'][$key]['follow'] = 'false';
				if ( ! isset($field['inline'])) $text['fields'][$key]['inline'] = 'false';
                if ( ! isset($field['hide_label'])) $text['fields'][$key]['hide_label'] = 'false';
                if ( ! isset($field['placeholder'])) $text['fields'][$key]['placeholder'] = 'false';

				// Sanitize html in form field settings
				foreach ( $field as $k => $v ) {
					if ( is_string($v) ) {
						//if ( 'notes' == $k || 'notes_after' == $k ) $text['fields'][$key][$k] = wp_filter_kses( $v );  //strips too much
                        if ( 'notes' == $k || 'notes_after' == $k ) $text['fields'][$key][$k] = $v;  // allow html
						else $text['fields'][$key][$k] = strip_tags( $v );  // strip html tags
					}
				}

				// Make sure the field name is not blank
				if ( empty($field['label'])) {
					$text['fields'][$key]['label'] = sprintf( __( 'Field %s', 'si-contact-form' ), $key );
					$temp = sprintf( __( 'Field label cannot be blank.  Label set to "Field  %s". To delete a field, use the delete option.', 'si-contact-form' ), $key );
					add_settings_error('fscf_field_settings', 'missing-label', $temp);
				}

				// Sanitize the slug
				$slug_changed = false;
                if ( !empty($field['slug']) && in_array( strtolower( $field['slug'] ), $bad_slugs ) )
                  $slug_changed = true;


				if ( empty($field['slug']) ) {
					// no slug, so make one from the label
					// the sanitize title function encodes UTF-8 characters, so we need to undo that

                    // this line croaked on some chinese characters
				    //$field['slug'] = substr( urldecode(self::sanitize_slug_with_dashes(remove_accents($field['label']))), 0, FSCF_MAX_SLUG_LEN );

                    $field['slug'] = remove_accents($field['label']);
                    $field['slug'] = preg_replace('~([^a-zA-Z\d_ .-])~', '', $field['slug']);
                    $field['slug'] = substr( urldecode(self::sanitize_slug_with_dashes($field['slug'])), 0, FSCF_MAX_SLUG_LEN );
                    if ($field['slug'] == '')
                       $field['slug'] = 'na';
					if ( '-' == substr( $field['slug'], strlen($field['slug'])-1, 1) )
							$field['slug'] = substr( $field['slug'], 0, strlen($field['slug'])-1);
					$slug_changed = true;
				} else if ( empty(self::$form_options['fields'][$key]['slug']) || ( $field['slug'] != self::$form_options['fields'][$key]['slug'] ) ) {
					// The slug has changed, so sanitize it
                    
                    // this line croaked on some chinese characters
				    //$field['slug'] = substr( urldecode(self::sanitize_slug_with_dashes(remove_accents($field['slug']))), 0, FSCF_MAX_SLUG_LEN );

                    $field['slug'] = remove_accents($field['slug']);
                    $field['slug'] = preg_replace('~([^a-zA-Z\d_ .-])~', '', $field['slug']);
                    $field['slug'] = substr( urldecode(self::sanitize_slug_with_dashes($field['slug'])), 0, FSCF_MAX_SLUG_LEN );
                    if ($field['slug'] == '')
                       $field['slug'] = 'na';
					$slug_changed = true;
				}

				// Make sure the slug is unique
				if ( $slug_changed ) {
					$text['fields'][$key]['slug'] = self::check_slug( $field['slug'], $slug_list );
				}
			}
            if( isset( $text['fields'][$key]['slug'] ) )
			  $slug_list[] = $text['fields'][$key]['slug'];

			// If a select type field, make sure the select options list is not empty
			if ( in_array( $field['type'], $select_type_fields ) ) {
                 // remove blank lines and trim options
               if( !empty($text['fields'][$key]['options']) )
                   $text['fields'][$key]['options'] = self::clean_textarea($text['fields'][$key]['options']);

               if (empty($field['options'])) {
				  $temp = sprintf( __( 'Select options are required for the %s field.', 'si-contact-form' ), $field['label'] );
				  add_settings_error('fscf_field_settings', 'missing-options', $temp);
               }
			}

			// If date type field, check format of default (if any)
			if ( 'date' == $field['type'] && '' != $field['default'] ) {
				if ($field['default'] != '[today]' && !FSCF_Process::validate_date( $field['default'], self::$current_form ) ) {
					$cal_date_array = array(
						'mm/dd/yyyy' => esc_html( __( 'mm/dd/yyyy', 'si-contact-form' ) ),
						'dd/mm/yyyy' => esc_html( __( 'dd/mm/yyyy', 'si-contact-form' ) ),
						'mm-dd-yyyy' => esc_html( __( 'mm-dd-yyyy', 'si-contact-form' ) ),
						'dd-mm-yyyy' => esc_html( __( 'dd-mm-yyyy', 'si-contact-form' ) ),
						'mm.dd.yyyy' => esc_html( __( 'mm.dd.yyyy', 'si-contact-form' ) ),
						'dd.mm.yyyy' => esc_html( __( 'dd.mm.yyyy', 'si-contact-form' ) ),
						'yyyy/mm/dd' => esc_html( __( 'yyyy/mm/dd', 'si-contact-form' ) ),
						'yyyy-mm-dd' => esc_html( __( 'yyyy-mm-dd', 'si-contact-form' ) ),
						'yyyy.mm.dd' => esc_html( __( 'yyyy.mm.dd', 'si-contact-form' ) ),
					);
					$temp = sprintf( __( 'Default date for %s is not correctly formatted. Format should be %s.', 'si-contact-form' ), $field['label'], $cal_date_array[$text['date_format']] );
					add_settings_error( 'fscf_field_settings', 'invalid-date', $temp );
				}
			}

		}	// end foreach (Process fields)

		$text = self::vcita_update_details($text);
		
		FSCF_Util::unencode_html($text);

		// Update the query args if necessary
		if ( ! isset($_POST['ctf_action'])&& isset( $_REQUEST['_wp_http_referer'] ) ) {
			// Set the current tab in _wp_http_referer so that we go there after the save
			$wp_referer = esc_url_raw(remove_query_arg( 'fscf_tab', $_REQUEST['_wp_http_referer'] ));
			$wp_referer = esc_url_raw(add_query_arg( 'fscf_tab', $_POST['current_tab'], $wp_referer ));
			if ( isset( $text['akismet_check'] ) ) {
				// Request Akismet check on page reload
				$wp_referer = esc_url_raw(add_query_arg( 'akismet_check', 'true', $wp_referer ));
				unset ( $text['akismet_check'] );	// Don't save this in database
			} else {
				$wp_referer = esc_url_raw(remove_query_arg( 'akismet_check', $wp_referer ));
			}
			$_REQUEST['_wp_http_referer'] = $wp_referer;
		}
		return( $text );
	}	// end function validate($text);


    static function sanitize_slug_with_dashes( $title, $raw_title = '' ) {
          $title = strip_tags($title);
	      // Preserve escaped octets.
	      $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
          // Remove percent signs that are not part of an octet.
	      $title = str_replace('%', '', $title);
	      // Restore octets.
	      $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	      if (seems_utf8($title)) {
	            $title = utf8_uri_encode($title, 200);
	      }
	      $title = preg_replace('/&.+?;/', '', $title); // kill entities
          $title = str_replace('.', '-', $title);
	      $title = preg_replace('/[^%a-zA-Z0-9 _-]/', '', $title);
	      $title = preg_replace('/\s+/', '-', $title);
          $title = preg_replace('|-+|', '-', $title);
	      $title = trim($title, '-');

        return $title;
	}
    // function sanitize_title_with_dashes


    static function get_post_types_slugs() {

      // check for custom post types, returns the global static self::$post_types_slugs
      // none of the field slugs can be the same as a post type rewrite_slug
      // or you will get "page not found" when posting the form with that field filled in

      $pt_args = array('public' => true,'_builtin' => false);
      $post_types = get_post_types( $pt_args, 'objects' );

      if ( $post_types ) {
         foreach ( $post_types as $post_type ) {
              self::$post_types_slugs[] = ( isset( $post_type->rewrite_slug ) ) ? $post_type->rewrite_slug : $post_type->name;
         }
      }
   }

	static function check_slug($slug, $slug_list) {
		// Checks the slug, and adds a number if necessary to make it unique
		//   $slug -- the slug to be checked
		//   $slug_list -- a list of existing slugs
		// Returns the new slug

        $slug_list  = array_merge( self::$post_types_slugs, $slug_list );

		// Duplicates have a two digit number appended to the end to make them unique
		// XXX do I neeed any messages about changing the slug?
		$numb = preg_match( '/\d{2}$/', $slug, $match);

		while ( in_array( $slug, $slug_list ) ) {
			if ( $numb ) {
				$new_numb = sprintf("%02d", substr($slug,strlen($slug)-2,2) + 1);
				$slug = substr($slug, 0, strlen($slug)-2) . $new_numb;
			} else {
				$slug .= '01';
				$numb = 1;
			}
		} // end while
		
		return($slug);
	}

   	static function clean_textarea($data) {
        // cleans blank lines and trims gaps from textarea list inputs
		// Returns the new data
        $new_data = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $data);
        $data_array = explode("\n",$new_data);
        $new_data = '';
        foreach($data_array as $line) {
            $line = trim($line);
            if ($line != '')  // do not use !empty, or an option '0' is deleted
               $new_data .= "$line\n";
        }
        return trim($new_data);
	} // end function clean_textarea

	static function set_fld_array() {
		// Set up the list of available tags for email

		self::get_options();

		self::$av_fld_arr  = array();  // used to show available field tags this form
		self::$av_fld_subj_arr  = array();  // used to show available field tags for this form subject

		// Fields
		foreach ( self::$form_options['fields'] as $key => $field ) {
			switch ($field['standard']) {
				case FSCF_NAME_FIELD :
					if ($field['disable'] == 'false') {
					   switch (self::$form_options['name_format']) {
						  case 'name':
							 self::$av_fld_arr[] = 'from_name';
						  break;
						  case 'first_last':
							 self::$av_fld_arr[] = 'first_name';
							 self::$av_fld_arr[] = 'last_name';
						  break;
						  case 'first_middle_i_last':
							 self::$av_fld_arr[] = 'first_name';
							 self::$av_fld_arr[] = 'middle_initial';
							 self::$av_fld_arr[] = 'last_name';
						  break;
						  case 'first_middle_last':
							 self::$av_fld_arr[] = 'first_name';
							 self::$av_fld_arr[] = 'middle_name';
							 self::$av_fld_arr[] = 'last_name';
						  break;
					   }
					}
					break;

				case FSCF_EMAIL_FIELD :
					// email
					self::$autoresp_ok = 1; // used in autoresp settings below
					if ($field['disable'] == 'false') {
						self::$av_fld_arr[] = 'from_email';
					}else{
					   self::$autoresp_ok = 0;
					}
					break;

				case FSCF_SUBJECT_FIELD :
					break;

				case FSCF_MESSAGE_FIELD :
					$msg_key = $key;	// this is used below
					break;

				default :
					// This is an added field

					if ( $field['type'] != 'fieldset-close' && $field['standard'] < 1) {
						if ( $field['type'] == 'fieldset' ) {
						} else if ( $field['type'] == 'attachment' && self::$form_options['php_mailer_enable'] == 'wordpress') {
							self::$av_fld_arr[] = $field['slug'];
						} else { // text, textarea, date, password, email, url, hidden, time, select, select-multiple, radio, checkbox, checkbox-multiple
							self::$av_fld_arr[] = $field['slug'];
							if ( $field['type'] == 'email' )
							  $autoresp_ok = 1;
						}
					}
			}	// end switch
		}	// end foreach

		self::$av_fld_subj_arr = self::$av_fld_arr;
		self::$av_fld_arr[] = 'subject';
		if (self::$form_options['fields'][$msg_key]['disable'] == 'false')
		   self::$av_fld_arr[] = 'message';

		self::$av_fld_arr[] = 'full_message';
		if ( function_exists('akismet_verify_key') && self::$form_options['akismet_disable'] == 'false' )
		   self::$av_fld_arr[] = 'akismet';

		self::$av_fld_arr[] = 'date_time';
        self::$av_fld_arr[] = 'ip_address';
		self::$av_fld_subj_arr[] = 'form_label';

	}	// function set_fld_array()

	static function add_field() {
        check_admin_referer( 'fs_contact_options-options', 'fs_options' );
		self::get_options();
		self::$form_options['fields'][] = FSCF_Util::$field_defaults;
	    self::$new_field_added = __( 'A new field has been added. Now you must edit the field name and details, then click <b>Save Changes</b>.', 'si-contact-form' );
        echo '<div id="message" class="updated fade"><p>' . self::$new_field_added . '</p></div>';


	}	// end function add_field()

	static function add_form() {
        // Add a new form
        check_admin_referer( 'fs_contact_options-options', 'fs_options' );

		self::$global_options = FSCF_Util::get_global_options();
		// Find the next form number
		// When forms are deleted, their form number is NOT reused
		self::$global_options['form_list'][self::$current_form] =  __('New Form', 'si-contact-form');

        // Highest form ID (used to assign ID to new form)
		// When forms are deleted, the remaining forms are NOT renumberd, so max_form_num might be greater than
		// the number of existing forms
        // recalibrate max_form_num to the highest form number (not count)
        ksort(self::$global_options['form_list']);
        self::$global_options['max_form_num'] = max(array_keys(self::$global_options['form_list']));
		update_option( 'fs_contact_global', self::$global_options );
		echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'Form %d has been added.', 'si-contact-form' ), self::$current_form ) . '</p></div>';

		return;
	}

	static function reset_form() {
		// Reset the current form to the defaults, but preserve the name
        check_admin_referer( 'fs_contact_options-options', 'fs_options' );
		self::get_options();
		$form_name = self::$form_options['form_name'];
		self::$form_options = self::$form_defaults;
		self::$form_options['form_name'] = $form_name;
		update_option(self::$form_option_name, self::$form_options);
		echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'Form %d has been reset to the default settings.', 'si-contact-form' ), self::$current_form ) . '</p></div>';
	}

	static function delete_form() {
		// Delete the current form
        check_admin_referer( 'fs_contact_options-options', 'fs_options' );
		self::get_options();
		if ( isset($_POST['form_num']) && is_numeric( $_POST['form_num'] ) ) {
            $form_num = absint($_POST['form_num']);
			$op_name = 'fs_contact_form' . $form_num ;
			$result = delete_option( $op_name );
			if ( ! $result ) {
				// Error deleting option
				echo '<div id="message" class="fsc-error fade"><p>' . sprintf( __( 'An error has occured.  Form %d could not be deleted.', 'si-contact-form' ), $form_num ) . '</p></div>';
			} else {
				unset( self::$global_options['form_list'][$form_num] );
                // Highest form ID (used to assign ID to new form)
			    // When forms are deleted, the remaining forms are NOT renumberd, so max_form_num might be greater than
			    // the number of existing forms
                ksort(self::$global_options['form_list']);
                self::$global_options['max_form_num'] = max(array_keys(self::$global_options['form_list']));
				update_option( 'fs_contact_global', self::$global_options );
				echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'Form %d has been deleted.', 'si-contact-form' ), $form_num  ) . '</p></div>';
			}
		}
	}

 /**
 * Remotely fetch, cache, and display HTML ad for the Fast Secure Contact Form Newsletter plugin addon.
 * for Constant Contact on Newsletter tab
 */
 static function kws_get_remote_ad() {

    // The ad is stored locally for 30 days as a transient. See if it exists.
    $cache = function_exists('get_site_transient') ? get_site_transient('fscf_kws_ad4') : get_transient('fscf_kws_ad4');

    // If it exists, use that (so we save some request time), unless ?cache is set.
    if(!empty($cache) && !isset($_REQUEST['cache'])) { return $cache; }

    // Get the advertisement remotely. An encrypted site identifier, the language of the site, and the version of the FSCF plugin will be sent to katz.co
    $response = wp_remote_post('http://katz.co/ads/', array('timeout' => 45,'body' => array('siteid' => sha1(site_url()), 'language' => get_bloginfo('language'), 'version' => FSCF_VERSION)));

    // If it was a successful request, process it.
    if(!is_wp_error($response)) {

        // Basically, remove <script>, <iframe> and <object> tags for security reasons
        $body = strip_tags(trim(rtrim($response['body'])), '<b><strong><em><i><span><u><ul><li><ol><div><attr><cite><a><style><blockquote><q><p><form><br><meta><option><textarea><input><select><pre><code><s><del><small><table><tbody><tr><th><td><tfoot><thead><u><dl><dd><dt><col><colgroup><fieldset><address><button><aside><article><legend><label><source><kbd><tbody><hr><noscript><link><h1><h2><h3><h4><h5><h6><img>');

        // If the result is empty, cache it for 8 hours. Otherwise, cache it for 30 days.
        $cache_time = empty($response['body']) ? floatval(60*60*8) : floatval(60*60*30);

        if(function_exists('set_site_transient')) {
            set_site_transient('fscf_kws_ad4', $body, $cache_time);
        } else {
            set_transient('fscf_kws_ad4', $body, $cache_time);
        }

        // return the results.
        return  $body;
    }
}


}  // end class FSCF_Options

// end of file
