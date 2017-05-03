<?php

/**
 * Description of class-fscf-process
 * Process class for processing the contact form once it has been submitted
 * Functions are called statically, so no need to instantiate the class
 * @authors Mike Challis and Ken Carlson
 */

class FSCF_Process {

	static $global_options, $form_options, $form_id_num;
	static $form_data;				// The data from the form, used to repopulate the form if there are errors
	static $form_processed = false;
	static $form_errors = array();  // form entry errors
	static $uploaded_files;
	static $form_action_url;
    static $akismet_spam_subject;
	static $redirect_enable;		// boolean: redirect after successful submit?
	static $meta_string;			// used for meta refresh
	static $email_msg, $email_msg_print, $php_eol, $selected_subject;
	static $email_header = array();		// Fields for the email header
	static $email_set_wp = array();   // used in mail send function
	static $email_fields;  // this holds the fields to display in sending an email
    static $av_tags_arr, $av_tags_subj_arr;		// list of avail field tags
	static $text_type_fields = array(
		'text', 
		'textarea',
		'email', 
		'password',
		'url'
	);
	static $select_type_fields = array(
		//'checkbox', // broke required
		'checkbox-multiple',
		'select',
		'select-multiple',
		'radio'
	);
	
	static function process_form() {
		// Invoked at init via add_action

		// Do we process one of our forms now?
		if ( isset( $_POST['si_contact_action'] ) && ( 'send' == $_POST['si_contact_action'] )
				&& isset( $_POST['form_id']) && is_numeric($_POST['form_id'] ) )
			self::$form_id_num = (int) $_POST['form_id'];
		else 
			// Error: no form id in $_POST
			return;

		// prevent double action
		if ( self::$form_processed )
			return;

         // begin logic that redirects on forged form token.
         $token = 'ok';
         if (!isset($_POST['fs_postonce_'.self::$form_id_num]) || empty($_POST['fs_postonce_'.self::$form_id_num]) || strpos($_POST['fs_postonce_'.self::$form_id_num] , ',') === false ) {
           $token = 'bad';
         }
         $vars = explode(',', $_POST['fs_postonce_'.self::$form_id_num]);
         if ( empty($vars[0]) || empty($vars[1]) || ! preg_match("/^[0-9]+$/",$vars[1]) ) {
           $token = 'bad';
         }
         if ( wp_hash( $vars[1] ) != $vars[0] ) {
             $token = 'bad';
         }
         if ( $token == 'bad' ) {
             // forgery token was no good,  so redirect and blank the form
             self::$form_action_url = FSCF_Display::get_form_action_url();
             wp_redirect( self::$form_action_url );
		     exit;
         }

		self::$global_options = FSCF_Util::get_global_options();
		self::$form_options = FSCF_Util::get_form_options(self::$form_id_num, $use_defauilts=true);

		// Do some security checks
		self::check_security();
		
		self::validate_data();

		self::$form_processed = true;

		if (  empty(self::$form_errors) ) {
			// Send the email, cleanup attachments, redirect.
			self::prepare_email();
            if ( self::$form_options['email_keep_attachments'] != 'true')
			   self::email_sent_cleanup_attachments();
            self::email_sent_redirect();
		}

		if ( ! empty(self::$uploaded_files) ) {
			// unlink (delete) attachment temp files
			foreach ( (array) self::$uploaded_files as $path ) {
				@unlink( $path );
			}
		}

	}  // end function process_form()
	
	static function check_security() {
		// check for various types of intrusion

        // XXX just think a ban feature is better suited for a security plugin
/*		global $fsc_enable_ip_bans, $fsc_banned_ips;

		// check for banned ip
		if ( $fsc_enable_ip_bans && in_array( $_SERVER['REMOTE_ADDR'], $fsc_banned_ips ) )
			wp_die( __( 'Your IP is Banned', 'si-contact-form' ) );*/

		$forbidden = self::spamcheckpost();
		if ( $forbidden )
			wp_die( "$forbidden" );
	}
	
