<?php

/**
 * Description of class-fscf-action
 * Action class for the functions to process ctf_action requests
 * Functions are called statically, so no need to instantiate the class
 * @authors Mike Challis and Ken Carlson
 */

class FSCF_Action {

	static $si_contact_from_email, $si_contact_from_name, $si_contact_mail_sender;

	static function do_ctf_action() {
		// See if a ctf_action has been invoked
		// Is there a ctf_action pending?
		if ( isset($_POST['ctf_action'] ) ) {
			// A ctf_action has been invoked by this plugin--process it
			// Backup Settings and Preview Form are handled in a different place

			// Admins only
			if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				 wp_die(__('You do not have permissions for managing this option', 'si-contact-form'));

			switch ( $_POST['ctf_action'] ) {

			case esc_attr__('Copy Settings', 'si-contact-form'):
				self::copy_settings();
				break;
           	case esc_attr__('Reset Styles on all forms', 'si-contact-form'):
				self::reset_all_styles();
				break;
			case esc_attr__('Restore Settings', 'si-contact-form'):
				self::restore_settings();
				break;
			case esc_attr__('Send Test', 'si-contact-form'):
				self::send_test_email();
				break;
			case esc_attr__('Add Field', 'si-contact-form'):
				FSCF_Options::add_field();
				break;
			case esc_attr__('Add Form', 'si-contact-form'):
				FSCF_Options::add_form();
				break;
			case esc_attr__('Reset Form', 'si-contact-form'):
				FSCF_Options::reset_form();
				break;
			case esc_attr__('Delete Form', 'si-contact-form'):
				FSCF_Options::delete_form();
				break;
            case esc_attr__('Import forms from 3.xx version', 'si-contact-form'):
				FSCF_Util::import_forced('force');
				break;
			default:
			}  // end switch
		}
	}	// end do_ctf_action()

	static function backup_download() {
// outputs a contact form settings backup file

		if ( isset($_POST['si_contact_backup_type'])
		&& (is_numeric($_POST['si_contact_backup_type']) || $_POST['si_contact_backup_type'] == 'all')
		&&	check_admin_referer( 'fs_contact_options-options', 'fs_options' ) ) {

			$backup_type = $_POST['si_contact_backup_type'];
			// get the global options from the database
			$si_contact_bk_gb = get_option("fs_contact_global");
			$si_contact_bk_gb['backup_type'] = $backup_type;
			$eol = "\r\n";

			// format the data to be stored in contact-form-backup.txt
			$string = "**SERIALIZED DATA, DO NOT HAND EDIT!**$eol";
			$ctf_version = FSCF_VERSION;
			$string .= "Backup of forms and settings for 'Fast Secure Contact Form' WordPress plugin $ctf_version$eol";
			$string .= 'Form ID included in this backup: '.$backup_type.$eol;
			$string .= "Web site: ".get_option('home').$eol;
			$string .= "Web site name: ".get_option('blogname').$eol;
			$string .= "Backup date: ".date_i18n(get_option('date_format').' '.get_option('time_format'), time() )."$eol*/$eol";
			$string .= "@@@@SPLIT@@@@$eol";
			$backup_array = array();
			$backup_array[0] = $si_contact_bk_gb;

			if ($backup_type == 'all'){
				// Back all the forms
				$ok = 1;
				foreach ($si_contact_bk_gb['form_list'] as $key => $val){
					$si_contact_bk_opt = get_option('fs_contact_form' . $key);
					if ( ! $si_contact_bk_opt ) {
						// Error, form option not found
						$ok = 0;
						break;
					} else {
						// strip slashes on get options array
                        // XXX not needed
						//$si_contact_bk_opt = stripslashes_deep( $si_contact_bk_opt );
						$backup_array[$key] = $si_contact_bk_opt;
					}
				}  // foreach
			} else {
				// Backup a single form
				$ok = 0;
				if (is_numeric($backup_type)
				&& $si_contact_bk_opt = get_option('fs_contact_form'.$backup_type)){
					// form x
					// strip slashes on get options array
                    // XXX not needed
					//$si_contact_bk_opt = stripslashes_deep( $si_contact_bk_opt );
					$backup_array[1] = $si_contact_bk_opt;
					$ok = 1;
				}
			}

			if(!$ok){
				// bail out
				wp_die(__('Requested form to backup is not found.', 'si-contact-form'));
			}
			$string .= serialize($backup_array);

			$filename = 'contact-form-backup-'.$backup_type.'.txt';

			// turn off compression on the server
            @ini_set('zlib.output_compression', 'Off');

			// force download dialog to web browser
			@ob_end_clean();
			header('Pragma: public');
			header('Expires: -1');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

			//header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			//header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			//header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' .(string)(strlen($string)) );
			flush();
			echo $string;
			exit;
		} // end backup action
	}	// end function backup_download()