	static function validate_data() {
		// Sanitize and validate the data on the form
		// At the same time, build the string for the email message

		// Set up variables
		// new lines should be (\n for UNIX, \r\n for Windows and \r for Mac)
		self::$php_eol = ( ! defined( 'PHP_EOL' )) ? (($eol = strtolower( substr( PHP_OS, 0, 3 ) )) == 'win') ? "\r\n" : (($eol == 'mac') ? "\r" : "\n")  : PHP_EOL;
		self::$php_eol = ( ! self::$php_eol) ? "\n" : self::$php_eol;
		self::$form_action_url = FSCF_Display::get_form_action_url();
				
		// Go through all the form fields

		// ********** First process the special fields **********
		
		$special_slugs = array( 'f_name', 'm_name', 'mi_name', 'l_name', 'email2', 'mailto_id' );
		foreach ( $special_slugs as $special ) {
			if ( isset($_POST[$special]) ) {
				// Check for newline injection attempts
				self::forbidifnewlines($_POST[$special]);
				self::$form_data[$special] = FSCF_Util::clean_input( $_POST[$special] );
			}
		}
		
		// Get the email-to contact
		$cid = self::$form_data['mailto_id'];
		if ( empty( $cid ) ) {
			self::$form_errors['contact'] = ( self::$form_options['error_contact_select'] != '') ? self::$form_options['error_contact_select'] : __( 'Selecting a contact is required.', 'si-contact-form' );
		} else  {
			$frm_id = self::$form_id_num;
			$contacts = FSCF_Display::get_contact_list(self::$form_id_num, self::$form_options['email_to']);
			$contact = ( isset($contacts[$cid])) ? $contacts[$cid] : false ;
			if ( ! isset( $contact['CONTACT'] ) ) {
			   self::$form_errors['contact'] =  __( 'Requested Contact not found.', 'si-contact-form' );
			}
		}
		// Setup the email and contact name for email
		self::$email_fields['email_to'] = ( isset( $contact['EMAIL'] ) ) ? FSCF_Util::clean_input( $contact['EMAIL'] ) : '';
		self::$email_fields['name_to'] = ( isset( $contact['CONTACT'] ) ) ? FSCF_Util::clean_input( $contact['CONTACT'] ) : '';

        // some people want labels and fields inline, some want the fields on new line
        $inline_or_newline = self::$php_eol;
        if ( self::$form_options['email_inline_label'] == 'true' )
          $inline_or_newline = ' ';

		// Start the email message
        // XXX someone might want to change To: , could add a setting
        self::$email_fields['name_to'] = str_replace('&#39;',"'",self::$email_fields['name_to']);
        self::$email_fields['name_to'] = str_replace('&quot;','"',self::$email_fields['name_to']);
        self::$email_fields['name_to'] = str_replace('&amp;','&',self::$email_fields['name_to']);
		self::$email_msg = self::make_bold( __( 'To:', 'si-contact-form' ) ) . $inline_or_newline . self::$email_fields['name_to'] .self::$php_eol.self::$php_eol;
	
		// ********* Now process the fields set up in Options **********
        $fields_in_use = array();
		foreach ( self::$form_options['fields'] as $key => $field ) {

			if ( 'true' == $field['disable']  || 'fieldset-close' == $field['type'] ) continue;
            $fields_in_use[$field['slug']] = 1;
			if ( 'fieldset' == $field['type'] ) {
				self::$email_msg .= self::make_bold($field['label']) . $inline_or_newline;
				continue;
			}

			// ***** Do processing that applies to all fields *****

			// Check for newline injection attempts
			if ( in_array($field['type'], self::$text_type_fields) && $field['type'] != 'textarea' ) {
               if ( !empty($_POST[$field['slug']]) )
				  self::forbidifnewlines( $_POST[$field['slug']] );
            }

			// Add sanitized data from POST to the form data array
			if ( isset( $_POST[$field['slug']] ) ) {
				if ( 'textarea' == $field['type'] && 'true' == self::$form_options['textarea_html_allow'] )
					self::$form_data[$field['slug']] = wp_kses_data( stripslashes( $_POST[$field['slug']] ) ); // allow only some safe html
				else
					self::$form_data[$field['slug']] = FSCF_Util::clean_input( $_POST[$field['slug']] );
			}
			// Set up values for unchecked checkboxes and unselected radio types
			else if ( 'checkbox' == $field['type'] || 'radio' == $field['type'] )
				self::$form_data[$field['slug']] = '';
			else if ( 'checkbox-multiple' == $field['type'] )
				self::$form_data[$field['slug']] = array();

			// XXX changed for option to hide labels that do not have field values, like when not required.
			// self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;

			// Required validate
			// ..different for checkbox-multiple, select types.  Not for hidden, checkbox
			if ( in_array($field['type'], self::$select_type_fields) ) {
				//if ( 'checkbox' != $field['type'] ) {
					// select, select-multiple, checkbox-multiple require at least one item to be selected
                    if ( 'subject' == $field['slug'] && 'select' == $field['type']) {
					   self::$selected_subject = self::validate_subject_select( $field );
					} else if ( 'select' == $field['type']) {
					   self::validate_select( $field['slug'], $field );
                    } else if ( 'true' == $field['req'] ) {
						if ( ! isset($_POST[$field['slug']]) ) {
						self::$form_errors[$field['slug']] = (self::$form_options['error_select'] != '') ? self::$form_options['error_select'] : __('At least one item in this field is required.', 'si-contact-form');
						}
					}
				//}
			} else if ( 'hidden' != $field['type'] && 'attachment' != $field['type'] ) {
                if ( 'true' == $field['placeholder'] && $field['default'] != '' && isset($_POST[$field['slug']])  ) {
                  // strip out the placeholder they posted with
                  $examine_placeholder_input = '';
                  $examine_placeholder_input = stripslashes($_POST[$field['slug']]);
                  if ($field['default'] == $examine_placeholder_input ) {
                       $_POST[$field['slug']] = '';
                  }
				}
				// Check for required fields
				// The name and email fields are validated separately
				if ( 'full_name' == $field['slug'] )
					self::validate_name( $field, $inline_or_newline );
				else if ( 'email' == $field['slug'] )
					self::validate_email( $field['req'], $inline_or_newline );
                else if ( 'email' == $field['type'] ) // extra field email type
					self::validate_email_type($field['slug'], $field['req'] );
                else if ( 'url' == $field['type'] ) // extra field email type
					self::validate_url_type($field['slug'], $field['req'] );
				else if ( 'true' == $field['req'] && $_POST[$field['slug']] == '' ) {
					self::$form_errors[$field['slug']] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
				}
			}

			// Max len validate (text type fields, and date?)
			if ( in_array($field['type'], self::$text_type_fields) && $field['max_len'] != ''
				&& strlen($_POST[$field['slug']]) > $field['max_len'] ) {
				self::$form_errors[$field['slug']] = sprintf( ( self::$form_options['error_maxlen'] != '') ? self::$form_options['error_maxlen'] : __('Maximum of %d characters exceeded.', 'si-contact-form'), $field['max_len']);
			}

			// Regex validate (not for hidden, checkbox/m, select/m, radio)
			if ( ! in_array($field['type'], self::$select_type_fields) && 'hidden' != $field['type'] && 'checkbox' != $field['type'] && $field['regex'] != '' ) {
               if ( 'true' == $field['req'] && empty($_POST[$field['slug']]) ) {
                   self::$form_errors[$field['slug']] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
               } else if ( !empty($_POST[$field['slug']]) && ! preg_match($field['regex'],$_POST[$field['slug']])) {
                  self::$form_errors[$field['slug']] = ($field['regex_error'] != '') ? $field['regex_error'] : __('Invalid input.', 'si-contact-form');
               }
			}

           // filter hook for form input validation
           self::$form_errors = apply_filters('si_contact_form_validate', self::$form_errors, self::$form_id_num);
			
			// ***** Now do processing based on field type *****

			switch ( $field['type'] ) {
				case 'text' :
				case 'email':
				case 'hidden':
				case 'textarea' :
				case 'password' :
				case 'url' :
					if ( 'full_name' != $field['slug'] && 'email' != $field['slug']  ) {
                        if ( self::$form_data[$field['slug']] == '' && self::$form_options['email_hide_empty'] == 'true' ) {

                        } else {
                                if ( 'subject' == $field['slug'] ) {
                                        $this_label = (self::$form_options['title_subj'] != '') ? self::$form_options['title_subj'] : __( 'Subject:', 'si-contact-form' );
                                        self::$email_msg .= self::make_bold( $this_label ) . $inline_or_newline;
                                } elseif ( 'message' == $field['slug'] ) {
                                        $this_label = (self::$form_options['title_mess'] != '') ? self::$form_options['title_mess'] : __( 'Message:', 'si-contact-form' );
                                        self::$email_msg .= self::make_bold( $this_label ) . $inline_or_newline;
                                } else {
                                        self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
                                }
                                self::$email_fields[$field['slug']] = self::$form_data[$field['slug']];
						        self::$email_msg .=  self::$form_data[$field['slug']] . self::$php_eol . self::$php_eol;
                        }
					}
					break;
				
				case 'checkbox' :
                    if ( empty( self::$form_data[$field['slug']] ) && self::$form_options['email_hide_empty'] == 'true' ) {

                    } else {
					        if ( '1' == self::$form_data[$field['slug']] ) {
                               self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
					           //self::$email_fields[$field['slug']] = '* '.__('selected', 'si-contact-form');
                               self::$email_fields[$field['slug']] = __('selected', 'si-contact-form');
					           self::$email_msg .=  self::$email_fields[$field['slug']] . self::$php_eol . self::$php_eol;
                            }
					}
					break;

				case 'radio' :
					// the response is the number of a single option
					// Get the options list
					$opts_array = explode("\n",$field['options']);
					if ( '' == $opts_array[0] && 'checkbox' == $field['type'] )
						$opts_array[0] = $field['label'];  // use the field name as the option name
                    if ( ! isset( $opts_array[self::$form_data[$field['slug']]-1] ) && self::$form_options['email_hide_empty'] == 'true' ) {

                    } else {
					        if ( isset($opts_array[self::$form_data[$field['slug']]-1]) ) {
                                self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
					         	//self::$email_fields[$field['slug']] = ' * ' . $opts_array[self::$form_data[$field['slug']]-1];
                                self::$email_fields[$field['slug']] = $opts_array[self::$form_data[$field['slug']]-1];
                                        // is this key==value set? use the key
                                        if ( preg_match('/^(.*)(==)(.*)$/', self::$email_fields[$field['slug']], $matches) ) {
                                             self::$email_fields[$field['slug']] = $matches[1];
                                        }
						        self::$email_msg .=  self::$email_fields[$field['slug']] . self::$php_eol . self::$php_eol;
					         }
                    }
					break;

				case 'select' :
                    $chosen = '';
                    if ( 'subject' == $field['slug'] && 'select' == $field['type']) {
                           $chosen = self::$selected_subject;
                    } else {
                       // response(s) are in an array
					    // was anything selected?
					   if ( ! empty(self::$form_data[$field['slug']]) ) {
						   $opts_array = explode("\n",$field['options']);
                           if (preg_match('/^\[.*]$/', trim($opts_array[0])))  // "[Please select]"
                                 unset($opts_array[0]);
                           else
                              $opts_array = array_combine(range(1, count($opts_array)), array_values($opts_array));
						   foreach ( $opts_array as $k => $v ) {
							  if ( in_array($k, self::$form_data[$field['slug']]) ) {
                                  // is this key==value set? use the key
                                  if ( preg_match('/^(.*)(==)(.*)$/', $v, $matches) )
                                      $v = $matches[1];
                                  $chosen .= $v;  // only one should be selected
                              }
						   }
					  }
                    }
                    if ( $chosen == '' && self::$form_options['email_hide_empty'] == 'true' ) {

                    } else {
                         if ( 'subject' == $field['slug'] && 'select' == $field['type']) {
                                 $this_label = (self::$form_options['title_subj'] != '') ? self::$form_options['title_subj'] : __( 'Subject:', 'si-contact-form' );
                                 self::$email_msg .= self::make_bold( $this_label ) . $inline_or_newline;
                         } else {
                                 self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
                         }
					        self::$email_fields[$field['slug']] = $chosen;
					        self::$email_msg .=  $chosen . self::$php_eol . self::$php_eol;
                    }
                    break;
				case 'select-multiple' :
				case 'checkbox-multiple' :
					// response(s) are in an array
					$chosen = '';
					// was anything selected?
					if ( ! empty(self::$form_data[$field['slug']]) ) {
						$opts_array = explode("\n",$field['options']);
                        if ( count(self::$form_data[$field['slug']]) > 1 ) { // prefix with ' * ' for multiple selections
						        foreach ( $opts_array as $k => $v ) {
							         if ( in_array($k+1, self::$form_data[$field['slug']]) ) {
                                        // is this key==value set? use the key
                                        if ( preg_match('/^(.*)(==)(.*)$/', $v, $matches) )
                                             $v = $matches[1];
							        	$chosen .= ' * '.$v;
                                     }
						        }
                        } else {
                                foreach ( $opts_array as $k => $v ) {  // no prefix ' * ' on single selections
							         if ( in_array($k+1, self::$form_data[$field['slug']]) ) {
                                        // is this key==value set? use the key
                                        if ( preg_match('/^(.*)(==)(.*)$/', $v, $matches) )
                                             $v = $matches[1];
								         $chosen .= $v;
                                     }
						        }
                        }
					}
					if ( $chosen == '' && self::$form_options['email_hide_empty'] == 'true' ) {

                    } else {
                            self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
					        self::$email_fields[$field['slug']] = $chosen;
					        self::$email_msg .=  $chosen . self::$php_eol . self::$php_eol;
                    }
					break;
				
				case 'date' :
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
                    $not_chosen = 0;
					if( 'true' != $field['req'] && ( $cal_date_array[self::$form_options['date_format']] == $_POST[$field['slug']] || empty($_POST[$field['slug']]) ) ) { // not required, no date picked
                             // this field wasn't set to required, no date picked, skip it
                             $not_chosen = 1;
                    } else if ( !self::validate_date( self::$form_data[$field['slug']], self::$form_id_num ) ) { // picked a date
						self::$form_errors[$field['slug']] = sprintf( (self::$form_options['error_date'] != '') ? self::$form_options['error_date'] : __('Please select a valid date in this format: %s.', 'si-contact-form'), $cal_date_array[self::$form_options['date_format']] );
					} else {
                            if ( $not_chosen && self::$form_options['email_hide_empty'] == 'true' ) {

                            } else {
                                    self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
					                self::$email_fields[$field['slug']] = self::$form_data[$field['slug']];
						            self::$email_msg .= self::$form_data[$field['slug']] . self::$php_eol . self::$php_eol;
                            }
					}
					break;

				case 'time' :
                   $not_chosen = 0;
				   if ( self::$form_options['time_format'] == '12' ) {
						$concat_time = self::$form_data[$field['slug']]['h'] . ':' . self::$form_data[$field['slug']]['m'] . ' ' . self::$form_data[$field['slug']]['ap'];
                        if( 'true' != $field['req'] && ( empty(self::$form_data[$field['slug']]['h']) && empty(self::$form_data[$field['slug']]['m']) && empty(self::$form_data[$field['slug']]['ap']) ) ) { // not required, no time picked
                             // this field wasn't set to required, no times picked, skip it
                             $not_chosen = 1;
                             $concat_time = '';
                        } else if( 'true' != $field['req'] && ! self::validate_time_ap( self::$form_data[$field['slug']]['h'], self::$form_data[$field['slug']]['m'], self::$form_data[$field['slug']]['ap'] )   ) {  // selection is incomplete
					         self::$form_errors[$field['slug']] = ( self::$form_options['error_time'] != '') ? self::$form_options['error_time'] : __('The time selections are incomplete, select all or none.', 'si-contact-form');
                        } else if ( 'true' == $field['req'] && ( ! preg_match("/^[0-9]{2}$/", self::$form_data[$field['slug']]['h']) || ! preg_match("/^[0-9]{2}$/", self::$form_data[$field['slug']]['m']) || empty( self::$form_data[$field['slug']]['ap'] ) ) ) { // not picked a time
					      	self::$form_errors[$field['slug']] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
					    }
				   } else {
                        // 24 hour format with no am/pm select field
						$concat_time = self::$form_data[$field['slug']]['h'] . ':' . self::$form_data[$field['slug']]['m'];
                        if( 'true' != $field['req'] && ( empty(self::$form_data[$field['slug']]['h']) && empty(self::$form_data[$field['slug']]['m']) ) ) { // not required, no time picked
                             // this field wasn't set to required, no times picked, skip it
                             $not_chosen = 1;
                             $concat_time = '';
                        } else if( 'true' != $field['req'] && ! self::validate_time( self::$form_data[$field['slug']]['h'], self::$form_data[$field['slug']]['m'] )   ) {  // selection is incomplete
					         self::$form_errors[$field['slug']] = ( self::$form_options['error_time'] != '') ? self::$form_options['error_time'] : __('The time selections are incomplete, select all or none.', 'si-contact-form');
                        } else if ( 'true' == $field['req'] && ( ! preg_match("/^[0-9]{2}$/", self::$form_data[$field['slug']]['h']) || ! preg_match("/^[0-9]{2}$/", self::$form_data[$field['slug']]['m']) ) ) { // not picked a time
					      	self::$form_errors[$field['slug']] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
					    }

                   }
                    if ( $not_chosen && self::$form_options['email_hide_empty'] == 'true' ) {

                    } else {
                            self::$email_msg .= self::make_bold( $field['label'] ) . $inline_or_newline;
                            self::$email_fields[$field['slug']] = $concat_time;
					        self::$email_msg .= $concat_time . self::$php_eol . self::$php_eol;
                    }
					break;
				
				case 'attachment' :
					self::validate_attach($field['slug'], $field['req'], $field['label'], $inline_or_newline);
					break;

				default :

			}  // end switch

			
		}  // end foreach

		// Add any hidden fields added by shortcodes
		// This is used only for sending email.  If the form is redrawn, the hidden fields will be added from
		// the shortcode.
		$frm_id = self::$form_id_num;
		if ( self::$global_options['enable_php_sessions'] == 'true'
				&& ! empty($_SESSION["fsc_shortcode_hidden_$frm_id"]) ) {
			$hidden_fields = $_SESSION["fsc_shortcode_hidden_$frm_id"];
			foreach ( $hidden_fields as $key => $value ) {
				if ( $key != '' && $value != '' ) {
					if ( $key == 'form_page' ) {  // page url
						self::$email_msg .= self::make_bold( __( 'Form Page', 'si-contact-form' ) ) . $inline_or_newline. esc_url( self::$form_action_url ) . self::$php_eol . self::$php_eol;
						self::$email_fields['form_page'] = esc_url( self::$form_action_url );
					} else {
						self::$email_msg .= self::make_bold( $key ) . $inline_or_newline . stripslashes( $value ) . self::$php_eol . self::$php_eol;
						self::$email_fields[$key] = $value;
					}
				}
				}
		}

        // filter hook to add any custom fields to email_fields array (not validated)
        self::$email_fields = apply_filters('si_contact_email_fields', self::$email_fields, self::$form_id_num);

        // filter hook to add any custom fields to email message (not validated)
        self::$email_msg = apply_filters('si_contact_email_msg', self::$email_msg, $inline_or_newline, self::$php_eol, self::$form_id_num);

        if (self::$form_options['print_form_enable'] == 'true') {
          self::$email_msg_print = self::$email_msg;
          //self::$email_msg_print .= self::make_bold( 'Time:' ) . $inline_or_newline;
		  //self::$email_msg_print .= date_i18n(get_option('date_format').' '.get_option('time_format'), current_time('timestamp') );
        }

		self::$email_fields['date_time'] = date_i18n(get_option('date_format').' '.get_option('time_format'), current_time('timestamp') );

        self::$email_fields['ip_address'] = (isset( $_SERVER['REMOTE_ADDR'] )) ? $_SERVER['REMOTE_ADDR'] : 'n/a';

		self::check_captcha();

        // check honeypot, if enabled
        if ( self::$form_options['honeypot_enable'] == 'true' && !isset(self::$form_errors['captcha'])) {
           $honeypot_slug = FSCF_Display::get_todays_honeypot_slug($fields_in_use);
           if ( !empty($_POST[$honeypot_slug] ) )
           self::$form_errors[$honeypot_slug] = (self::$form_options['error_spambot'] != '') ? self::$form_options['error_spambot'] : __('Possible spam bot. Try again.', 'si-contact-form');
        }

		self::$email_msg .= self::check_akismet();

        if ( self::$form_options['sender_info_enable'] == 'true' )
		   self::$email_msg .= self::get_user_info(); // adds sender info to email

        // filter hook for modifying the complete email message
        self::$email_msg = apply_filters('si_contact_email_message', self::$email_msg, self::$email_fields, $inline_or_newline, self::$php_eol, self::$form_id_num);

		return;
	}  // end function validate_data


 	static function validate_time( $hr, $min ) {
    // 24 hour format with no am/pm select field
    // Checks time input to find out if time was selectors were selected but incomplete

    // was all time inputs selected?
    if ( preg_match("/^[0-9]{2}$/", $hr) && preg_match("/^[0-9]{2}$/", $min)  )
     return true;

     // were none time inputs not selected
     if ( !preg_match("/^[0-9]{2}$/", $hr) && !preg_match("/^[0-9]{2}$/", $min) )
     return true;

     // only some were selected, but not all
	 return false;

    } // end function validate_time()

    static function validate_time_ap( $hr, $min, $ap ) {
    // 12 hour format with am/pm select field
    // Checks time input to find out if time was selectors were selected but incomplete

    // was all time inputs selected?
    if ( preg_match("/^[0-9]{2}$/", $hr) && preg_match("/^[0-9]{2}$/", $min) && !empty( $ap ) )
     return true;

     // were none time inputs not selected
     if ( !preg_match("/^[0-9]{2}$/", $hr) && !preg_match("/^[0-9]{2}$/", $min) && empty( $ap ) )
     return true;

     // only some were selected, but not all
	 return false;

    } // end function validate_time()

 	static function validate_date( $input, $form_id_num ) {
    // Checks date input for proper formatting of actual calendar dates
    // Matches the date format and also validates month and number of days in a month.
    // All leap year dates allowed.

    if ( ! self::$form_options )
		   self::$form_options = FSCF_Util::get_form_options ( $form_id_num, $use_defaults=true );

    $date_format = self::$form_options['date_format'];
    // find the delimiter of the date_format setting: slash, dash, or dot
    if (strpos($date_format,'/')) {
      $delim = '/'; $regexdelim = '\/';
    } else if (strpos($date_format,'-')) {
       $delim = '-'; $regexdelim = '-';
    } else if (strpos($date_format,'.')) {
      $delim = '.';  $regexdelim = '\.';
    }

    if ( $date_format == "mm${delim}dd${delim}yyyy" )
        $regex = "/^(((0[13578]|(10|12))${regexdelim}(0[1-9]|[1-2][0-9]|3[0-1]))|(02${regexdelim}(0[1-9]|[1-2][0-9]))|((0[469]|11)${regexdelim}(0[1-9]|[1-2][0-9]|30)))${regexdelim}[0-9]{4}$/";

	if ( $date_format == "dd${delim}mm${delim}yyyy" )
        $regex = "/^(((0[1-9]|[1-2][0-9]|3[0-1])${regexdelim}(0[13578]|(10|12)))|((0[1-9]|[1-2][0-9])${regexdelim}02)|((0[1-9]|[1-2][0-9]|30)${regexdelim}(0[469]|11)))${regexdelim}[0-9]{4}$/";

	if ( $date_format == "yyyy${delim}mm${delim}dd" )
        $regex = "/^[0-9]{4}${regexdelim}(((0[13578]|(10|12))${regexdelim}(0[1-9]|[1-2][0-9]|3[0-1]))|(02${regexdelim}(0[1-9]|[1-2][0-9]))|((0[469]|11)${regexdelim}(0[1-9]|[1-2][0-9]|30)))$/";

    if ( ! preg_match($regex, $input)  )
	    return false;
    else
        return true;

    } // end function validate_date()

	static function validate_name($field, $inline_or_newline) {
		// validates all the standard name inputs

        // The name components are already sanitized and stored in self::$form_data

        $placeh_name_fail = $placeh_fname_fail = $placeh_lname_fail = $placeh_mname_fail = $placeh_miname_fail = 0;

		// If the name is required, make sure it is there
		if ( 'true' == $field['req'] ) {
			switch ( self::$form_options['name_format'] ) {
				case 'name':
					if ( '' == self::$form_data['full_name'] || $placeh_name_fail) {
						self::$form_errors['full_name'] = (self::$form_options['error_name'] != '') ? self::$form_options['error_name'] : __( 'Your name is required.', 'si-contact-form' );
                        if ($placeh_name_fail)
                           self::$form_data['full_name'] = $field['default'];
                    }
					break;
				default:
                  // middle initial is allowed to be empty
					if ( empty( self::$form_data['f_name'] ) || $placeh_fname_fail ) {
						self::$form_errors['f_name'] = (self::$form_options['error_name'] != '') ? self::$form_options['error_name'] : __( 'Your name is required.', 'si-contact-form' );
                        if ($placeh_fname_fail)
                           self::$form_data['f_name'] = $f_default;
                    }
					if ( empty( self::$form_data['l_name'] ) || $placeh_lname_fail ) {
						self::$form_errors['l_name'] = (self::$form_options['error_name'] != '') ? self::$form_options['error_name'] : __( 'Your name is required.', 'si-contact-form' );
                         if ($placeh_lname_fail)
                           self::$form_data['l_name'] = $l_default;
                    }
                    if ( self::$form_options['name_format'] == 'first_middle_last' ) {
					   if ($placeh_mname_fail)
                         self::$form_data['m_name'] = $m_default;
                    }
                    if ( self::$form_options['name_format'] == 'first_middle_i_last' ) {
					   if ($placeh_miname_fail)
                         self::$form_data['mi_name'] = $mi_default;
                    }
			}  // end switch
		}

		// If necessary, adjust the name case
		foreach ( array('full_name','f_name','m_name','l_name') as $fld) {
			if ( ! empty(self::$form_data[$fld]))
				self::$form_data[$fld] = FSCF_Util::name_case(self::$form_data[$fld]);
		}

		// Add the name to the email message
		switch ( self::$form_options['name_format'] ) {
			case 'name':
                if ( self::$form_data['full_name'] == '' && self::$form_options['email_hide_empty'] == 'true' ) {

                } else {
                        $this_label = (self::$form_options['title_name'] != '') ? self::$form_options['title_name'] : __( 'Name:', 'si-contact-form' );
                        self::$email_msg .= self::make_bold( $this_label ) . $inline_or_newline;
					    self::$email_msg .= self::$form_data['full_name'] . self::$php_eol . self::$php_eol;
                }
				break;
			case 'first_last':
				self::$email_msg .= (self::$form_options['title_fname'] != '') ? self::$form_options['title_fname'] : __( 'First Name:', 'si-contact-form' );
				self::$email_msg .= ' ' . self::$form_data['f_name'] . self::$php_eol;
				self::$email_msg .= (self::$form_options['title_lname'] != '') ? self::$form_options['title_lname'] : __( 'Last Name:', 'si-contact-form' );
				self::$email_msg .= ' ' . self::$form_data['l_name'] . self::$php_eol . self::$php_eol;
				self::$email_fields['first_name'] = self::$form_data['f_name'];
				self::$email_fields['last_name'] = self::$form_data['l_name'];
				break;
			case 'first_middle_i_last':
				self::$email_msg .= (self::$form_options['title_fname'] != '') ? self::$form_options['title_fname'] : __( 'First Name:', 'si-contact-form' );
				self::$email_msg .= ' ' . self::$form_data['f_name'] . self::$php_eol;
				if ( self::$form_data['mi_name'] != '' && !$placeh_miname_fail ) {
					self::$email_msg .= (self::$form_options['title_miname'] != '') ? self::$form_options['title_miname'] : __( 'Middle Initial:', 'si-contact-form' );
					self::$email_msg .= ' ' . self::$form_data['mi_name'] . self::$php_eol;
				}
				self::$email_msg .= (self::$form_options['title_lname'] != '') ? self::$form_options['title_lname'] : __( 'Last Name:', 'si-contact-form' );
				self::$email_msg .= ' ' . self::$form_data['l_name'] . self::$php_eol . self::$php_eol;
				break;
			case 'first_middle_last':
				self::$email_msg .= (self::$form_options['title_fname'] != '') ? self::$form_options['title_fname'] : __( 'First Name:', 'si-contact-form' );
				self::$email_msg .= ' ' . self::$form_data['f_name'] . self::$php_eol;
				if ( self::$form_data['m_name'] != '' && !$placeh_mname_fail ) {
					self::$email_msg .= (self::$form_options['title_mname'] != '') ? self::$form_options['title_mname'] : __( 'Middle Name:', 'si-contact-form' );
					self::$email_msg .= ' ' . self::$form_data['m_name'] . self::$php_eol;
				}
				self::$email_msg .= (self::$form_options['title_lname'] != '') ? self::$form_options['title_lname'] : __( 'Last Name:', 'si-contact-form' );
				self::$email_msg .= ' ' . self::$form_data['l_name'] . self::$php_eol . self::$php_eol;
		}
			
		// Build the name string for the email
		self::$email_fields['from_name'] = '';
		if ( ! empty( self::$form_data['full_name'] ) )
			self::$email_fields['from_name'] .= self::$form_data['full_name'];
		if ( ! empty( self::$form_data['f_name'] ) )
			self::$email_fields['from_name'] .= self::$form_data['f_name'];
		if ( ! empty( self::$form_data['mi_name'] ) )
			self::$email_fields['from_name'] .= ' ' . self::$form_data['mi_name'];
		if ( ! empty( self::$form_data['m_name'] ) )
			self::$email_fields['from_name'] .= ' ' . self::$form_data['m_name'];
		if ( ! empty( self::$form_data['l_name'] ) )
			self::$email_fields['from_name'] .= ' ' . self::$form_data['l_name'];

	}  // end function validate_name()