	static function copy_settings() {
		// copy settings from one form to another
		// used when resetting or copying style settings
		if ( isset( $_POST['si_contact_copy_what'] )
				&& isset( $_POST['si_contact_this_form'] )
				&& is_numeric( $_POST['si_contact_this_form'] )
				&& isset( $_POST['si_contact_destination_form'] )
			&&	check_admin_referer( 'fs_contact_options-options', 'fs_options' ) ) {

			$copy_what = $_POST['si_contact_copy_what'];
			$this_form = $_POST['si_contact_this_form'];
			$destination_form = $_POST['si_contact_destination_form'];

			// get the global options from the database
			$si_contact_bk_gb = get_option( 'fs_contact_global' );

			// get the options to copy from
			$this_form_arr = get_option( "fs_contact_form$this_form" );

			$ok = 0;
			if ( $destination_form == 'all' ) {
				foreach ( $si_contact_bk_gb['form_list'] as $key => $val ) {
					// No need to copy the source form onto itself
					if ( $key <> $this_form ) {
						if ( $copy_what == 'styles' ) {
							$destination_form_arr = get_option( "fs_contact_form$key" );
							$destination_form_arr = self::copy_styles( $this_form_arr, $destination_form_arr );
							update_option( "fs_contact_form$key", $destination_form_arr );
						} else {
							update_option( "fs_contact_form$key", $this_form_arr );

							// update the global forms list
			                $si_contact_bk_gb['form_list'][$key] = $this_form_arr['form_name'];
						}
					}
				} // end foreach
                // Be sure that the forms are listed in ascending key order
				// Sort the forms list by key
				ksort( $si_contact_bk_gb['form_list'] );
                update_option( 'fs_contact_global', $si_contact_bk_gb );
				$ok = 1;
			} else if ( is_numeric( $destination_form ) ) {
				// Copy a single form
				if ( $copy_what == 'styles' ) {
					$destination_form_arr = get_option( "fs_contact_form$destination_form" );
					$destination_form_arr = self::copy_styles( $this_form_arr, $destination_form_arr );
					update_option( "fs_contact_form$destination_form", $destination_form_arr );
				} else {
					update_option( "fs_contact_form$destination_form", $this_form_arr );

					// update the global forms list
			        $si_contact_bk_gb['form_list'][$destination_form] = $this_form_arr['form_name'];

				}
                // Be sure that the forms are listed in ascending key order
				// Sort the forms list by key
				ksort( $si_contact_bk_gb['form_list'] );
                update_option( 'fs_contact_global', $si_contact_bk_gb );
				$ok = 1;
			} // end else if

			if ( !$ok ) {
				// bail out
				wp_die( __( 'Requested form to copy settings from is not found.', 'si-contact-form' ) );
			}

			// success
			if ( $destination_form == 'all' ) {
				echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'Form %d settings have been copied to all forms.', 'si-contact-form' ), $this_form ) . '</p></div>';
			} else {
				echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'Form %d settings have been copied to form %d.', 'si-contact-form' ), $this_form, $destination_form ) . '</p></div>';
			}
			// Force reload of global and form options
			FSCF_Options::unload_options();

			return;

		} // end action copy settings
	}	// end function copy_settings

   	static function reset_all_styles() {
		// reset all styles on all forms (button on the tools tab)
	   if (
        //isset( $_POST['si_contact_reset_all_styles'] )
		 check_admin_referer( 'fs_contact_options-options', 'fs_options' ) ) {

			// get the global options from the database
			$si_contact_bk_gb = get_option( 'fs_contact_global' );

			// get the style defaults to copy from
            $style_reset_defaults = FSCF_Util::set_style_defaults();

			foreach ( $si_contact_bk_gb['form_list'] as $key => $val ) {

				$destination_form_arr = get_option( "fs_contact_form$key" );

                foreach ( $style_reset_defaults as $skey => $sval ) {
				        $destination_form_arr[$skey] = $sval;
		        }
				update_option( "fs_contact_form$key", $destination_form_arr );

			} // end foreach

            // Be sure that the forms are listed in ascending key order
			// Sort the forms list by key
			ksort( $si_contact_bk_gb['form_list'] );
            update_option( 'fs_contact_global', $si_contact_bk_gb );

			// success
			echo '<div id="message" class="updated fade"><p>' . __( 'Styles have been reset on all forms.', 'si-contact-form' ) . '</p></div>';

			// Force reload of global and form options
			FSCF_Options::unload_options();

			return;

	  } // end action reset_all_styles
	}	// end function reset_all_styles


	static function copy_styles( $this_form_arr, $destination_form_arr ) {
		// Copy the contact form styles from $this_form_arr to $destination_form_arr
		$style_copy_arr = array(
			'border_enable',

            // Alignment DIVs
			'form_style',           // Form DIV, how wide is the form DIV
            'left_box_style',       // left box DIV, container for vcita
            'right_box_style',      // right box DIV, container for vcita
            'clear_style',          // clear both
		    'field_left_style',        // field left (wider)
 		    'field_prefollow_style',   // field pre follow (narrower)
		    'field_follow_style',   // field follow
			'title_style',          // Input labels alignment DIV
			'field_div_style',      // Input fields alignment DIV
			'captcha_div_style_sm', // Small CAPTCHA DIV
			'captcha_div_style_m',  // Large CAPTCHA DIV
			'captcha_image_style',  // CAPTCHA image alignment
			'captcha_reload_image_style', // CAPTCHA reload image alignment
			'submit_div_style',     // Submit DIV
            'border_style',         // style of the form border (if border is enabled)

		     // Style of labels, fields and text
            'required_style',       // required field indicator
            'required_text_style',  // required field text
			'hint_style',           // small text hints like please enter your email again
            'error_style',          // Input validation messages
            'redirect_style',       // Redirecting message
            'fieldset_style',       // style of the fieldset box (for field)
            'label_style',          // Field labels
  			'option_label_style',   // Options labels

 			'field_style',          // Input text fields
  			'captcha_input_style',  // CAPTCHA input field
 			'textarea_style',       // Input Textarea
            'select_style',         // Input Select
 			'checkbox_style',       // Input checkbox
            'radio_style',          // Input radio
            'placeholder_style',    // placeholder style

			'button_style',         // Submit button
			'reset_style',          // Reset button
            'vcita_button_style',     // vCita button
            'vcita_div_button_style', // vCita button div box
			'powered_by_style',     // the "powered by" link

            );

		foreach ( $style_copy_arr as $style_copy ) {
			$destination_form_arr[$style_copy] = $this_form_arr[$style_copy];
		}
		return $destination_form_arr;
	}	// end function copy_styles()


	static function send_test_email() {

		// Send a test mail if necessary
		if ( isset( $_POST['si_contact_to'] )
			&&	check_admin_referer( 'fs_contact_options-options', 'fs_options' ) ) {
			// Send a test email
			// new lines should be (\n for UNIX, \r\n for Windows and \r for Mac)

			FSCF_Options::$form_options = FSCF_Util::get_form_options(FSCF_Options::$current_form, true);
//			get_options();
			$php_eol = (!defined( 'PHP_EOL' )) ? (($eol = strtolower( substr( PHP_OS, 0, 3 ) )) == 'win') ? "\r\n" : (($eol == 'mac') ? "\r" : "\n")  : PHP_EOL;
			$php_eol = (!$php_eol) ? "\n" : $php_eol;

			$email = $_POST['si_contact_to'];
			$name = __( 'Fast Secure Contact Form', 'si-contact-form' );
			if ( FSCF_Util::validate_email( $email ) ) {

				$subject = __( 'Test email to ', 'si-contact-form' ) . $email;
				$message = __( 'This is a test email generated by the Fast Secure Contact Form WordPress plugin.', 'si-contact-form' );
				$message = wordwrap( $message, 70, $php_eol );

				$smtp_debug = '';
				$ctf_email_on_this_domain = FSCF_Options::$form_options['email_from']; // optional
				// prepare the email header
				self::$si_contact_from_name = $name;
				self::$si_contact_from_email = $email;
				//$si_contact_mail_sender = $ctf_email_on_this_domain;
				if ( $ctf_email_on_this_domain != '' ) {
					if ( !preg_match( "/,/", $ctf_email_on_this_domain ) ) {
						// just an email: user1@example.com
						$si_contact_mail_sender = $ctf_email_on_this_domain;
						if ( FSCF_Options::$form_options['email_from_enforced'] == 'true' )
							self::$si_contact_from_email = $ctf_email_on_this_domain;
					} else {
						// name and email: webmaster,user1@example.com
						list($key, $value) = explode( ",", $ctf_email_on_this_domain );
						$key = trim( $key );
						$value = trim( $value );
						$si_contact_mail_sender = $value;
						if ( FSCF_Options::$form_options['email_from_enforced'] == 'true' )
							self::$si_contact_from_email = $value;
					}
				}
				$header_php = 'From: ' . self::$si_contact_from_name . ' <' . self::$si_contact_from_email . '>\n'; // header for php mail only
				$header = '';  // for php mail and wp_mail
				if ( FSCF_Options::$form_options['email_reply_to'] != '' ) { // custom reply_to
					$header .= "Reply-To: " . FSCF_Options::$form_options['email_reply_to'] . "\n";
				} else {
					$header .= "Reply-To: $email\n";
				}
				if ( $ctf_email_on_this_domain != '' ) {
					$header .= 'X-Sender: ' . $si_contact_mail_sender . "\n";
					$header .= 'Return-Path: ' . $si_contact_mail_sender . "\n";
				}
				$header .= 'Content-type: text/plain; charset=' . get_option( 'blog_charset' ) . $php_eol;

				@ini_set( 'sendmail_from', self::$si_contact_from_email );

				if ( FSCF_Options::$form_options['php_mailer_enable'] == 'php' ) {
					// sending with php mail
					$header_php .= $header;
					// Start output buffering to grab smtp debugging output
					ob_start();
					if ( $ctf_email_on_this_domain != '' && FSCF_Util::fsc_is_shell_safe($si_contact_mail_sender) ) {
						// Pass the Return-Path via sendmail's -f command.
						$result = mail( $email, $subject, $message, $header_php, '-f ' . $si_contact_mail_sender );
					} else {
						// the fifth parameter, don't use it if Return-path address not set
						$result = mail( $email, $subject, $message, $header_php );
					}
					$smtp_debug = ob_get_clean();
				} else if ( FSCF_Options::$form_options['php_mailer_enable'] == 'wordpress' ) {
					// sending with wp_mail
					add_filter( 'wp_mail_from', 'FSCF_Action::si_contact_form_from_email', 1 );  // took out _form
					add_filter( 'wp_mail_from_name', 'FSCF_Action::si_contact_form_from_name', 1 );  // took out _form
					if ( $ctf_email_on_this_domain != '' ) {
						// Add an action on phpmailer_init to add Sender $this->si_contact_mail_sender for Return-path in wp_mail
						// this helps spf checking when the Sender email address matches the site domain name
						add_action( 'phpmailer_init', 'FSCF_Action::si_contact_form_mail_sender', 1 );
					}
					global $phpmailer;
					// Make sure the PHPMailer class has been instantiated
					// (copied verbatim from wp-includes/pluggable.php)
					// (Re)create it, if it's gone missing
					if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
						require_once ABSPATH . WPINC . '/class-phpmailer.php';
						require_once ABSPATH . WPINC . '/class-smtp.php';
						$phpmailer = new PHPMailer();
					}

					// Set SMTPDebug to level 2
					$phpmailer->SMTPDebug = 2;

					// Start output buffering to grab smtp debugging output
					ob_start();

					// Send the test mail
					$result = wp_mail( $email, $subject, $message, $header );

					// Grab the smtp debugging output
					$smtp_debug = ob_get_clean();
				}

				// Output the response
				?>
				<div id="message" class="updated"><p><strong><?php
				_e( 'Test Message Sent', 'si-contact-form' );
				echo '<br />' . FSCF_Options::$form_options['php_mailer_enable'];
				echo ' ' . $subject;
				?></strong></p>
				<?php if ( $result != true ) { ?>
					<p><?php _e( 'The result was:', 'si-contact-form' ); ?></p>
					<?php echo '<p><a href="http://www.fastsecurecontactform.com/email-does-not-send">' . __( 'See FAQ', 'si-contact-form' ) . '</a></p>'; ?>
					<pre><?php esc_html(var_dump( $result )); ?></pre>
					<?php
					if ( FSCF_Options::$form_options['php_mailer_enable'] == 'wordpress' ) {
						?>
						<p><?php _e( 'The full debugging output is shown below:', 'si-contact-form' ); ?></p>
						<?php echo '<p><a href="http://www.fastsecurecontactform.com/email-does-not-send">' . __( 'See FAQ', 'si-contact-form' ) . '</a></p>'; ?>
						<pre><?php esc_html(var_dump( $phpmailer )); ?></pre>
						<?php
					}
				} else {
					echo '<p>' . _e( 'Be sure to check your email to see if you received it.', 'si-contact-form' ) . '</p>';
					echo '<p><a href="http://www.fastsecurecontactform.com/email-does-not-send">' . __( 'See FAQ', 'si-contact-form' ) . '</a></p>';
				}
				if ( $smtp_debug != '' ) {
					?>
					<p><?php _e( 'The Email debugging output is shown below:', 'si-contact-form' ); ?></p>
					<?php echo '<p><a href="http://www.fastsecurecontactform.com/email-does-not-send">' . __( 'See FAQ', 'si-contact-form' ) . '</a></p>'; ?>
					<pre><?php echo esc_html($smtp_debug) ?></pre>
					<?php
				}
			} else {
				echo '<div id="message" class="updated"><p><strong>' . __( 'Test failed: Invalid email address', 'si-contact-form' ) . '</strong></p>';
			}
			?>
			</div>
			<?php
		} // end Send a test mail if necessary
	}	// end function send_test_email


	static function si_contact_form_from_email() { 
		// called in send_test_email
		return self::$si_contact_from_email;
	}

	static function si_contact_form_from_name() {
		// called in send_test_email
		return self::$si_contact_from_name;
	}

	static function si_contact_form_mail_sender( $phpmailer ) {
		// called in send_test_email
		// add Sender for Return-path to wp_mail
		$phpmailer->Sender = self::$si_contact_mail_sender;
	}

	static function restore_settings() {
		// restores settings from a contact form settings backup file
		if ( isset( $_POST['si_contact_restore_type'] )
			&& check_admin_referer( 'fs_contact_options-options', 'fs_options' ) ) {

			$bk_form_num = $_POST['si_contact_restore_type'];

			// form file upload
			if ( isset( $_FILES['si_contact_backup_file'] ) && !empty( $_FILES['si_contact_backup_file'] ) ) {
				$file = $_FILES['si_contact_backup_file'];
			} else {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Backup file is required.', 'si-contact-form' ) . '</p></div>';
				return;
			}
			if ( ($file['error'] && UPLOAD_ERR_NO_FILE != $file['error']) || !is_uploaded_file( $file['tmp_name'] ) ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Backup file upload failed.', 'si-contact-form' ) . '</p></div>';
				return;
			}

			if ( empty( $file['tmp_name'] ) ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Backup file is required.', 'si-contact-form' ) . '</p></div>';
				return;
			}

			// check file type
			$file_type_pattern = '/\.txt$/i';
			if ( !preg_match( $file_type_pattern, $file['name'] ) ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Backup file type not allowed.', 'si-contact-form' ) . '</p></div>';
				return;
			}

			// check size
			$allowed_size = 1048576; // 1mb default
			if ( $file['size'] > $allowed_size ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Backup file size is too large.', 'si-contact-form' ) . '</p></div>';
				return;
			}

			// get the uploaded file that contains all the data
			$ctf_backup_data = file_get_contents( $file['tmp_name'] );
			$ctf_backup_data_split = explode( "@@@@SPLIT@@@@\r\n", $ctf_backup_data );
			$ctf_backup_array = unserialize( $ctf_backup_data_split[1] );

			if ( !isset( $ctf_backup_array ) || !is_array( $ctf_backup_array ) || !isset( $ctf_backup_array[0]['backup_type'] ) ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Backup file contains invalid data.', 'si-contact-form' ) . '</p></div>';
				return;
			}

			// Is this uploaded backup set from an older version?
            // Using the Restore tool, you can restore your backed up forms from 2.8 and newer.
            //$old_version = 0;
			//if ( isset($ctf_backup_array[0]['ctf_version'])  || isset($ctf_backup_array[0]['captcha_disable_session']))
            $old_version = 1;

            if ( isset($ctf_backup_array[0]['fscf_version']) )
                $old_version = 0;


			if ( $old_version ) {
				require_once FSCF_PATH . 'includes/class-fscf-import.php';
			}

			$ctf_backup_type = $ctf_backup_array[0]['backup_type'];
			unset( $ctf_backup_array[0]['backup_type'] );

			// is the uploaded file of the "all" type?
			if ( $ctf_backup_type != 'all' && $bk_form_num == 'all' ) {
				echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Selected All to restore, but backup file is a single form.', 'si-contact-form' ) . '</p></div>';
				return;
			}

			// No errors detected, so restore the form(s)
			$glob_options = FSCF_Util::get_global_options();

			// ********** Restore all ? **********
			
			if ( $ctf_backup_type == 'all' && $bk_form_num == 'all' ) {
				// all
                $forms_we_have = count($ctf_backup_array);
				// is the uploaded file of the "all" type?
				//if ( !isset( $ctf_backup_array[2] ) || !is_array( $ctf_backup_array[2] ) ) { // did not always work
                if ( $forms_we_have < 2 ) {
					echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Selected All to restore, but backup form is missing.', 'si-contact-form' ) . '</p></div>';
					return;
				}

                // import a few global options
                $copy_fields = array( 'donated', 'vcita_auto_install', 'vcita_dismiss' );
		        foreach ( $copy_fields as $field ) {
			        if ( ! empty( $ctf_backup_array[0][$field] ) )
				      $glob_options[$field] = $ctf_backup_array[0][$field];
	            }
                // import this global option
                // Highest form ID (used to assign ID to new form)
			    // When forms are deleted, the remaining forms are NOT renumberd, so max_form_num might be greater than
			    // the number of existing forms
		        if ( ! empty( $ctf_backup_array[0]['max_forms'] ) )
			          $glob_options['max_form_num'] = $ctf_backup_array[0]['max_forms'];

				foreach ( $ctf_backup_array as $id => $settings ) {
					// skip the global options array
					if ( 0 == $id )
						continue;

					if ( $old_version )
						$settings = FSCF_Import::convert_form_options ( $settings, $ctf_backup_array[$id]['max_fields'] );
					
					if ( ! get_option( "fs_contact_form$id" ) ) {
						add_option( "fs_contact_form$id", $settings, '', 'yes' );
					} else {
						update_option( "fs_contact_form$id", $settings );
					}
					// Update the form name in the global forms list
                    // sometimes the old version had empty form name
					$glob_options['form_list'][$id] = ( empty($settings['form_name'])) ? 'imported' : $settings['form_name'];


				} // end foreach

				// Be sure that the forms are listed in ascending key order
				// Sort the forms list by key

                // recalibrate max_form_num to the highest form number (not count)   
				ksort( $glob_options['form_list'] );
                $glob_options['max_form_num'] = max(array_keys($glob_options['form_list']));

				// XXX uncomment this later?
				//error_reporting(0); // suppress errors because a different version backup may have uninitialized vars
				// success
				echo '<div id="message" class="updated fade"><p>' . __( 'All form settings have been restored from the backup file.', 'si-contact-form' ) . '</p></div>';

			 // end restoring all

			// ********** Restore single? **********
			
			} else if ( is_numeric( $bk_form_num ) ) {
				// single
				// form numbers do not need to match
				if ( (!get_option( "fs_contact_form$bk_form_num" ) ) ) {
					echo '<div id="message" class="updated fade"><p>' . __( 'Restore failed: Form to restore to does not exist.', 'si-contact-form' ) . '</p></div>';
					return;
				}

				// is the uploaded file of the "single" type?
				if ( !isset( $ctf_backup_array[2] ) || !is_array( $ctf_backup_array[2] ) ) 
					$settings = $ctf_backup_array[1];	// single form backup file
				else 
					$settings = $ctf_backup_array[$bk_form_num];	// "all" backup file

                // XXX uncomment this later?
                //error_reporting(0); // suppress errors because a different version backup may have uninitialized vars
				if ( $old_version )
					$settings = FSCF_Import::convert_form_options ( $settings, $ctf_backup_array[1]['max_fields'] );

				// Update the form name in the global forms list
				$glob_options['form_list'][$bk_form_num] = $settings['form_name'];
				update_option( "fs_contact_form$bk_form_num", $settings );

				// Success
				echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'Form %d settings have been restored from the backup file.', 'si-contact-form' ), $bk_form_num ) . '</p></div>';
			
			} // end restoring single

			// Update the global options to save the updated form list
			update_option( 'fs_contact_global', $glob_options );

			// Force reload of global and form options
			FSCF_Options::unload_options();

			} // end action backup restore
		} // end function restore_settings()

		
}  // end class FSCF_Action

// end of file