	static function validate_email($req, $inline_or_newline) {
       	// validates all the standard email inputs
		if ( isset( $_POST['email'] ) )
			$email = strtolower( FSCF_Util::clean_input( $_POST['email'] ) );
		if ( 'true' == self::$form_options['double_email'] ) {
            $req = 'true';
			if ( isset( $_POST['email2'] ) )
				$email2 = strtolower( FSCF_Util::clean_input( $_POST['email2'] ) );
		}

		if ( 'true' == $req ) {
			if ( ! FSCF_Util::validate_email( self::$form_data['email'] ) ) {
				self::$form_errors['email'] = (self::$form_options['error_email'] != '') ? self::$form_options['error_email'] : __( 'A proper email address is required.', 'si-contact-form' );
			}
			if ( 'true' == self::$form_options['double_email'] && ! FSCF_Util::validate_email( $email2 ) ) {
				self::$form_errors['email2'] = (self::$form_options['error_email'] != '') ? self::$form_options['error_email'] : __( 'A proper email address is required.', 'si-contact-form' );
			}
			if ( 'true' == self::$form_options['double_email'] && !empty($email) && !empty($email2) && ($email != $email2) ) {
				self::$form_errors['email2'] = (self::$form_options['error_email2'] != '') ? self::$form_options['error_email2'] : __( 'The two email addresses did not match.', 'si-contact-form' );
			}
		}
        if ( empty($email) && self::$form_options['email_hide_empty'] == 'true' ) {

        } else {
                $this_label = (self::$form_options['title_email'] != '') ? self::$form_options['title_email'] : __( 'Email:', 'si-contact-form' );
                self::$email_msg .= self::make_bold( $this_label ) . $inline_or_newline;
		        self::$email_fields['from_email'] = self::$form_data['email'];
	            self::$email_msg .= self::$email_fields['from_email'] . self::$php_eol . self::$php_eol;
        }

	}  // end function validate_email()

    static function validate_email_type( $slug, $req ) {
      // validates extra field type that is email
      if ( 'true' == $req  ) {
           if ( ! FSCF_Util::validate_email( self::$form_data[$slug] ) )
	       self::$form_errors[$slug] = (self::$form_options['error_email'] != '') ? self::$form_options['error_email'] : __( 'A proper email address is required.', 'si-contact-form' );
	  } else if ( !empty(self::$form_data[$slug]) ) {
           if ( ! FSCF_Util::validate_email( self::$form_data[$slug] ) )  // was not required but something filled it, so ckeck
	       self::$form_errors[$slug] =  (self::$form_options['error_email_check'] != '') ? self::$form_options['error_email_check'] : __( 'Not a proper email address.', 'si-contact-form' );
      }

    } // end function validate_email_type

     static function validate_url_type( $slug, $req ) {
      // validates extra fiedld type that is url
      if ( 'true' == $req  ) {
           if ( ! FSCF_Util::validate_url( self::$form_data[$slug] ) )
	       self::$form_errors[$slug] = (self::$form_options['error_url'] != '') ? self::$form_options['error_url'] : __( 'Invalid URL.', 'si-contact-form' );
	  } else if ( !empty(self::$form_data[$slug]) ) {
           if ( ! FSCF_Util::validate_url( self::$form_data[$slug] ) )  // was not required but something filled it, so ckeck
	       self::$form_errors[$slug] =  (self::$form_options['error_url'] != '') ? self::$form_options['error_url'] : __( 'Invalid URL.', 'si-contact-form' );
      }

    } // end function validate_url_type

    static function validate_subject_select( $field ) {
      // validates subject type that is select
      // response(s) are in an array
      $sid = self::$form_data['subject'][0];
	  $opts_array = explode("\n",$field['options']);
      if (preg_match('/^\[.*]$/', trim($opts_array[0])))  // "[Please select]"
           unset($opts_array[0]);
      else
           $opts_array = array_combine(range(1, count($opts_array)), array_values($opts_array)); //0 key becomes 1
      if( empty($sid) ) {
               self::$form_errors['subject'] = (self::$form_options['error_subject'] != '') ? self::$form_options['error_subject'] :  __('Selecting a subject is required.', 'si-contact-form');
      } else if (empty($opts_array) || !isset($opts_array[$sid])) {
               self::$form_errors['subject'] = __('Requested subject not found.', 'si-contact-form');
      } else {
               return $opts_array[$sid];
      }

    } // end function validate_subject_select


    static function validate_select( $slug, $field ) {
      // validates extra field type that is select
      // response(s) are in an array
      $sid = self::$form_data[$slug][0];
	  $opts_array = explode("\n",$field['options']);
      if (preg_match('/^\[.*]$/', trim($opts_array[0])))  // "[Please select]"
           unset($opts_array[0]);
      else
           $opts_array = array_combine(range(1, count($opts_array)), array_values($opts_array)); //0 key becomes 1
      if ( 'true' == $field['req'] ) {
         if( empty($sid) ) {
                  self::$form_errors[$slug] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
         } else if (empty($opts_array) || !isset($opts_array[$sid])) {
                  self::$form_errors[$slug] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
         }
      }
    } // end function validate_select

	static function validate_attach( $slug, $req, $label, $inline_or_newline ) {
		// validates and saves uploaded file attchments for file attach field types.
		// also sets errors if the file did not upload or was not accepted.
		// Test if a file was selected for attach.
		$field_file['name'] = '';
		if ( isset( $_FILES[$slug] ) )
			$field_file = $_FILES[$slug];

        if ( 'true' == $req && empty( $field_file['name'] ) ) {
				self::$form_errors[$slug] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
                return;
         }
		if($field_file['name'] != '') {  // may not be required
			if ( self::$form_options['php_mailer_enable'] == 'php' ) {
				self::$form_errors[$slug] = __( 'Attachments not supported.', 'si-contact-form' );
                return;
			} else if ( ($field_file['error'] && UPLOAD_ERR_NO_FILE != $field_file['error']) || !is_uploaded_file( $field_file['tmp_name'] ) ) {
				self::$form_errors[$slug] = __( 'Attachment upload failed.', 'si-contact-form' );
                return;
			} else if ( empty( $field_file['tmp_name'] ) ) {
				self::$form_errors[$slug] = ( self::$form_options['error_field'] != '') ? self::$form_options['error_field'] : __('This field is required.', 'si-contact-form');
                return;
			} else {

				// check file types
				$file_type_pattern = self::$form_options['attach_types'];
				if ( $file_type_pattern == '' )
					$file_type_pattern = 'doc,docx,pdf,txt,gif,jpg,jpeg,png';
				$file_type_pattern = str_replace( ',', '|', self::$form_options['attach_types'] );
				$file_type_pattern = str_replace( ' ', '', $file_type_pattern );
				$file_type_pattern = trim( $file_type_pattern, '|' );
				$file_type_pattern = '(' . $file_type_pattern . ')';
				$file_type_pattern = '/\.' . $file_type_pattern . '$/i';

				if ( !preg_match( $file_type_pattern, $field_file['name'] ) ) {
					self::$form_errors[$slug] = __( 'Attachment file type not allowed.', 'si-contact-form' );
					return;
				}

				// check size
				$allowed_size = 1048576; // 1mb default
				if ( preg_match( '/^([[0-9.]+)([kKmM]?[bB])?$/', self::$form_options['attach_size'], $matches ) ) {
					$allowed_size = (int) $matches[1];
					$kbmb = strtolower( $matches[2] );
					if ( 'kb' == $kbmb ) {
						$allowed_size *= 1024;
					} elseif ( 'mb' == $kbmb ) {
						$allowed_size *= 1024 * 1024;
					}
				}
				if ( $field_file['size'] > $allowed_size ) {
					self::$form_errors[$slug] = __( 'Attachment file size is too large.', 'si-contact-form' );
					return;
				}

				$filename = $field_file['name'];

				// safer file names for scripts.
				if ( preg_match( '/\.(php|pl|py|rb|js|cgi)\d?$/', $filename ) )
					$filename .= '.txt';

				$filename = wp_unique_filename( FSCF_ATTACH_DIR, $filename );
				$new_file = trailingslashit( FSCF_ATTACH_DIR ) . $filename;

				if ( false === @move_uploaded_file( $field_file['tmp_name'], $new_file ) ) {
					self::$form_errors[$slug] = __( 'Attachment upload failed while moving file.', 'si-contact-form' );
					return;
				}

				// uploaded only readable for the owner process
				@chmod( $new_file, 0400 );
				self::$uploaded_files[$slug] = $new_file;

                self::$email_msg .= self::make_bold( $label ) . $inline_or_newline;
				self::$email_fields[$slug] =  __('File is attached:', 'si-contact-form') . ' ' . $filename;
				self::$email_msg .= ' ' . self::$email_fields[$slug] . self::$php_eol.self::$php_eol;
			} // end else (no errors)
		} else {
               if ( self::$form_options['email_hide_empty'] == 'true' ) {

               } else {
                       // no file was attached, and it was not required
                       self::$email_msg .= self::make_bold( $label ) . $inline_or_newline;
                       self::$email_fields[$slug] =  __('No file attached', 'si-contact-form');
			           self::$email_msg .= ' ' . __('No file attached', 'si-contact-form') . self::$php_eol.self::$php_eol;
               }
        }

	}  // end function validate_attach

	static function get_user_info() {
		// Gathers user info to include in the email message
		// Returns the user info string
		global $current_user, $user_ID;  // see if current WP user
		wp_get_current_user();

		// lookup country info for this ip
		// geoip lookup using Visitor Maps and Who's Online plugin
		$geo_loc = '';


		if ( file_exists( WP_PLUGIN_DIR . '/visitor-maps/include-whos-online-geoip.php' )
			&&	file_exists( WP_CONTENT_DIR .'/visitor-maps-geoip/GeoLiteCity.dat' ) ) {
			require_once(WP_PLUGIN_DIR . '/visitor-maps/include-whos-online-geoip.php');
			$gi = geoip_open_VMWO( WP_CONTENT_DIR .'/visitor-maps-geoip/GeoLiteCity.dat', VMWO_GEOIP_STANDARD );
			$record = geoip_record_by_addr_VMWO( $gi, $_SERVER['REMOTE_ADDR'] );
			geoip_close_VMWO( $gi );
			$li = array( );
			$li['city_name'] = (isset( $record->city )) ? $record->city : '';
			$li['state_name'] = (isset( $record->country_code ) && isset( $record->region )) ? $GEOIP_REGION_NAME[$record->country_code][$record->region] : '';
			$li['state_code'] = (isset( $record->region )) ? strtoupper( $record->region ) : '';
			$li['country_name'] = (isset( $record->country_name )) ? $record->country_name : '--';
			$li['country_code'] = (isset( $record->country_code )) ? strtoupper( $record->country_code ) : '--';
			$li['latitude'] = (isset( $record->latitude )) ? $record->latitude : '0';
			$li['longitude'] = (isset( $record->longitude )) ? $record->longitude : '0';
			if ( $li['city_name'] != '' ) {
				if ( $li['country_code'] == 'US' ) {
					$geo_loc = $li['city_name'];
					if ( $li['state_code'] != '' )
						$geo_loc = $li['city_name'] . ', ' . strtoupper( $li['state_code'] ) . self::$php_eol;
				} else {	  // all non us countries
					$geo_loc = $li['city_name'] . ', ' . strtoupper( $li['country_code'] ) . self::$php_eol;
				}
			} else {
				$geo_loc = '~ ' . $li['country_name'] . self::$php_eol;
			}
            $geo_loc .= 'http://maps.google.com/maps?q='. $li['latitude'] . ','. $li['longitude'];
		}
		// add some info about sender to the email message
		$userdomain = '';
		$userdomain = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
		$user_info_string = '';
        $user_info = array();
		if ( self::$form_options['email_html'] == 'true' )
			$user_info_string = '<div style="background:#eee;border:1px solid gray;color:gray;padding:1em;margin:1em 0;">';
		if ( $user_ID != '' ) {
			//user logged in
			if ( $current_user->user_login != '' )
				$user_info['wp_user'] = __( 'From a WordPress user', 'si-contact-form' ) . ': ' . $current_user->user_login;
			if ( $current_user->user_email != '' )
			   	$user_info['wp_user_email'] = __( 'User email', 'si-contact-form' ) . ': ' . $current_user->user_email;
			if ( $current_user->user_firstname != '' )
				$user_info['wp_user_first_name'] = __( 'User first name', 'si-contact-form' ) . ': ' . $current_user->user_firstname;
			if ( $current_user->user_lastname != '' )
				$user_info['wp_user_last_name'] = __( 'User last name', 'si-contact-form' ) . ': ' . $current_user->user_lastname;
			if ( $current_user->display_name != '' )
			    $user_info['wp_user_display_name'] = __( 'User display name', 'si-contact-form' ) . ': ' . $current_user->display_name;
		}
		$user_info['wp_user_ip'] = __( 'Sent from (ip address)', 'si-contact-form' ) . ': ' . esc_attr( $_SERVER['REMOTE_ADDR'] ) . " ($userdomain)";
		if ( $geo_loc != '' ) {
			$user_info['wp_user_location'] = __( 'Location', 'si-contact-form' ) . ': ' . $geo_loc;
			self::$form_data['sender_location'] = __( 'Location', 'si-contact-form' ) . ': ' . $geo_loc;
		}
	    $user_info['wp_user_date'] = __( 'Date/Time', 'si-contact-form' ) . ': ' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), current_time('timestamp') );
		$user_info['wp_user_referer'] = __( 'Coming from (referer)', 'si-contact-form' ) . ': ' . esc_url( self::$form_action_url );
		$user_info['wp_user_agent'] = __( 'Using (user agent)', 'si-contact-form' ) . ': ' . FSCF_Util::clean_input( $_SERVER['HTTP_USER_AGENT'] ) . self::$php_eol;

        // filter hook to allow modify $user_info array
        $user_info = apply_filters('si_contact_user_info', $user_info, self::$form_id_num);

        foreach ($user_info as $k => $v) {
                $user_info_string .= $v . self::$php_eol;
        }
  		if ( self::$form_options['email_html'] == 'true' )
			$user_info_string .= '</div>';

		return($user_info_string);
	}
	
	static function check_captcha() {
		// begin captcha check if enabled
		// captcha is optional but recommended to prevent spam bots from spamming your contact form

        $captcha_enabled = FSCF_Display::is_captcha_enabled(self::$form_id_num);

        $error_captcha_wrong = (self::$form_options['error_captcha_wrong'] != '') ? self::$form_options['error_captcha_wrong'] : __( 'That CAPTCHA was incorrect.', 'si-contact-form' );
        $error_captcha_blank = (self::$form_options['error_captcha_blank'] != '') ? self::$form_options['error_captcha_blank'] : __( 'Please complete the CAPTCHA.', 'si-contact-form' );
        $error_captcha_incorrect =  __( 'That CAPTCHA was incorrect. Try again.', 'si-contact-form' );

        if ( $captcha_enabled == 'recaptcha' ) {
		   // validate the recaptcha now

            if( isset( $_POST['g-recaptcha-response'] ) ) {
                 $ip = (isset( $_SERVER['REMOTE_ADDR'] )) ? $_SERVER['REMOTE_ADDR'] : '';
                 $result =  wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=".sanitize_text_field(urlencode(self::$global_options['recaptcha_secret_key']))."&response=" .sanitize_text_field(urlencode($_POST['g-recaptcha-response']))."&remoteip=" . sanitize_text_field(urlencode($ip)) );
               if ( is_wp_error( $result ) || empty( $result['body'] ) ) {
                   self::$form_errors['captcha']  =  __( 'Error connecting to the reCAPTCHA API host.', 'si-contact-form' );
                   if( WP_DEBUG === true )
                       self::$form_errors['captcha'] .=  ' ( '. $result->get_error_message() .' )';
			       return;
		       }
               $response = json_decode( wp_remote_retrieve_body( $result ), true );

               if( $response["success"] ) {
                   // ok, can continue
               } else {
                      // did not check the box?
                   	self::$form_errors['captcha'] = $error_captcha_wrong;
              }
            } else {
                    self::$form_errors['captcha'] = $error_captcha_blank;
            }


		} else if ( $captcha_enabled == 'sicaptcha'  ) {
                // validate the secure image captcha now
			$captcha_code = FSCF_Util::clean_input( $_POST['captcha_code'] );

			if ( self::$global_options['enable_php_sessions'] == 'true' ) { // this feature only works when PHP sessions are enabled
				//captcha with PHP sessions
				if ( !isset( $_SESSION['securimage_code_ctf_' . self::$form_id_num] ) || empty( $_SESSION['securimage_code_ctf_' . self::$form_id_num] ) ) {
					self::$form_errors['captcha'] = $error_captcha_incorrect;
				} else {
					if ( empty( $captcha_code ) ) {
						self::$form_errors['captcha'] = $error_captcha_blank;
					} else {
						require_once FSCF_CAPTCHA_PATH . '/securimage.php';
						$img = new Securimage_Captcha_ctf();
						$img->form_num = self::$form_id_num; // makes compatible with multi-forms on same page
						$valid = $img->check( "$captcha_code" );
						// has the right CAPTCHA code has been entered?
						if ( $valid == true ) {
							// ok can continue
						} else {
                             self::$form_errors['captcha'] = $error_captcha_wrong;

						}
					}
				}
			} else {
				//captcha without PHP sessions

				if ( empty( $captcha_code ) ) {
					self::$form_errors['captcha'] = $error_captcha_blank;
				} else if ( !isset( $_POST['fscf_captcha_prefix' . self::$form_id_num] ) || empty( $_POST['fscf_captcha_prefix' . self::$form_id_num] ) ) {
                    // this error means PHP session error, or they sat on the page more than 30 min
					self::$form_errors['captcha'] = $error_captcha_incorrect;
				} else {
					$prefix = 'xxxxxx';
					if ( isset( $_POST['fscf_captcha_prefix' . self::$form_id_num] ) && is_string( $_POST['fscf_captcha_prefix' . self::$form_id_num] ) && preg_match( '/^[a-zA-Z0-9]{15,17}$/', $_POST['fscf_captcha_prefix' . self::$form_id_num] ) ) {
						$prefix = $_POST['fscf_captcha_prefix' . self::$form_id_num];
					}
					if ( is_readable( FSCF_CAPTCHA_PATH . '/cache/' . $prefix . '.php' ) ) {
						include( FSCF_CAPTCHA_PATH . '/cache/' . $prefix . '.php' );
						// has the right CAPTCHA code has been entered?
						if ( 0 == strcasecmp( $captcha_code, $captcha_word ) ) {
							// captcha was matched
							@unlink( FSCF_CAPTCHA_PATH . '/cache/' . $prefix . '.php' );
							// ok can continue
						} else {
							self::$form_errors['captcha'] = $error_captcha_wrong;
						}
					} else {
                        // this error means cache read error, or they sat on the page more than 30 min
						self::$form_errors['captcha'] = $error_captcha_incorrect;
					}
				}
			}   // end if use PHP session
		}   // end if enable captcha
	}	// end function check_captcha()

	static function check_akismet() {
		$string = '';
		// Check with Akismet, but only if Akismet is installed, activated, and has a KEY. (Recommended for spam control).
		if ( self::$form_options['akismet_disable'] == 'false' ) { // per form disable feature
             // check if akismet is activated, version 2.x or 3.x
             if ( ( is_callable( array( 'Akismet', 'http_post' ) ) || function_exists( 'akismet_http_post' ) ) && get_option( 'wordpress_api_key' ) ) {
				global $akismet_api_host, $akismet_api_port;
				$c['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
				$c['user_agent'] = (isset( $_SERVER['HTTP_USER_AGENT'] )) ? $_SERVER['HTTP_USER_AGENT'] : '';
				$c['referrer'] = (isset( $_SERVER['HTTP_REFERER'] )) ? $_SERVER['HTTP_REFERER'] : '';
				$c['blog'] = get_option( 'home' );
				$c['blog_lang'] = get_locale(); // default 'en_US'
				$c['blog_charset'] = get_option( 'blog_charset' );
				$c['permalink'] = self::$form_action_url;
				$c['comment_type'] = 'contact-form';
                if ( ! empty(self::$email_fields['from_name']) )
				   $c['comment_author'] = self::$email_fields['from_name'];
			//$c['is_test']  = "1";  // uncomment this when testing spam detection
			//$c['comment_author']  = "viagra-test-123";  // uncomment this to test spam detection
				// or  You can just put viagra-test-123 as the name when testing the form (no need to edit this php file to test it)
				if ( ! empty(self::$email_fields['from_email']) )
					$c['comment_author_email'] = self::$email_fields['from_email'];
				$c['comment_content'] = self::$email_msg;
				$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );
				foreach ( $_SERVER as $key => $value ) {
					if ( !in_array( $key, $ignore ) && is_string( $value ) )
						$c["$key"] = $value;
					else
						$c["$key"] = '';
				}
				$query_string = '';
				foreach ( $c as $key => $data ) {
					if ( is_string( $data ) )
						$query_string .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';
				}
				//echo "test $akismet_api_host, $akismet_api_port, $query_string"; exit;

                if ( is_callable( array( 'Akismet', 'http_post' ) ) ) { // Akismet v3.0+
	                $response = Akismet::http_post( $query_string, 'comment-check' );
                } else {  // Akismet v2.xx
	                $response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );
	            }

				if ( 'true' == $response[1] ) {
					if ( self::$form_options['akismet_send_anyway'] == 'false' ) {
						self::$form_errors['akismet'] = (self::$form_options['error_input'] != '') ? self::$form_options['error_input'] : __( 'Invalid Input - Spam?', 'si-contact-form' );
						global $user_ID;
						if ( $user_ID != '' && current_user_can( 'manage_options' ) ) {
                            // this error only shown to WP admins
							// show administrator a helpful message
							self::$form_errors['akismet'] .= '<br />' . __( 'Akismet determined your message is spam. This can be caused by the message content, your email address, or your IP address being on the Akismet spam system. The administrator can turn off Akismet for the form on the form edit menu.', 'si-contact-form' );
						}
					} else {
						// Akismet says it is spam. flag the subject as spam and send anyway.
                        // XXX someday make these messages editable in settings
						self::$akismet_spam_subject = __( 'Akismet: Spam', 'si-contact-form' ) . ' - ';
						$string .=  __( 'Akismet Spam Check: probably spam', 'si-contact-form' ) . self::$php_eol;
						self::$email_fields['akismet'] = __( 'probably spam', 'si-contact-form' );
					}
				} else {
					$string .= __( 'Akismet Spam Check: passed', 'si-contact-form' ) . self::$php_eol;
					self::$email_fields['akismet'] = __( 'passed', 'si-contact-form' );
				}
            }
		}
		return($string);

	} // end function check_akismet()

  static function vcita_update_existing_user($params) {
    extract(self::vcita_get_contents("http://".$global_defaults['vcita_site']."/api/experts/".$params['vcita_uid']));
    
    return self::vcita_parse_user_info($params, $success, $raw_data);
  } // end function vcita_update_existing_user()

  static function vcita_create_or_validate_user($params, $global_defaults) {
    extract(self::vcita_post_contents("http://".$global_defaults['vcita_site']."/api/experts?".
                                       "email=".urlencode($params['vcita_email']).
                                       "&first_name=".urlencode($params['vcita_first_name'])."&last_name=".
                                       urlencode($params['vcita_last_name'])."&ref=wp-fscf&o=int.1"));

    return self::vcita_parse_user_info($params, $success, $raw_data);
  } // end function vcita_generate_or_validate_user()


  static function vcita_parse_user_info($params, $success, $raw_data) {
    // Parse the result from the vCita API.
    // Update all the parameters with the given values / error.
    
    if (!$success) {
      $params['vcita_last_error'] = "Temporary problem, please try again later";
    } else {
      $data = json_decode($raw_data);
      
      if ($data->{'success'} == 1) {
        $params['vcita_confirmed'] = $data->{'confirmed'};
        $params['vcita_last_error'] = "";
        if(isset($data->{'uid'}) && $data->{'uid'} != ""){
          $params['vcita_uid'] = $data->{'uid'};
        } else {
          $params['vcita_uid'] = $data->{'id'};
        }

	// Auth token for new users
	if(isset($data->{'auth_token'}) && $data->{'auth_token'} != ""){
          $params['auth_token'] = $data->{'auth_token'};
        }

        $params['vcita_first_name'] = $data->{'first_name'};
        $params['vcita_last_name'] = $data->{'last_name'};
      }
    }
    
    return $params;
  } // end function vcita_parse_user_info()

  static function vcita_disconnect_form($form_params) {
      //Disconnect the user from vCita by removing his details.
       $form_params['vcita_approved']    = 'false';
       $form_params['vcita_uid']         = '';
       $form_params['vcita_email']       = '';
       $form_params['vcita_first_name']  = '';
       $form_params['vcita_last_name']   = '';

       return $form_params;
  } // end function vcita_disconnect_form()

  static function vcita_post_contents($url) {
    // Perform an HTTP POST Call to retrieve the data for the required content.
    // @param $url
    // @return array - raw_data and a success flag
    $response  = wp_remote_post($url, array('header' => array('Accept' => 'application/json; charset=utf-8'),
                                            'timeout' => 10));

    return self::vcita_parse_response($response);
  } // end function vcita_post_contents()

  static function vcita_get_contents($url) {
      // Perform an HTTP GET Call to retrieve the data for the required content.
      // @param $url
      // @return array - raw_data and a success flag
      $response = wp_remote_get($url, array('header' => array('Accept' => 'application/json; charset=utf-8'),
                                            'timeout' => 10));

      return self::vcita_parse_response($response);
  } // end function vcita_get_contents()

  static function vcita_parse_response($response) {
      // Parse the HTTP response and return the data and if was successful or not.
      $success = false;
      $raw_data = "Unknown error";
      
      if (is_wp_error($response)) {
          $raw_data = $response->get_error_message();
      
      } elseif (!empty($response['response'])) {
          if ($response['response']['code'] != 200) {
              $raw_data = $response['response']['message'];
          } else {
              $success = true;
              $raw_data = $response['body'];
          }
      }
      
      return compact('raw_data', 'success');
  }  // end function vcita_parse_response()

  static function vcita_print_admin_page_notification($form_params, $global_options){
    if ($global_options['vcita_dismiss'] == 'false' && $form_params['vcita_scheduling_button'] == 'true' && $form_params['vcita_approved'] == 'false'){
      echo "<div id='si-fscf-vcita-warning' class='fsc-error'>Appointment booking by vCita has not been configured on the Scheduling tab yet&nbsp;<a href='".
      admin_url( 'options-general.php?page=si-contact-form/si-contact-form.php&fscf_form='. FSCF_Options::$current_form)."&fscf_tab=7'>Click to SETUP</a>, or <a href='#' onclick='document.getElementById(\"vcita_disable_button\").click();return false;'>Disable</a></div>";
    }
  }  // end function vcita_print_admin_page_notification()

  static function vcita_disable_init_msg($form_params, $global_options) {
   if ($global_options['vcita_initialized'] == 'true'){
      echo "<div class='scheduler_not_conected_note'>A confirmation email has been sent to ";
      echo $form_params['vcita_email'];
      echo " Please make sure you have received the email.</div><br />";
      $glob_options = FSCF_Util::get_global_options();
      $glob_options['vcita_initialized'] = 'false';
      update_option( 'fs_contact_global', $glob_options );
    }
    
  } // end function vcita_disable_init_msg()
	
	
	static function forbidifnewlines( $input ) {
		// check posted input for email injection attempts
		// Check for these common exploits
		// if you edit any of these do not break the syntax of the regex
		$input_expl = "/(<CR>|<LF>|\r|\n|%0a|%0d|content-type|mime-version|content-transfer-encoding|to:|bcc:|cc:|document.cookie|document.write|onmouse|onkey|onclick|onload)/i";
		// Loop through each POST'ed value and test if it contains one of the exploits fromn $input_expl:
		if ( is_string( $input ) ) {
			$v = strtolower( $input );
			$v = str_replace( 'donkey', '', $v ); // fixes invalid input with "donkey" in string
			$v = str_replace( 'monkey', '', $v ); // fixes invalid input with "monkey" in string
			if ( preg_match( $input_expl, $v ) ) {
                // XXX someday make these messages editable in settings
				wp_die( __( 'Illegal characters in POST. Possible email injection attempt', 'si-contact-form' ) );
			}
		}
	}  // end function forbidifnewlines


	static function spamcheckpost() {
		// helps spam protect the postaction
		// blocks contact form posted from other domains
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return __( 'Invalid User Agent', 'si-contact-form' );
		}

		// Make sure the form was indeed POST'ed:
		if ( ! $_SERVER['REQUEST_METHOD'] == "POST" ) {
			return __( 'Invalid POST', 'si-contact-form' );
		}

		// Make sure the form was posted from an approved host name.
		if ( self::$form_options['domain_protect'] == 'true' ) {
			$print_authHosts = '';
			$uri = parse_url( get_option( 'home' ) );
			$domain_arr = preg_replace( "/^www\./i", '', $uri['host'] );
            if ( !is_array( $domain_arr ) )
                $domain_arr = array( "$domain_arr" );

            // Additional allowed domain names(optional): from the form edit 'Security' tab
            $more_domains = explode("\n",trim(self::$form_options['domain_protect_names']));
            if ( !empty($more_domains) )
               $domain_arr = array_merge( $more_domains, $domain_arr );

			// Host names from where the form is authorized to be posted from:
			$domain_arr = array_map( 'strtolower', $domain_arr );
			foreach ( $domain_arr as $each_domain ) {
				$print_authHosts .= ', ' . $each_domain;
			}

			// Where have we been posted from?
			if ( isset( $_SERVER['HTTP_REFERER'] ) and trim( $_SERVER['HTTP_REFERER'] ) != '' ) {
				$fromArray = parse_url( strtolower( $_SERVER['HTTP_REFERER'] ) );
				$test_url = preg_replace( "/^www\./i", '', $fromArray['host'] );
				if ( ! in_array( $test_url , $domain_arr )  )
					return sprintf( __( 'Invalid HTTP_REFERER domain. The domain name posted from does not match the allowed domain names from the form edit Security tab: %s', 'si-contact-form' ), $print_authHosts );
			}
		} // end if domain protect

		// check posted input for email injection attempts
		// Check for these common exploits
		// if you edit any of these do not break the syntax of the regex
		$input_expl = "/(%0a|%0d)/i";
		// Loop through each POST'ed value and test if it contains one of the exploits fromn $input_expl:
		foreach ( $_POST as $k => $v ) {
			if ( is_string( $v ) ) {
				$v = strtolower( $v );
				$v = str_replace( 'donkey', '', $v ); // fixes invalid input with "donkey" in string
				$v = str_replace( 'monkey', '', $v ); // fixes invalid input with "monkey" in string
				if ( preg_match( $input_expl, $v ) ) {
                  // XXX someday make these messages editable in settings
					return __( 'Illegal characters in POST. Possible email injection attempt', 'si-contact-form' );
				}
			}
		}

		return 0;
	}  // end function spamcheckpost

	static function prepare_email() {
		// Send the email, etc.

		self::$email_fields['full_message'] = self::$email_msg;

		if ( 'true' == self::$form_options['email_html'] ) {
			self::$email_msg = str_replace( array( "\r\n", "\r", "\n" ), "<br>", self::$email_msg );
			self::$email_msg = '<html><body>' . self::$php_eol . self::$email_msg . '</body></html>' . self::$php_eol;
		}

		// wordwrap email message
		self::$email_msg = wordwrap( self::$email_msg, 70, self::$php_eol );

		// See if the email should be sent
		$email_off = 0;
		if ( self::$form_options['redirect_enable'] == 'true' && self::$form_options['redirect_query'] == 'true' && self::$form_options['redirect_email_off'] == 'true' )
			$email_off = 1;
		if ( self::$form_options['export_email_off'] == 'true' )
			$email_off = 1;
		if ( self::$form_options['silent_send'] != 'off' && self::$form_options['silent_email_off'] == 'true' )
			$email_off = 1;

			$ctf_email_on_this_domain = self::$form_options['email_from']; // optional

			// ***** Prepare the email header *****

			// *** From name and email
			$no_name = 0;
            if ( empty(self::$email_fields['from_name']) ) {
                    // if name field was disabled, the from name will be ~ because we don't know the users name.
                    self::$email_header['from_name'] = '~';
                    self::$email_fields['from_name'] = '~';
					$no_name = 1;
            } else {
                    self::$email_header['from_name'] = self::$email_fields['from_name'];
            }
            $no_email = 0;
            if ( empty(self::$email_fields['from_email']) ) {
            // if email field was disabled, the from email will be admin email because we don't know the users email.
                   self::$email_header['from_email'] = get_option( 'admin_email' );
                   self::$email_fields['from_email'] = get_option( 'admin_email' );
				   $no_email = 1;
            } else {
                   self::$email_header['from_email'] = self::$email_fields['from_email'];
            }

			if ( !empty($ctf_email_on_this_domain) ) { // Set the Return-path as specified in settings
				if ( !preg_match( "/,/", $ctf_email_on_this_domain ) ) {
					// just an email: user1@example.com
					self::$email_fields['mail_sender'] = $ctf_email_on_this_domain;
					if ( self::$form_options['email_from_enforced'] == 'true' )
						self::$email_header['from_email'] = $ctf_email_on_this_domain;
				} else {
					// name and email: webmaster,user1@example.com
					list($key, $value) = explode( ",", $ctf_email_on_this_domain );
					$key = trim( $key );
					$value = trim( $value );
					self::$email_fields['mail_sender'] = $value;
					if ( $no_name )
						self::$email_header['from_name'] = $key;
					if ( $no_email || self::$form_options['email_from_enforced'] == 'true' )
						self::$email_header['from_email'] = $value;
				}
			}

            // hook for modifying the from_name and from_email
            self::$email_fields['from_name'] = apply_filters('si_contact_from_name', self::$email_fields['from_name'], self::$form_id_num);
            self::$email_fields['from_email'] = apply_filters('si_contact_from_email', self::$email_fields['from_email'], self::$form_id_num);

		if ( ! $email_off ) {
           if ( $no_name && self::$email_header['from_name'] == '~' )  // they had no name field
		       $header_php = 'From: ' . self::$email_header['from_email'] . ' <' . self::$email_header['from_email'] .">\n"; // header for php mail only
           else
		       $header_php = 'From: ' . self::$email_header['from_name'] . ' <' . self::$email_header['from_email'] .">\n"; // header for php mail only

			$header = '';  // for php mail and wp_mail

			// *** To name and email, including cc and bcc
			// process $mail_to user1@example.com,[cc]user2@example.com,[cc]user3@example.com,[bcc]user4@example.com,[bcc]user5@example.com
			// some are cc, some are bcc
			$mail_to_arr = explode( ',', self::$email_fields['email_to'] );
			self::$email_fields['email_to'] = trim( $mail_to_arr[0] );
			unset( $mail_to_arr[0] );
			$ctf_email_address_cc = '';
            // XXX add send a copy feature?
			//This is a bit of a hack, but it will send a carbon copy to the sender:
            // $ctf_email_address_cc .= "$email,";
			$ctf_email_address_bcc = self::$form_options['email_bcc'];
			if ( $ctf_email_address_bcc != '' )
				$ctf_email_address_bcc = $ctf_email_address_bcc . ',';
			foreach ( $mail_to_arr as $key => $this_mail_to ) {
				if ( preg_match( "/\[bcc\]/i", $this_mail_to ) ) {
					$this_mail_to = str_replace( '[bcc]', '', $this_mail_to );
					$ctf_email_address_bcc .= "$this_mail_to,";
				} else {
					$this_mail_to = str_replace( '[cc]', '', $this_mail_to );
					$ctf_email_address_cc .= "$this_mail_to,";
				}
			}
			if ( !empty($ctf_email_address_cc) ) {
				$ctf_email_address_cc = rtrim( $ctf_email_address_cc, ',' );
				$header .= "Cc: $ctf_email_address_cc\n";
			}
			if ( !empty($ctf_email_address_bcc) ) {
				$ctf_email_address_bcc = rtrim( $ctf_email_address_bcc, ',' );
				$header .= "Bcc: $ctf_email_address_bcc\n";
			}

			// *** Reply to and X-Sender
			if ( !empty(self::$form_options['email_reply_to']) ) { // custom reply_to
				$header .= 'Reply-To: ' . self::$form_options['email_reply_to'] . "\n";
			} else {
				$header .= 'Reply-To: ' . self::$email_fields['from_email'] . "\n";
			}
			if ( $ctf_email_on_this_domain != '' ) {
                // Return-path address setting
				$header .= 'X-Sender: ' . self::$email_fields['mail_sender'] . "\n";
				$header .= 'Return-Path: ' . self::$email_fields['mail_sender'] . "\n";
			}

			// *** Email Subject
			// subject can include posted data names feature:
            if (self::$selected_subject != '')
                $subj = self::$form_options['email_subject'] . ' ' . self::$selected_subject; // came from a select field
			else if ( isset(self::$form_data['subject']) && self::$form_data['subject'] != '' )
				$subj = self::$form_options['email_subject'] . ' ' . self::$form_data['subject']; // came from text field
			else
				$subj = self::$form_options['email_subject'];  // was not required, use the options

			foreach ( self::$email_fields as $key => $data ) {
				if ( in_array( $key, array( 'message', 'full_message', 'akismet' ) ) )  // disallow these
					continue;
				if ( is_string( $data ) )
					$subj = str_replace( '[' . $key . ']', $data, $subj );
			}

			$posted_form_name = ( self::$form_options['form_name'] != '' ) ? self::$form_options['form_name'] : sprintf( __( 'Form: %d', 'si-contact-form' ), self::$form_id_num );
			$subj = str_replace( '[form_label]', $posted_form_name, $subj );
            // filter hook for modifying the email subject(great for adding a ticket number)
            $subj = apply_filters('si_contact_email_subject', $subj, self::$form_id_num);
            if ( !empty(self::$akismet_spam_subject) ) // Akismet: Spam -
			    $subj = self::$akismet_spam_subject . $subj;
            self::$email_fields['subject'] = $subj;

			// ***** Send the email *****
		 
			// Send html email?
			if ( self::$form_options['email_html'] == 'true' ) {
				$header .= 'Content-type: text/html; charset=' . get_option( 'blog_charset' ) . self::$php_eol;
			} else {
				$header .= 'Content-type: text/plain; charset=' . get_option( 'blog_charset' ) . self::$php_eol;
			}

            // filter hook for modifying the email header
            $header  = apply_filters('si_contact_email_header', $header, self::$form_id_num);

            // not sure if this is needed because the From: header is always set
			//@ini_set( 'sendmail_from', self::$email_fields['from_email'] );

          /*  print $header;
             print '----';
             print $header_php;
            exit;*/

			if ( self::$form_options['php_mailer_enable'] == 'php' ) {
				// sending with php mail
				$header_php .= $header;

				if ( $ctf_email_on_this_domain != '' && FSCF_Util::fsc_is_shell_safe(self::$email_fields['mail_sender']) ) {
					// Pass the Return-Path via sendmail's -f command.
					@mail( self::$email_fields['email_to'], self::$email_fields['subject'], self::$email_msg, $header_php, '-f ' . self::$email_fields['mail_sender'] );
				} else {
					// the fifth parameter , don't use it if Return-path address not set
					@mail( self::$email_fields['email_to'], self::$email_fields['subject'], self::$email_msg, $header_php );
				}
			} else {
				// sending with wp_mail
                if ( $no_name && self::$email_header['from_name'] == '~' )
                    self::$email_set_wp['from_name'] = self::$email_header['from_email']; // they had no name field
                else
                    self::$email_set_wp['from_name'] = self::$email_header['from_name'];
                add_filter( 'wp_mail_from_name', 'FSCF_Process::set_wp_from_name', 1 );

                self::$email_set_wp['from_email'] = self::$email_header['from_email'];
				add_filter( 'wp_mail_from', 'FSCF_Process::set_wp_from_email', 1 );

				if ( $ctf_email_on_this_domain != '' ) {
					// Add an action on phpmailer_init to add Sender $this->si_contact_mail_sender for Return-path in wp_mail
					// this helps spf checking when the Sender email address matches the site domain name
                     self::$email_set_wp['mail_sender'] = self::$email_fields['mail_sender'];
					add_action( 'phpmailer_init', 'FSCF_Process::set_wp_mail_sender', 1 );
				}
                $send_attachments = true;
                //filter hook to disable emailing the attachment (useful for when email_keep_attachments is checked and you don't want it emailed).
                $send_attachments= apply_filters( 'si_contact_send_attachments', $send_attachments,  self::$form_id_num);

				if ( self::$uploaded_files && $send_attachments ) {
					$attach_this_mail = array( );
					foreach ( self::$uploaded_files as $path ) {
						$attach_this_mail[] = $path;
					}
					@wp_mail( self::$email_fields['email_to'], self::$email_fields['subject'], self::$email_msg, $header, $attach_this_mail );
				} else {
                    //echo 'header:'.$header.'<br>'.'to:'.self::$email_fields['email_to'].'<br>'.'subject:'.self::$email_fields['subject'].'<br>'.'message:'.self::$email_msg.'<br>';
          		@wp_mail( self::$email_fields['email_to'], self::$email_fields['subject'], self::$email_msg, $header );
				}
			}
		} // end if (!$email_off) {
		
		// Confirmation email (used to be called "autoresponder")
		if ( self::$form_options['auto_respond_enable'] == 'true' &&
        !$no_email && // do not send not when email field is disabled
        !empty(self::$form_options['auto_respond_subject']) &&
        !empty(self::$form_options['auto_respond_message']) ) {
			$subj = self::$form_options['auto_respond_subject'];
			$msg = self::$form_options['auto_respond_message'];

			// self::$email_fields is an array of the form name value pairs
			// autoresponder can include posted data, tags are set on form settings page
			foreach ( self::$email_fields as $key => $data ) {
				if ( in_array( $key, array( 'message', 'full_message', 'akismet' ) ) )  // disallow these
					continue;
				if ( is_string( $data ) ) {
					$subj = str_replace( '[' . $key . ']', $data, $subj );
					$msg = str_replace( '[' . $key . ']', $data, $msg );
				}
			}

			$subj = str_replace( '[form_label]', $posted_form_name, $subj );

			// Remove all empty field tags unmatched in posted data (for the fields not required)
           	self::set_tags_array();
            foreach ( self::$av_tags_arr as $i ) {
              $msg = str_replace( '[' . $i . "]\r\n", '', $msg );
			  $msg = str_replace( '[' . $i . ']', '', $msg );
		    }

            foreach ( self::$av_tags_subj_arr as $i ) {
			  $subj = str_replace( '[' . $i . ']', '', $subj );
		    }
            // filter hook for modifying the autoresponder email subject(great for adding a ticket number)
            $subj = apply_filters('si_contact_autoresp_email_subject', $subj, self::$form_id_num);

			// wordwrap email message
			$msg = wordwrap( $msg, 70, self::$php_eol );

			$header = '';
			$header_php = '';

			// Prepare the email header
			$header_php = 'From: ' . self::$form_options['auto_respond_from_name'] . ' <' . self::$form_options['auto_respond_from_email'] . ">\n";

			$header .= 'Reply-To: ' . self::$form_options['auto_respond_reply_to'] . "\n";

		   //	$header .= 'X-Sender: ' . self::$form_options['auto_respond_from_email'] . "\n";
		   //	$header .= 'Return-Path: ' . self::$form_options['auto_respond_from_email'] . "\n";

			if ( $ctf_email_on_this_domain != '' ) {
                //  Return-path address setting
				$header .= 'X-Sender: ' . self::$email_fields['mail_sender'] . "\n";
				$header .= 'Return-Path: ' . self::$email_fields['mail_sender'] . "\n";
			}

			if ( self::$form_options['auto_respond_html'] == 'true' ) {
				$header .= 'Content-type: text/html; charset=' . get_option( 'blog_charset' ) . self::$php_eol;
			} else {
				$header .= 'Content-type: text/plain; charset=' . get_option( 'blog_charset' ) . self::$php_eol;
			}

            // XXX some of this duplicate code.  someday could make into send_email function

            // XXX 09/01/2013 not sure if this is needed because the From: header is always set
			//@ini_set( 'sendmail_from', self::$form_options['auto_respond_from_email'] );

			if ( self::$form_options['php_mailer_enable'] == 'php' ) {
				// autoresponder sending with php
				$header_php .= $header;
				if ( $ctf_email_on_this_domain != '' && FSCF_Util::fsc_is_shell_safe(self::$email_fields['mail_sender'])  ) {
					// Pass the Return-Path via sendmail's -f command.
					@mail( self::$email_fields['from_email'], $subj, $msg, $header_php, '-f ' . self::$email_fields['mail_sender'] );
				} else {
					// the fifth parameter, don't use it if Return-path address not set
					@mail( self::$email_fields['from_email'], $subj, $msg, $header_php );
				}
			} else {
				// autoresponder sending with wp_mail
                self::$email_set_wp['from_name'] = self::$form_options['auto_respond_from_name'];
				add_filter( 'wp_mail_from_name', 'FSCF_Process::set_wp_from_name', 1 );

                self::$email_set_wp['from_email'] = self::$form_options['auto_respond_from_email'];
                add_filter( 'wp_mail_from', 'FSCF_Process::set_wp_from_email', 1 );

				@wp_mail( self::$email_fields['from_email'], $subj, $msg, $header );
			}
		}  // end if confirmation email (autoresponder)

       // added optional condition for silent send
       $silent_ok = 1;
       if ( !empty(self::$form_options['silent_conditional_field']) && !empty(self::$form_options['silent_conditional_value']) ) {
           if ( isset(self::$email_fields[self::$form_options['silent_conditional_field']]) && self::$email_fields[self::$form_options['silent_conditional_field']] == self::$form_options['silent_conditional_value'] )
             $silent_ok = 1;
           else
             $silent_ok = 0;
       }

         // filter hook for modifying the email_fields array
        self::$email_fields = apply_filters('si_contact_email_fields_posted', self::$email_fields, self::$form_id_num);

		// Silent sending?
		if ( self::$form_options['silent_send'] == 'get' && !empty(self::$form_options['silent_url']) && $silent_ok ) {
			// build query string
			$query_string = self::export_convert( self::$email_fields, self::$form_options['silent_rename'], self::$form_options['silent_ignore'], self::$form_options['silent_add'], 'query' );
            //echo $query_string;
			if ( !preg_match( "/\?/", self::$form_options['silent_url'] ) )
				$silent_result = wp_remote_get( self::$form_options['silent_url'] . '?' . $query_string, array( 'timeout'	 => 20, 'sslverify'	 => false ) );
			else
				$silent_result = wp_remote_get( self::$form_options['silent_url'] . '&' . $query_string, array( 'timeout'	 => 20, 'sslverify'	 => false ) );
			if ( !is_wp_error( $silent_result ) ) {
				$silent_result = wp_remote_retrieve_body( $silent_result );
			}
		   //	print_r($silent_result);
		}

		if ( self::$form_options['silent_send'] == 'post' && !empty(self::$form_options['silent_url']) && $silent_ok ) {
			// build post_array
			$post_array = self::export_convert( self::$email_fields, self::$form_options['silent_rename'], self::$form_options['silent_ignore'], self::$form_options['silent_add'], 'array' );
			$silent_result = wp_remote_post( self::$form_options['silent_url'], array( 'body'		 => $post_array, 'timeout'	 => 20, 'sslverify'	 => false ) );
			if ( !is_wp_error( $silent_result ) ) {
				$silent_result = wp_remote_retrieve_body( $silent_result );
			}
		   //	print_r($silent_result);
		}

		// Export option
		// filter posted data based on admin settings
		$posted_data_export = self::export_convert( self::$email_fields, self::$form_options['export_rename'], self::$form_options['export_ignore'], self::$form_options['export_add'], 'array' );
		// hook for other plugins to use (just after message posted)
		$fsctf_posted_data = (object) array( 'form_number' => self::$form_id_num, 'title' => self::$form_options['form_name'], 'posted_data' => $posted_data_export, 'uploaded_files' => (array) self::$uploaded_files );
		do_action_ref_array( 'fsctf_mail_sent', array( &$fsctf_posted_data ) );


	}  // end function prepare_email()

	static function set_wp_from_email() { // used in function prepare_email
		return self::$email_set_wp['from_email'];
	}

	static function set_wp_from_name() {  // used in function prepare_email
		return self::$email_set_wp['from_name'];
	}

	static function set_wp_mail_sender( $phpmailer ) {  // used in function prepare_email
		// add Sender for Return-path to wp_mail
		$phpmailer->Sender = self::$email_set_wp['mail_sender'];
	}


 	static function email_sent_cleanup_attachments() {
           // clean up the attachment directory after email sent

        if ( ! empty(self::$uploaded_files) ) {
            // unlink attachment temp files individually
            foreach ( (array) self::$uploaded_files as $path ) {
               @unlink( $path );
            }
            // full directory sweep cleanup
            //self::clean_temp_dir( FSCF_ATTACH_DIR, 3 );
        }
   }

	static function email_sent_redirect() {
		// displays thank you after email is sent

		// Redirct after email sent?
		self::$redirect_enable = 'false';

		if ( self::$form_options['redirect_enable'] == 'true' ) {
			self::$redirect_enable = 'true';
			$ctf_redirect_url = self::$form_options['redirect_url'];
		}
		// allow shortcode redirect to override options redirect settings
        if ( self::$global_options['enable_php_sessions'] == 'true' &&  // this feature only works when PHP sessions are enabled
			$_SESSION['fsc_shortcode_redirect_' . self::$form_id_num] != '' ) {
			self::$redirect_enable = 'true';
			$ctf_redirect_url = strip_tags( $_SESSION['fsc_shortcode_redirect_' . self::$form_id_num] );
		}
		if ( self::$redirect_enable == 'true' ) {
			if ( $ctf_redirect_url == '#' ) {  // if you put # for the redirect URL it will redirect to the same page the form is on regardless of the page.
				$ctf_redirect_url = self::$form_action_url;
			}
            // filter hook for changing the redirect URL. You could make a function that changes it based on fields
            $ctf_redirect_url = apply_filters( 'si_contact_redirect_url', $ctf_redirect_url, self::$email_fields,  self::$form_data['mailto_id'], self::$form_id_num);

			// redirect query string code
			if ( self::$form_options['redirect_query'] == 'true' ) {
				// build query string
				$query_string = self::export_convert( self::$email_fields, self::$form_options['redirect_rename'], self::$form_options['redirect_ignore'], self::$form_options['redirect_add'], 'query' );
				if ( !preg_match( "/\?/", $ctf_redirect_url ) )
					$ctf_redirect_url .= '?' . $query_string;
				else
					$ctf_redirect_url .= '&' . $query_string;
			}
			$ctf_redirect_timeout = absint(self::$form_options['redirect_seconds']); // time in seconds to wait before loading another Web page
                   // echo $ctf_redirect_url; exit;
            if ($ctf_redirect_timeout == 0 ) {
               // use wp_redirect when timeout seconds is 0.
               // So now if you set the timeout to 0 seconds, then post the form, it gets instantly redirected to the redirect URL
               // and you are responsible to display the "your message has been sent, thank you" message there.
               //wp_redirect( $ctf_redirect_url );
               header("Location: $ctf_redirect_url");
		       exit;
           }

			// meta refresh page timer feature
            // allows some seconds to to display the "your message has been sent, thank you" message.
            // note $ctf_redirect_url query_string is already url encoded
			self::$meta_string = "<meta http-equiv=\"refresh\" content=\"$ctf_redirect_timeout;URL=".$ctf_redirect_url."\">\n";
			if (is_admin())
				add_action('admin_head', 'FSCF_Process::meta_refresh',1);
			else
				add_action('wp_head', 'FSCF_Process::meta_refresh',1);

		} // end if (self::$redirect_enable == 'true')

	}  // end function email_sent_cleanup
	
	static function meta_refresh() {
		echo self::$meta_string;
	}
	
	static function export_convert( $posted_data, $rename, $ignore, $add, $return = 'array' ) {
		$query_string = '';
		$posted_data_export = array( );
		//rename field names array
		$rename_fields = array( );
		$rename_fields_test = explode( "\n", $rename );
		if ( !empty( $rename_fields_test ) ) {
			foreach ( $rename_fields_test as $line ) {
				if ( preg_match( "/=/", $line ) ) {
					list($key, $value) = explode( "=", $line );
					$key = trim( $key );
					$value = trim( $value );
					if ( $key != '' && $value != '' )
						$rename_fields[$key] = $value;
				}
			}
		}
		// add fields
		$add_fields_test = explode( "\n", $add );
		if ( !empty( $add_fields_test ) ) {
			foreach ( $add_fields_test as $line ) {
				if ( preg_match( "/=/", $line ) ) {
					list($key, $value) = explode( "=", $line );
					$key = trim( $key );
					$value = trim( $value );
					if ( $key != '' && $value != '' ) {
						if ( $return == 'array' )
							$posted_data_export[$key] = $value;
						else
							$query_string .= $key . '=' . urlencode( stripslashes( $value ) ) . '&';
					}
				}
			}
		}
		//ignore field names array
		$ignore_fields = array( );
		$ignore_fields = array_map( 'trim', explode( "\n", $ignore ) );
		// $posted_data is an array of the form name value pairs
		foreach ( $posted_data as $key => $value ) {
			if ( is_string( $value ) ) {
				if ( in_array( $key, $ignore_fields ) )
					continue;
				$key = ( isset( $rename_fields[$key] ) ) ? $rename_fields[$key] : $key;
				if ( $return == 'array' )
					$posted_data_export[$key] = $value;
				else
					$query_string .= $key . '=' . urlencode( stripslashes( $value ) ) . '&';
			}
		}
		if ( $return == 'array' )
			return $posted_data_export;
		else
			return substr($query_string, 0, -1); // remove & on end
	}  // end function export_convert


	static function clean_temp_dir( $dir, $minutes = 30 ) {
		// needed for emptying temp directories for attachments
		// garbage collection    // deletes all files over xx minutes old in a temp directory
		if ( !is_dir( $dir ) || !is_readable( $dir ) || !is_writable( $dir ) )
			return false;

		$count = 0;
		$list = array( );
		$handle = @opendir( $dir );
		if ( $handle  ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file == '.' || $file == '..' || $file == '.htaccess' || $file == 'index.php' )
					continue;

				$stat = @stat( $dir . $file );
				if ( ( $stat['mtime'] + $minutes * 60 ) < time() ) {
					@unlink( $dir . $file );
					$count += 1;
				} else {
					$list[$stat['mtime']] = $file;
				}
			}
			closedir( $handle );
			// purge xx amount of files based on age to limit a DOS flood attempt. Oldest ones first, limit 500
			if ( isset( $list ) && count( $list ) > 499 ) {
				ksort( $list );
				$ct = 1;
				foreach ( $list as $k => $v ) {
					if ( $ct > 499 )
						@unlink( $dir . $v );
					$ct += 1;
				}
			}
		}
		return $count;
	}  // end function clean_temp_dir

	static function make_bold( $label ) {
		// makes bold html email labels
		if ( self::$form_options['email_html'] == 'true' )
			return '<b>' . $label . '</b>';
		else
			return $label;
	}


   	static function set_tags_array() {
		// Set up the list of available tags for email

		self::$av_tags_arr  = array();  // used to show available field tags this form
		self::$av_tags_subj_arr  = array();  // used to show available field tags for this form subject

		// Fields
		foreach ( self::$form_options['fields'] as $key => $field ) {
			switch ($field['standard']) {
				case FSCF_NAME_FIELD :
					if ($field['disable'] == 'false') {
					   switch (self::$form_options['name_format']) {
						  case 'name':
							 self::$av_tags_arr[] = 'from_name';
						  break;
						  case 'first_last':
							 self::$av_tags_arr[] = 'first_name';
							 self::$av_tags_arr[] = 'last_name';
						  break;
						  case 'first_middle_i_last':
							 self::$av_tags_arr[] = 'first_name';
							 self::$av_tags_arr[] = 'middle_initial';
							 self::$av_tags_arr[] = 'last_name';
						  break;
						  case 'first_middle_last':
							 self::$av_tags_arr[] = 'first_name';
							 self::$av_tags_arr[] = 'middle_name';
							 self::$av_tags_arr[] = 'last_name';
						  break;
					   }
					}
					break;

				case FSCF_EMAIL_FIELD :
					// email
					if ($field['disable'] == 'false')
						self::$av_tags_arr[] = 'from_email';
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
							self::$av_tags_arr[] = $field['slug'];
						} else { // text, textarea, date, password, email, url, hidden, time, select, select-multiple, radio, checkbox, checkbox-multiple
							self::$av_tags_arr[] = $field['slug'];
						}
					}
			}	// end switch
		}	// end foreach

		self::$av_tags_subj_arr = self::$av_tags_arr;
		self::$av_tags_arr[] = 'subject';
		if (self::$form_options['fields'][$msg_key]['disable'] == 'false')
		   self::$av_tags_arr[] = 'message';

		self::$av_tags_arr[] = 'full_message';
		if ( function_exists('akismet_verify_key') && self::$form_options['akismet_disable'] == 'false' )
		   self::$av_tags_arr[] = 'akismet';

		self::$av_tags_arr[] = 'date_time';
        self::$av_tags_arr[] = 'ip_address';
		self::$av_tags_subj_arr[] = 'form_label';

	}	// function set_tags_array()

}  // end class FSCF_Process

// end of file
