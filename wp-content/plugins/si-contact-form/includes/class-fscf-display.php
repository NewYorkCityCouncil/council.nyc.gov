<?php

/**
 * Description of class-fscf-display
 * Display class to display the contact form on user website.
 * Functions are called statically, so no need to instantiate the class
 * @authors Mike Challis and Ken Carlson
 */

class FSCF_Display {

	static $global_options, $form_options;
	static $form_content = array();  // Sanitized content submitted by contact form POST of from defaults
	static $form_errors = array();	// error messages from form processing (key = field name)
	static $style = array();		// styles for formatting the form
	static $ext_css;		// styles for formatting the form
	static $contact_error;
	static $form_id_num, $placeholder;
	static $email_msg_print, $contacts;
	static $req_field_ind, $ctf_field_size, $form_action_url, $aria_required;
	static $have_attach = '';
	static $printed_tooltip_filetypes, $fscf_use_window_onload;
	static $add_fscf_script, $add_placeholder_script, $add_date_js_array, $add_date_js, $add_recaptcha_js, $add_recaptcha_js_array, $add_recaptcha_script;

	static function process_short_code($atts) {
		// Process shortcode and display the form
		// and decide whether to send the email or not

        self::$add_fscf_script = true;  // condition flag for add styles and scripts to page or post the form is on

		// Extract shortcode atts
		extract( shortcode_atts( array(
					'form'		 => '',
					'redirect'	 => '',
					'hidden'	 => '',
					'email_to'	 => '',
					), $atts ) );

		// Verify form number
		self::$global_options = FSCF_Util::get_global_options();

		self::$form_id_num = '1';
		if ( isset($form) && is_numeric($form) ) {
		   self::$form_id_num = (int)$form;
		} else {
           echo __( 'Contact Form Shortcode Error: Invalid form number in shortcode.', 'si-contact-form' );
           return;
        }

		$frm_id = self::$form_id_num;

		// Get the form options
		self::$form_options = FSCF_Util::get_form_options( self::$form_id_num, false );  // Don't use defaults if it doesn't exist

		if ( ! self::$form_options ) {
			// Form does not exist in options table
			// Display error message and return
            echo  sprintf( __( 'Contact Form Shortcode Error: Form %s does not exist', 'si-contact-form' ), self::$form_id_num );
            return;
		}

		// Update some language
		// The update_lang function receives the array by reference, so it can be changed
		FSCF_Util::update_lang(self::$form_options);

		// Store shortcode atts
		// http://www.fastsecurecontactform.com/shortcode-options
        if (self::$global_options['enable_php_sessions'] == 'true') { // this feature only works when PHP sessions are enabled
			$_SESSION["fsc_shortcode_redirect_$frm_id"] = $redirect;
			$_SESSION["fsc_shortcode_hidden_$frm_id"] = $hidden;
			$_SESSION["fsc_shortcode_email_to_$frm_id"] = $email_to;
        } else {
           if ( !empty($redirect) || !empty($hidden) || !empty($email_to)) {
            // trying to use shorcode attributes with the required PHP sessions setting turn off
            // Display error message and return
            echo  __( 'Contact Form Shortcode Error: Using shorcode attributes requires the PHP sessions setting to be enabled on the Advanced tab in form settings.', 'si-contact-form' );
            return;
           }
        }

		self::$form_action_url = self::get_form_action_url();

		// initialize vars
		self::$contact_error = 0;

		// Save parameters from query string, if any
		self::get_query_parms();

        // initialize external css
        if (self::$form_options['external_style'] == 'true')
          self::get_ext_css();

        // Has a preview been selected?
		$preview = ( isset($_POST['ctf_action']) && __('Preview Form', 'si-contact-form') == $_POST['ctf_action'] ) ? true : false;
        if (is_admin() && $preview && self::$form_options['external_style'] == 'true')
			self::external_style_head();

		self::$req_field_ind = ( self::$form_options['req_field_indicator_enable'] == 'true' ) ? '<span '.self::get_this_css('required_style').'>'.self::$form_options['req_field_indicator'] . '</span>' : '';

		// See if a form has been processed, and if so, if there were errors
		if ( FSCF_Process::$form_processed && FSCF_Process::$form_id_num == self::$form_id_num  && empty(FSCF_Process::$form_errors) ) {
			// Form was processed and has no errors--display thank you message
			$string = self::display_thank_you();
		} else {
            if (!isset(self::$add_date_js)) {
                 self::$add_date_js_array = array();
                 self::$add_date_js = '';
            }
            if (!isset(self::$add_recaptcha_js)) {
                 self::$add_recaptcha_js_array = array();
                 self::$add_recaptcha_js = '';
            }
			if ( ! empty(FSCF_Process::$form_errors) && FSCF_Process::$form_id_num == self::$form_id_num ) {
				// The form was processed, but had errors
				if ( ! empty(FSCF_Process::$form_data) ) {
					// If this is not true, there is an internal error...
					self::$form_content = array_merge( self::$form_content, FSCF_Process::$form_data );
					self::$form_errors  = array_merge( self::$form_errors, FSCF_Process::$form_errors );
					// XXX later, improve variable usage for error tracking?
					self::$contact_error = true;
				}
			}
			// ***** Display the Form *****
            self::$placeholder = 0;
            self::$add_recaptcha_script = 0;
            $string = "\n\n<!-- Fast Secure Contact Form plugin " . FSCF_VERSION . " - begin - FastSecureContactForm.com -->
<div ".self::get_this_css('clear_style')."></div>\n" . self::$form_options['welcome'];
			$string = self::display_form($string);
		}

		return($string);
	}	// end function process_short_code()

    static function get_this_css($tag){
       // returns the correct css, inline or external css
       return $this_style = ( self::$form_options['external_style'] == 'true' ) ? 'class="'.self::$ext_css[$tag] .'"' : self::convert_css( self::$form_options[$tag]);

     }

  static function external_style_head() {
     // puts style on form preview when external css is enabled
  wp_enqueue_script('jquery');
?>
<script type="text/javascript">
//<![CDATA[
var fscf_styles = "\n\
<style type='text/css'>\n\
/*-----------[Fast Secure Contact Form]-----------*/\n\
/* Alignment DIVs */\n\
.fscf-div-form              { <?php echo self::$form_options['form_style']; ?> }\n\
.fscf-div-left-box          { <?php echo self::$form_options['left_box_style']; ?> }\n\
.fscf-div-right-box         { <?php echo self::$form_options['right_box_style']; ?> }\n\
.fscf-div-clear             { <?php echo self::$form_options['clear_style']; ?> }\n\
.fscf-div-field-left        { <?php echo self::$form_options['field_left_style']; ?> }\n\
.fscf-div-field-prefollow   { <?php echo self::$form_options['field_prefollow_style']; ?> }\n\
.fscf-div-field-follow      { <?php echo self::$form_options['field_follow_style']; ?> }\n\
.fscf-div-label             { <?php echo self::$form_options['title_style']; ?> }\n\
.fscf-div-field             { <?php echo self::$form_options['field_div_style']; ?> }\n\
.fscf-div-captcha-sm        { <?php echo self::$form_options['captcha_div_style_sm']; ?> }\n\
.fscf-div-captcha-m         { <?php echo self::$form_options['captcha_div_style_m']; ?> }\n\
.fscf-image-captcha         { <?php echo self::$form_options['captcha_image_style']; ?> }\n\
.fscf-image-captcha-refresh { <?php echo self::$form_options['captcha_reload_image_style']; ?> }\n\
.fscf-div-submit            { <?php echo self::$form_options['submit_div_style']; ?> }\n\
.fscf-fieldset              { <?php echo self::$form_options['border_style']; ?> }\n\
/* Styles of labels, fields and text */\n\
.fscf-required-indicator { <?php echo self::$form_options['required_style']; ?> }\n\
.fscf-required-text      { <?php echo self::$form_options['required_text_style']; ?> }\n\
.fscf-hint-text          { <?php echo self::$form_options['hint_style']; ?> }\n\
.fscf-div-error          { <?php echo self::$form_options['error_style']; ?> }\n\
.fscf-div-redirecting    { <?php echo self::$form_options['redirect_style']; ?> }\n\
.fscf-fieldset-field     { <?php echo self::$form_options['fieldset_style']; ?> }\n\
.fscf-label              { <?php echo self::$form_options['label_style']; ?> }\n\
.fscf-option-label       { <?php echo self::$form_options['option_label_style']; ?> }\n\
.fscf-input-text         { <?php echo self::$form_options['field_style']; ?> }\n\
.fscf-input-captcha      { <?php echo self::$form_options['captcha_input_style']; ?> }\n\
.fscf-input-textarea     { <?php echo self::$form_options['textarea_style']; ?> }\n\
.fscf-input-select       { <?php echo self::$form_options['select_style']; ?> }\n\
.fscf-input-checkbox     { <?php echo self::$form_options['checkbox_style']; ?> }\n\
.fscf-input-radio        { <?php echo self::$form_options['radio_style']; ?> }\n\
.fscf-button-submit      { <?php echo self::$form_options['button_style']; ?> }\n\
.fscf-button-reset       { <?php echo self::$form_options['reset_style']; ?> }\n\
.fscf-button-vcita       { <?php echo self::$form_options['vcita_button_style']; ?> }\n\
.fscf-button-div-vcita   { <?php echo self::$form_options['vcita_div_button_style']; ?> }\n\
.fscf-powered-by         { <?php echo self::$form_options['powered_by_style']; ?> }\n\
/* Placeholder Style - WebKit browsers - Safari, Chrome */\n\
::-webkit-input-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }\n\
/* Placeholder Style - Mozilla Firefox 4 - 18 */\n\
:-moz-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }\n\
/* Placeholder Style - Mozilla Firefox 19+ */\n\
::-moz-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }\n\
/* Placeholder Style - Internet Explorer 10+ */\n\
:-ms-input-placeholder { <?php echo self::$form_options['placeholder_style']; ?> }\n\
</style>\n\
";
jQuery(document).ready(function($) {
$('head').append(fscf_styles);
});
//]]>
</script>
<?php

  }

  static function get_ext_css() {
   // external css class names
   self::$ext_css = array(

            // Alignment DIVs
		    'form_style'           => 'fscf-div-form',   // Form DIV, how wide is the form DIV
            'left_box_style'       => 'fscf-div-left-box',   // left box DIV, container for vcita
            'right_box_style'      => 'fscf-div-right-box',   // right box DIV, container for vcita
		    'clear_style'          => 'fscf-div-clear',   // clear both
		    'field_left_style'     => 'fscf-div-field-left',   // field left
            'field_prefollow_style' => 'fscf-div-field-prefollow',   // field prefollow
		    'field_follow_style'   => 'fscf-div-field-follow',   // field follow

			'title_style'          => 'fscf-div-label', // Input labels alignment DIV
			'field_div_style'      => 'fscf-div-field',   // Input fields alignment DIV
			'captcha_div_style_sm' => 'fscf-div-captcha-sm',  // Small CAPTCHA DIV
			'captcha_div_style_m'  => 'fscf-div-captcha-m',  // Large CAPTCHA DIV
			'captcha_image_style'  => 'fscf-image-captcha', // CAPTCHA image alignment
			'captcha_reload_image_style' => 'fscf-image-captcha-refresh', // CAPTCHA refresh image alignment
			'submit_div_style'     => 'fscf-div-submit', // Submit DIV
            'border_style'         => 'fscf-fieldset', // style of the fieldset box (if enabled)

		    // Styles of labels, fields and text

            'required_style'       => 'fscf-required-indicator',   // required field indicator
            'required_text_style'  => 'fscf-required-text',   // required field text
			'hint_style'           => 'fscf-hint-text',  // small text hints like file types
            'error_style'          => 'fscf-div-error', // Input validation messages
            'redirect_style'       => 'fscf-div-redirecting', // Redirecting message
            'fieldset_style'       => 'fscf-fieldset-field', // style of the fieldset box (for a field)

            'label_style'          => 'fscf-label', // Field labels
  			'option_label_style'   => 'fscf-option-label', // Options labels

 			'field_style'          => 'fscf-input-text', // Input text fields
  			'captcha_input_style'  => 'fscf-input-captcha', // CAPTCHA input field
 			'textarea_style'       => 'fscf-input-textarea',  // Input Textarea
            'select_style'         => 'fscf-input-select',  //  Input Select
 			'checkbox_style'       => 'fscf-input-checkbox',  // Input checkbox
            'radio_style'          => 'fscf-input-radio',  // Input radio

			'button_style'         => 'fscf-button-submit', // Submit button
			'reset_style'          => 'fscf-button-reset', // Reset button
            'vcita_button_style'   => 'fscf-button-vcita', // vCita button
            'vcita_div_button_style' => 'fscf-button-div-vcita', // vCita button div box

 			'powered_by_style'     => 'fscf-powered-by', // the "powered by" link

		);
 }

  static function get_honeypot_slugs($fields) {
  // filter a list of field names that are not currently used on the form
	 $decoy_fields = array( 'address','suite','company','phone','title','city','state','fax','newsletter','webites','zipcode','address2','firstname','lastname','birthday');

      // check for custom post types,
      // none of the field slugs can be the same as a post type rewrite_slug
      // or you will get "page not found" when posting the form with that field filled in

      $pt_args = array('public' => true,'_builtin' => false);
      $post_types = get_post_types( $pt_args, 'objects' );
      $pt_slugs = array();
      if ( $post_types ) {
         foreach ( $post_types as $post_type ) {
              $pt_slugs[] = ( isset( $post_type->rewrite_slug ) ) ? $post_type->rewrite_slug : $post_type->name;
         }
      }

	  if ($fields && is_array($fields)) {
		  foreach ($decoy_fields as $index => $decoy) {
				if (isset($fields[$decoy]))  // decoy field matches a form field, remove the decoy from list
					unset($decoy_fields[$index]);
                if (!empty($pt_slugs) && in_array( $decoy, $pt_slugs ) )
                    unset($decoy_fields[$index]); // decoy field matches a custom post type, remove the decoy from list
		  }
	  }

		sort($decoy_fields);
		return $decoy_fields;
	}

	static function get_todays_honeypot_slug($fields) {
       // find a decoy field name that is not currently used on the form
       // change it each day of the week
		$decoy_fields = self::get_honeypot_slugs($fields);
		$max = count($decoy_fields);
         if ($max > 5)
		      $index = date('w');
         else if ($max > 0)
              $index = 0;
         else
              return 'email456';

        return $decoy_fields[$index];
	}


	static function display_form($string) {
		// Build the code to display the form in $string and return it
		// The form code will be appended to $string and returned

		global $captcha_path_cf; // used by secureimage.php
		$captcha_path_cf = FSCF_CAPTCHA_PATH;

		// Set up the styles for the form
		self::$style['hint'] = self::convert_css( self::$form_options['hint_style'] );
		self::$style['textarea'] = self::convert_css( self::$form_options['textarea_style'] );
		self::$style['checkbox'] = self::convert_css( self::$form_options['checkbox_style'] );
		self::$style['option_label'] = self::convert_css( self::$form_options['option_label_style'] ); // option label
        self::$style['label'] = self::convert_css( self::$form_options['label_style'] ); // label
		self::$style['form'] = self::convert_css( self::$form_options['form_style'] );
		self::$style['border'] = self::convert_css( self::$form_options['border_style'] );
        self::$style['fieldset'] = self::convert_css( self::$form_options['fieldset_style'] );
		self::$style['select'] = self::convert_css( self::$form_options['select_style'] );
		self::$style['title'] = self::convert_css( self::$form_options['title_style'] );
		self::$style['field'] = self::convert_css( self::$form_options['field_style'] ); // text fields
		self::$style['field_div'] = self::convert_css( self::$form_options['field_div_style'] );
		self::$style['error'] = self::convert_css( self::$form_options['error_style'] );
		self::$style['required'] = self::convert_css( self::$form_options['required_style'] );
        self::$style['required_text'] = self::convert_css( self::$form_options['required_text_style'] );
		self::$style['submit_div'] = self::convert_css(self::$form_options['submit_div_style']);
		self::$style['submit'] = self::convert_css(self::$form_options['button_style']);
		self::$style['reset'] = self::convert_css(self::$form_options['reset_style']);

		self::$aria_required = ' aria-required="true" ';

        $hidden = "\n";

		if ( self::$contact_error )  // this is for some people who hide the form in a div, if there are validation errors, unhide it
			self::$form_options['form_style'] = str_replace( 'display: none;', '', self::$form_options['form_style'] );


		$string .= '
<div id="FSContact' . self::$form_id_num . '" ' . self::get_this_css('form_style') . '>';

        $form_attributes = '';
        if( !empty(self::$form_options['form_attributes']) )
                $form_attributes = self::$form_options['form_attributes'].' ';

        if (self::$form_options['vcita_scheduling_button'] == 'true' && self::is_vcita_activated() )
	         $string .= "\n<div ".'id="fscf_div_left_box' . self::$form_id_num . '" '.self::get_this_css('left_box_style').">";

        $anchor = '';
        if ( self::$form_options['anchor_enable'] == 'true' )
           $anchor = '#FSContact' . self::$form_id_num;

$string .= '
<form ' . self::$have_attach . 'action="' . esc_url( self::$form_action_url ) . $anchor . '" id="fscf_form' . self::$form_id_num . '" '.$form_attributes.'method="post">
';

		if ( self::$form_options['border_enable'] == 'true' ) {
			$string .=	'<fieldset id="fscf_form_fieldset' . self::$form_id_num . '" ' . self::get_this_css('border_style') . ">\n";
			if ( self::$form_options['title_border'] != '' ) {
				$string .= '<legend>';
				$string .= self::$form_options['title_border'];
				$string .= "</legend>\n";
			}
		}
		// check attachment directory
		$frm_id = self::$form_id_num;  // needed for use w/in "" below.. can't use self::
		if ( self::$have_attach ) {
			self::init_temp_dir( FSCF_ATTACH_DIR );
			if ( self::$form_options['php_mailer_enable'] == 'php' ) {
				self::set_form_error("fscf_attach_dir$frm_id", __( 'Attachments are only supported when the Send Email function is set to WordPress. You can find this setting on the contact form settings page.', 'si-contact-form' ) );
			}
			if ( ! is_dir( FSCF_ATTACH_DIR ) ) {
				self::set_form_error("fscf_attach_dir$frm_id", __( 'The temporary folder for the attachment field does not exist.', 'si-contact-form' ) );
			} else if ( ! is_writable( FSCF_ATTACH_DIR ) ) {
				self::set_form_error("fscf_attach_dir$frm_id", __( 'The temporary folder for the attachment field is not writable.', 'si-contact-form' ) );
			} else {
				// delete files over 3 minutes old in the attachment directory
                // full directory sweep cleanup
				//self::clean_temp_dir( FSCF_ATTACH_DIR, 3 );
			}
		}

      // check for custom post types, returns the global static self::$post_types_slugs
      // none of the field slugs can be the same as a post type rewrite_slug
      // or you will get "page not found" when posting the form with that field filled in
      $pt_args = array('public' => true,'_builtin' => false);
      $post_types = get_post_types( $pt_args, 'objects' );
      $slug_list = array();
      $post_types_slugs = array('post','page','attachment','revision');
      if ( $post_types ) {
         foreach ( $post_types as $post_type ) {
              $post_types_slugs[] = ( isset( $post_type->rewrite_slug ) ) ? $post_type->rewrite_slug : $post_type->name;
         }
        // print_r($post_types_slugs);

        foreach ( self::$form_options['fields'] as $key => $field ) {
          $slug_list[] = $field['slug'];
        }
        //print_r($slug_list);

        foreach ($post_types_slugs as $key => $slug) {
            if ( in_array( strtolower( $slug ), $slug_list ) ) {
             	$string .= '
		<div id="fscf_form_error_email' . self::$form_id_num . '" ' . self::get_this_css('error_style') . '>' .sprintf( __( 'Warning: one of your field tags conflicts with the post type redirect tag "%s". To automatically correct this, click the <b>Save Changes</b> button on the form edit page.', 'si-contact-form' ), $slug )
         . "\n    </div>\n";
            }

        }
      }

     	// print input error message
		if ( self::$contact_error ) {
			// There are errors, so print the generic error message
			$string .= '      <div id="fscf_form_error' . self::$form_id_num . '" ' . self::get_this_css('error_style') . ">\n";
			$string .= (self::$form_options['error_correct'] != '') ? self::$form_options['error_correct'] : __( 'Please make corrections below and try again.', 'si-contact-form' );
			$string .= "\n    </div>\n";
			
			// Print errors that appear at the top of the form
			$string .= self::echo_if_error("fscf_attach_dir$frm_id");
			$string .= self::echo_if_error('akismet');
		}
		
		// Get the email-to list
		self::$contacts = self::get_contact_list(self::$form_id_num, self::$form_options['email_to']);
		
		if ( empty( self::$contacts ) ) {	// was $ctf_contacts
			$string .= '
		<div id="fscf_form_error_email' . self::$form_id_num . '" ' . self::get_this_css('error_style') . '>' . __( 'ERROR: Misconfigured email address in options.', 'si-contact-form' )
         . "\n    </div>\n";
		}

		if ( self::$global_options['enable_php_sessions'] == 'true' ) { // this feature only works when PHP sessions are enabled
			if ( ! empty( $_SESSION["fsc_shortcode_hidden_$frm_id"] ) ) {
				$hidden_fields = self::get_hidden_fields();
				if ( ! empty( $hidden_fields ) ) {
					foreach ( $hidden_fields as $key => $value ) {
						$hidden .= "\n" . '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />'. "\n";
					}
				}
			} else {
				unset( $_SESSION["fsc_shortcode_hidden_$frm_id"] );
			}
		}

		// Add a hidden field if this is the admin preview, so that we return to the preview after submit
		if ( is_admin() )
			$hidden .= '<input type="hidden" name="ctf_action" value="' . __('Preview Form', 'si-contact-form') . '" />'. "\n";

		$hidden .= '<input type="hidden" name="fscf_submitted" value="0" />'. "\n";
		$hidden .= '<input type="hidden" name="fs_postonce_'.self::$form_id_num.'" value="'. wp_hash( time() ).','.time() .'" />'. "\n";
		$hidden .= '<input type="hidden" name="si_contact_action" value="send" />'. "\n";
        $hidden .= '<input type="hidden" name="form_id" value="' . self::$form_id_num . '" />'. "\n";


		if ( self::$form_options['req_field_label_enable'] == 'true' && self::$form_options['req_field_indicator_enable'] == 'true' ) {
            $string .= "\n".'<div id="fscf_required'.self::$form_id_num.'">' . "\n";
            $string .= '  <span '.self::get_this_css('required_style').'>' . self::$form_options['req_field_indicator'] . '</span> <span '.self::get_this_css('required_text_style').'>';
            $string .= (self::$form_options['tooltip_required'] != '') ? self::$form_options['tooltip_required'] : __( 'indicates required field', 'si-contact-form' );
		    $string .= "</span>\n</div>\n\n";
		}
		// If there are multiple mail-to contacts, display a select form
		if ( count( self::$contacts ) > 1 ) {

			$string .= '<div id="fscf_div_clear_contact' . self::$form_id_num.'" '.self::get_this_css('clear_style').'>' . "\n" . '  <div id="fscf_div_field_contact' . self::$form_id_num . '" '.self::get_this_css('field_left_style').'>
    <div ' . self::get_this_css('title_style') . '>
      <label '.self::get_this_css('label_style').' for="fscf_mail_to' . self::$form_id_num . '">';
			$string .= (self::$form_options['title_dept'] != '') ? self::$form_options['title_dept'] : __( 'Select a contact:', 'si-contact-form' );
			$string .= self::$req_field_ind . '</label>
    </div>
    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'contact' ) . '
      <select ' . self::get_this_css('select_style') . ' id="fscf_mail_to' . self::$form_id_num . '" name="mailto_id" ' . self::$aria_required . '>
';
			$string .= '       <option value="">';
			$string .= (self::$form_options['title_select'] != '') ? esc_html( self::$form_options['title_select'] ) : esc_html( __( 'Select', 'si-contact-form' ) );
			$string .= "</option>\n";

			if ( ! isset( $cid ) && '' != self::$form_content['mailto_id'] ) {
				$cid = (int) self::$form_content['mailto_id'];
			}

			$selected = '';

			foreach ( self::$contacts as $k => $v ) {
				if ( !empty( $cid ) && $cid == $k ) {
					$selected = ' selected="selected"';
				}
				$string .= '       <option value="' . $k . '"' . $selected . '>' . esc_attr($v['CONTACT']) . "</option>\n";
				$selected = '';
			}
			$string .= '      </select>
    </div>
  </div>
</div>
';
		} else {
			$hidden .= '<input type="hidden" name="mailto_id" value="1" />'. "\n";
		}

		$open_fieldset = false;	// is a fieldset field open?
		// A div class="fscf-clear" is used to group a field with any that follow it
		// $open_div tracks whether this div is currently open
		$open_div = false;
		$date_fields = array();		// List of date fields
		self::$printed_tooltip_filetypes = 0;

		// ********** Go through all the fields and print them **********

         // fill in any missing defaults
        $field_opt_defaults = array(
          'hide_label'	 => 'false',
          'placeholder'	 => 'false',
         );

		// Create a list of follow values for fields
		$field_follow = array();
		foreach ( self::$form_options['fields'] as $key => $field ) {
			if ( 'true' != $field['disable'] ) 
				$field_follow[] = $field['follow'];
		}
		
		$fld_cnt = 0;
		$fields_in_use = array();
		foreach ( self::$form_options['fields'] as $key => $field ) {

            // fill in any missing field options defaults
		    foreach ( $field_opt_defaults as $dfkey => $dfval ) {
                if ( !isset($field[$dfkey]) || empty($field[$dfkey]) )
				      $field[$dfkey] = $dfval;
		    }

			if ( 'true' == $field['disable'] ) continue;
            $fields_in_use[$field['slug']] = 1;
			if ( 'true' == $field['follow'] && $open_div ) {
				$string .= '  <div id="fscf_div_follow' . self::$form_id_num.'_'.$key.'" '.self::get_this_css('field_follow_style').'>';
			} else {
				if ( $open_div ) {
					// close the preceeding div used for grouping
					$string .= "</div>\n";
					$open_div = false;
				}
				if ( 'fieldset' == $field['type'] || 'fieldset-close' == $field['type'] ) {
					$string .= "\n" . '<div '.self::get_this_css('clear_style')."></div>\n";
				} else {
					$open_div = true;
					$string .= "\n" . '<div id="fscf_div_clear' . self::$form_id_num.'_'.$key.'" '.self::get_this_css('clear_style').'>' . "\n" . '  ';
                    $string .= '<div id="fscf_div_field' . self::$form_id_num.'_'.$key.'" ';
                    // find out if this field preceeds a follow field or vcita enabled (narrow), else it needs to be (wide)
//                    if ( ( isset(self::$form_options['fields'][$key+1] ) && self::$form_options['fields'][$key+1]['follow'] == 'true' )
                    if ( ( isset($field_follow[$fld_cnt+1] ) && $field_follow[$fld_cnt+1] == 'true' )
						// Added by Ken Carlson for displaying name parts inline
						|| ( FSCF_NAME_FIELD == $field['standard'] && 'name' != self::$form_options['name_format'] && 'true' == $field['inline'] )
                      || ( self::$form_options['vcita_scheduling_button'] == 'true' && self::is_vcita_activated() 
                       ) )
						  $string .= self::get_this_css('field_prefollow_style').'>'; // narrow
					   else
						  $string .= self::get_this_css('field_left_style').'>'; // wide
				}
			}
			$fld_cnt++;

			// Display code common to all/most field types
			if ( ! in_array( $field['type'], array('fieldset', 'fieldset-close', 'hidden') ) ) {
				if ( $field['notes'] != '' ) {
					$string .= "\n". self::ctf_notes( $field['notes'] );
				}
				if ( ('checkbox' != $field['type'] && $field['standard'] < 1) && ( 'false' == $field['hide_label'] ) ) {
                    // hiding the label
					// Standard field labels can be changed in options, so don't print them here
					// single checkbox labels are printed next to the checkbox
					$string .= "\n    <div ".' id="fscf_label' . self::$form_id_num . '_' . $key . '" ' .  self::get_this_css('title_style') .'>
      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_field' . self::$form_id_num . '_' . $key . '">' . esc_html($field['label']);
					$string .= ( 'true' == $field['req'] ) ? self::$req_field_ind : '';
					$string .= "</label>\n    </div>";
				} else if ( ('checkbox' == $field['type'] && $field['standard'] < 1) || ( 'true' == $field['hide_label'] ) ) {
                    // single checkbox keep the div to maintain style left alignment (no label here), or hide label was checked
					$string .= "\n    <div ".' id="fscf_label' . self::$form_id_num . '_' . $key . '" ' . self::get_this_css('title_style');
					$string .= ">\n    </div>";

                }
				self::$aria_required = ( $field['req'] && 'true' == self::$form_options['aria_required'] ) ? ' aria-required="true" ' : '';
			}

			switch ( $field['type'] ) {
				case 'fieldset' :
					if ( $open_fieldset )
						$string .= "</fieldset>\n";
					if ( $field['notes'] != '' ) {
						$string .= "\n". self::ctf_notes( $field['notes'] );
					}
					$string .= "\n<fieldset".' id="fscf_fieldset' . self::$form_id_num . '_' . $key . '" ';
					$string .= ($field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('fieldset_style');
					$string .= '>
';
                    if ( 'false' == $field['hide_label'] )
	$string .= '	  <legend>' . esc_html($field['label']) . "</legend>\n";
					$open_fieldset = true;
					break;

				case 'fieldset-close' :
					if($open_fieldset)
					   $string .=   "</fieldset>\n";
					$open_fieldset = false;
					break;
					
				case 'hidden' :
					$string .= '      <input type="hidden" name="' . $field['slug'] . '" value="'
						. esc_attr(self::$form_content[$field['slug']]) . '" />' . "\n";					
					break;
				
				case 'password' :
					$string .= '    <div '.self::get_this_css('field_div_style').'>'.self::echo_if_error( $field['slug'] ) . "\n"
						. '      <input ';
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="password" id="fscf_field'.self::$form_id_num.'_'.$key.'" name="' . $field['slug'] . '" value=""'
//					No defaults for password!
						. ' '.self::$aria_required.' ';
					if($field['max_len'] != '')
						$string .=  ' maxlength="'.$field['max_len'].'"';
					if($field['attributes'] != '')
					  $string .= ' '.$field['attributes'];
                    if ( 'true' == $field['placeholder'] && $field['default'] != '') {
                         $string .= ' placeholder="'.esc_attr($field['default']).'"';
                         self::$placeholder = 1;
                    }
					$string .= " />\n    </div>\n";
					break;
				
				case 'text':
				case 'email':
				case 'url':
					$string .= self::display_field_text($key, $field);
					break;
				
				case 'textarea':
					$string .= self::display_field_textarea($key, $field);
					break;
				
				case 'select' :
				case 'select-multiple':
					$string .= self::display_field_select($key, $field);
					break;

				case 'checkbox':
				case 'checkbox-multiple':
				case 'radio':
					$string .= self::display_field_checkbox($key, $field);
					break;

				case 'date':
					$string .= self::display_field_date($key, $field);
					$date_fields[] = $key;
					break;
				
				case 'time':
					$string .= self::display_field_time($key, $field);
					break;
				
				case 'attachment':
					$string .= self::display_field_attachment($key, $field);
					break;				
			}	// end switch

			if ( 'fieldset' <> $field['type'] && 'fieldset-close' <> $field['type'] && 'hidden' <> $field['type'] ) {
				if ( $field['notes_after'] != '' ) {
					$string .= self::ctf_notes($field['notes_after'])."\n";
					}
			}
			if ( $open_div ) {
				$string .= "  </div>\n";	// close field div
			}
			
		} // end foreach (go through fields)

        // action hook for form display after fields
        $string = apply_filters( 'si_contact_display_after_fields', $string, self::$style, self::$form_errors, self::$form_id_num );

		// Are there any date fields?
		if ( count($date_fields) > 0 )
			self::setup_calendar($date_fields);

		
		// ********** Display stuff at the bottom of form **********
		
		// close final outer container for field and follow groups
		if ( $open_div ) {
			$string .= '</div>
<div '.self::get_this_css('clear_style').'></div>' . "\n";
		}
        $captcha_enabled = self::is_captcha_enabled(self::$form_id_num);

        if ( $captcha_enabled == 'recaptcha' ) {
			$string .= self::display_recaptcha() . "\n";
		} else if ( $captcha_enabled == 'sicaptcha' ) {
			$string .= self::display_captcha() . "\n";
		}

        // hidden empty honeypot field, if enabled
        if ( self::$form_options['honeypot_enable'] == 'true' ) {
           $honeypot_slug = self::get_todays_honeypot_slug($fields_in_use);
      $string .= '        '.self::echo_if_error( $honeypot_slug ).'
<div style="display:none;">
     <label for="'.$honeypot_slug.self::$form_id_num.'"><small>'.__('Leave this field empty', 'si-contact-form').'</small></label>
     <input type="text" name="'.$honeypot_slug.'" id="'.$honeypot_slug.self::$form_id_num.'" value="" />
</div>
';
        }

		// Display the submit button
		$string .= "\n<div ".'id="fscf_submit_div'.self::$form_id_num.'" ' . self::get_this_css('submit_div_style') . '>
		<input type="submit" id="fscf_submit' . self::$form_id_num . '" ' . self::get_this_css('button_style') . ' value="';
		$string .= (self::$form_options['title_submit'] != '') ? esc_attr( self::$form_options['title_submit'] ) : esc_attr( __( 'Submit', 'si-contact-form' ) );
		$string .= '" ';
        $onclick = 0;
        if( !empty(self::$form_options['submit_attributes']) ) {
                $string .= self::$form_options['submit_attributes'].' ';
             if ( preg_match( "/onclick/i", self::$form_options['submit_attributes'] ) )
                $onclick = 1;
        }
		if ( self::$form_options['enable_areyousure'] == 'true' && !$onclick) {
			$msg = (self::$form_options['title_areyousure'] != '') ? esc_html( addslashes( self::$form_options['title_areyousure'] ) ) : esc_html( addslashes( __( 'Are you sure?', 'si-contact-form' ) ) );
			$string .= ' onclick="return confirm(\'' . $msg . '\')" ';

		}
        // only allow the submit button one click
        if( self::$form_options['enable_submit_oneclick'] == 'true' && self::$form_options['enable_areyousure'] != 'true' && !$onclick) {
          $msg = (self::$form_options['title_submitting'] != '') ? esc_html( addslashes( self::$form_options['title_submitting'] ) ) : esc_html( addslashes( __( 'Submitting...', 'si-contact-form' ) ) );
          $string .= ' onclick="this.disabled=true; this.value=\''.$msg.'\'; this.form.submit();" ';
        }
		$string .= '/> ';
		
		if ( !self::$contact_error && self::$form_options['enable_reset'] == 'true' ) {
			$string .= '<input type="reset" id="fscf_reset' . self::$form_id_num . '" ' . self::get_this_css('reset_style') . ' value="';
			$string .= (self::$form_options['title_reset'] != '') ? esc_attr( self::$form_options['title_reset'] ) : esc_attr( __( 'Reset', 'si-contact-form' ) );
			$msg = addslashes( __( 'Do you really want to reset the form?', 'si-contact-form' ) );
			$string .= '" onclick="return confirm(\'' . $msg . '\')" />';
		}

		$string .= "\n</div>\n";

		if ( self::$form_options['border_enable'] == 'true' ) {
			$string .= "</fieldset>\n";
		}

        $string .= $hidden;

		// Close the form
		$string .= "\n</form>\n";

		if ( self::$form_options['enable_credit_link'] == 'true' ) {
			$string .= "\n    <p " . self::convert_css( self::$form_options['powered_by_style'] ) . '>' . __( 'Powered by', 'si-contact-form' )
				. ' <a href="http://wordpress.org/extend/plugins/si-contact-form/" target="_blank">' . __( 'Fast Secure Contact Form', 'si-contact-form' )
				. "</a></p>\n";
		}

        if ( self::$form_options['vcita_scheduling_button'] == 'true' && self::is_vcita_activated() ) {
           $string .= "</div>\n<div ".'id="fscf_div_right_box' . self::$form_id_num . '" '.self::get_this_css('right_box_style').">\n";
		   $string = self::display_vcita_scheduler_button( $string );
		   $string .= "\n</div>\n";
        }


		$string .= '</div>';	// closes fscf-container
		$string .= "
<div ".self::get_this_css('clear_style')."></div>\n";
if (self::$placeholder && self::$form_options['external_style'] == 'false') {
   self::$add_placeholder_script = 1; // for adding the javascript
   $placeholder_style = self::$form_options['placeholder_style'];
   if ( preg_match( "/^style=\"(.*)\"$/i", $placeholder_style, $matches ) )
			$placeholder_style = $matches[1];
   if ( preg_match( "/^class=\"(.*)\"$/i", $placeholder_style, $matches ) )
			$placeholder_style = $matches[1];

$string .= '
<style type="text/css">

/* Placeholder Style - WebKit browsers - Safari, Chrome */
::-webkit-input-placeholder { '.$placeholder_style.' }

/* Placeholder Style - Mozilla Firefox 4 - 18 */
:-moz-placeholder { '.$placeholder_style.' }

/* Placeholder Style - Mozilla Firefox 19+ */
::-moz-placeholder { '.$placeholder_style.' }

/* Placeholder Style - Internet Explorer 10+ */
:-ms-input-placeholder { '.$placeholder_style.' }

</style>
';
}

        if ( !empty( self::$form_options['after_form_note'] ) )
           $string .= self::$form_options['after_form_note'];

		$string .= "\n".'<!-- Fast Secure Contact Form plugin '.FSCF_VERSION.' - end - FastSecureContactForm.com -->'. "\n";

		return($string);
	}	// end function display_form()
	
	static function display_name($key, $field) {
		// Returns the code to display the name field on the form
		global $current_user, $user_ID;
		// Get defaults

		// Find logged in user's WP name (auto form fill feature):
		// http://codex.wordpress.org/Function_Reference/get_currentuserinfo
        $auto_fill = 0;
		if ( isset(self::$form_content[$field['slug']]) &&
        '' == self::$form_content[$field['slug']] &&
        $user_ID != '' &&
        $current_user->user_login != 'admin' &&
        ! current_user_can( 'manage_options' ) &&

        self::$form_options['auto_fill_enable'] == 'true' ) {
			// user logged in (and not admin rights) (and auto_fill_enable set in options)
            $auto_fill = 1;
			self::$form_content[$field['slug']] = $current_user->user_login;
		}

        $f_default = $m_default = $mi_default = $l_default = '';
       if ( $field['default'] != '') {
           if ( self::$form_options['name_format'] == 'first_last' ) {
                  if ( !preg_match('/^(.*)(==)(.*)$/', $field['default'], $matches) )
                      $field['default'] = 'First Name==Last Name'; // default to proper format
                  if ( preg_match('/^(.*)(==)(.*)$/', $field['default'], $matches) ) {
                       $f_default = $matches[1];
                       $l_default = $matches[3];
                  }
           } else if ( self::$form_options['name_format'] == 'first_middle_last' ) {
                  if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $field['default'], $matches) )
                       $field['default'] = 'First Name==Middle Name==Last Name'; // default to proper format
                  if ( preg_match('/^(.*)(==)(.*)(==)(.*)$/', $field['default'], $matches) ) {
                       $f_default = $matches[1];
                       $m_default = $matches[3];
                       $l_default = $matches[5];
                  }
           } else if ( self::$form_options['name_format'] == 'first_middle_i_last' ) {
                  if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $field['default'], $matches) )
                       $field['default'] = 'First Name==Middle Initial==Last Name'; // default to proper format
                  if ( preg_match('/^(.*)(==)(.*)(==)(.*)$/', $field['default'], $matches) ) {
                       $f_default = $matches[1];
                       $mi_default = $matches[3];
                       $l_default = $matches[5];
                  }
           }
        }
		$string = '';
		$f_name_string = '
    <div ' . self::get_this_css('title_style') . ">\n";
              if ( 'false' == $field['hide_label'] ) {
$f_name_string .= '      <label ';
	    $f_name_string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$f_name_string .= ' for="fscf_f_name' . self::$form_id_num . '">';
		$f_name_string .= (self::$form_options['title_fname'] != '') ? self::$form_options['title_fname'] : __( 'First Name:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$f_name_string .= self::$req_field_ind;
                $f_name_string .= "</label>\n";
              }
$f_name_string .= '    </div>
    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'f_name' ) . '
	  <input ';
		$f_name_string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$f_name_string .= ' type="text" id="fscf_f_name' . self::$form_id_num .
		'" name="f_name" value="' . esc_attr( self::$form_content['f_name'] ) . '" ' . self::$aria_required;
        if($field['attributes'] != '')
			  $f_name_string .= ' '.$field['attributes'];
if ( 'true' == $field['placeholder'] && $f_default != '') {
         $f_name_string .= ' placeholder="'.esc_attr($f_default).'"';
   self::$placeholder = 1;
}
        $f_name_string .= ' />
    </div>';

		$l_name_string = '
    <div ' . self::get_this_css('title_style') . ">\n";
              if ( 'false' == $field['hide_label'] ) {
$l_name_string .= '      <label ';
	    $l_name_string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$l_name_string .= ' for="fscf_l_name' . self::$form_id_num . '">';
		$l_name_string .= (self::$form_options['title_lname'] != '') ? self::$form_options['title_lname'] : __( 'Last Name:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$l_name_string .= self::$req_field_ind;
                $l_name_string .= "</label>\n";
              }
$l_name_string .= '    </div>
    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'l_name' ) . '
	  <input ';
		$l_name_string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$l_name_string .= ' type="text" id="fscf_l_name' . self::$form_id_num . '" name="l_name" value="' . esc_attr( self::$form_content['l_name'] ) . '" ' . self::$aria_required;
        if($field['attributes'] != '')
			  $l_name_string .= ' '.$field['attributes'];
 if ( 'true' == $field['placeholder'] && $l_default != '') {
            $l_name_string .= ' placeholder="'.esc_attr($l_default).'"';
    self::$placeholder = 1;
}
        $l_name_string .= ' />
    </div>
';

		switch ( self::$form_options['name_format'] ) {
			case 'name':
				$string .= '
    <div ' . self::get_this_css('title_style') . ">\n";
              if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_name' . self::$form_id_num . '">';
				$string .= (self::$form_options['title_name'] != '') ? self::$form_options['title_name'] : __( 'Name:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$string .= self::$req_field_ind;
                $string .= "</label>\n";
              }
$string .= '    </div>
    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'full_name' ) . '
      <input ';
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="text" id="fscf_name' . self::$form_id_num . '" name="full_name" value="' . esc_attr( self::$form_content[$field['slug']] ) . '" ' . self::$aria_required;
        if($field['attributes'] != '')
			  $string .= ' '.$field['attributes'];
        if($auto_fill) //auto form fill logged in name and email
              $string .= ' readonly="readonly"';
if ( 'true' == $field['placeholder'] && $field['default'] != '') {
   $string .= ' placeholder="'.esc_attr($field['default']).'"';
 self::$placeholder = 1;
}
        $string .= ' />
    </div>
';
				break;
			case 'first_last':
				$string .= $f_name_string;
				// See if name parts are to be displayed inline
				if ( 'true' == $field['inline'] )
					$string .= '</div><div ' . self::get_this_css('field_follow_style').'>';
				$string .= $l_name_string;
				break;
			case 'first_middle_i_last':
				$string .= $f_name_string;
                // See if name parts are to be displayed inline
				if ( 'true' == $field['inline'] )
					$string .= '</div><div ' . self::get_this_css('field_follow_style').'>';
				$string .= '
    <div ' . self::get_this_css('title_style') . ">\n";
              if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_mi_name' . self::$form_id_num . '">';
				$string .= (self::$form_options['title_miname'] != '') ? self::$form_options['title_miname'] : __( 'Middle Initial:', 'si-contact-form' );
				$string .= "</label>\n";
              }
$string .= '    </div>
    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'mi_name' ) . '
       <input ';
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="text" id="fscf_mi_name' . self::$form_id_num . '" name="mi_name" value="' . esc_attr( self::$form_content['mi_name'] ) . '" ';
        if($field['attributes'] != '')
		     $string .= ' '.$field['attributes'];
        if ( 'true' == $field['placeholder'] && $mi_default != '') {
          $string .= ' placeholder="'.esc_attr($mi_default).'"';
          self::$placeholder = 1;
        }
        $string .= ' />
    </div>
';
				// See if name parts are to be displayed inline
				if ( 'true' == $field['inline'] )
					$string .= '</div><div ' . self::get_this_css('field_follow_style').'>';
				$string .= $l_name_string;
				break;
			case 'first_middle_last':
				$string .= $f_name_string;
                // See if name parts are to be displayed inline
				if ( 'true' == $field['inline'] )
					$string .= '</div><div ' . self::get_this_css('field_follow_style').'>';
				$string .= '
    <div ' . self::get_this_css('title_style') . ">\n";
              if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_m_name' . self::$form_id_num . '">';
				$string .= (self::$form_options['title_mname'] != '') ? self::$form_options['title_mname'] : __( 'Middle Name:', 'si-contact-form' );
				$string .= "</label>\n";
              }
$string .= '    </div>
    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'm_name' ) . '
      <input ';
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="text" id="fscf_m_name' . self::$form_id_num . '" name="m_name" value="' . esc_attr( self::$form_content['m_name'] ) . '" ' . self::$aria_required;
        if($field['attributes'] != '')
			  $string .= ' '.$field['attributes'];
 if ( 'true' == $field['placeholder'] && $m_default != '') {
      $string .= ' placeholder="'.esc_attr($m_default).'"';
      self::$placeholder = 1;
 }
        $string .= ' />
    </div>';
    				// See if name parts are to be displayed inline
				if ( 'true' == $field['inline'] )
					$string .= '</div><div ' . self::get_this_css('field_follow_style').'>';
				$string .= $l_name_string;
				break;
		}
		return($string);

	}	// end function display_name()
	
	static function display_email($key, $field) {
		global $current_user, $user_ID;
		$string = '';

        //filter hook for email input type, someone might want to change it from type='text' to type='email'
        $email_input_type = apply_filters( 'si_contact_email_input_type', 'text',  self::$form_id_num);

		// Find logged in user's WP email address (auto form fill feature):
		// http://codex.wordpress.org/Function_Reference/get_currentuserinfo
        $auto_fill = 0;
		if ( '' == self::$form_content[$field['slug']] && $user_ID != '' && $current_user->user_login != 'admin' &&
				!current_user_can( 'manage_options' ) && self::$form_options['auto_fill_enable'] == 'true' ) {
			// user logged in (and not admin rights) (and auto_fill_enable set in options)
            $auto_fill = 1;
			self::$form_content[$field['slug']] = $current_user->user_email;
            if ( 'true' == self::$form_options['double_email'] )
			    self::$form_content['email2'] = $current_user->user_email;
		}
        $email_default = '';
        $email2_default = '';
       	if ( 'true' == self::$form_options['double_email'] ) {
            $field['req'] = 'true';
            $default = self::$form_options['fields']['1']['default']; // email field
            // find the true default for email, email2
            // is there xx==xx
            if ( !preg_match('/^(.*)(==)(.*)$/', $default, $matches) )
                       $default = 'Email==Re-enter Email';
            if ( preg_match('/^(.*)(==)(.*)$/', $default, $matches) ) {
                 $email_default = $matches[1];
                 $email2_default = $matches[3];
            }
        } else {
               $email_default = $field['default'];
        }

	$string .= "\n    <div " . self::get_this_css('title_style') . ">\n";
                if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_email' . self::$form_id_num . '">';
			$string .= (self::$form_options['title_email'] != '') ? self::$form_options['title_email'] : __( 'Email:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$string .= self::$req_field_ind;
                $string .= "</label>\n";
              }
        $string .= "    </div>\n";
        $string .= '    <div ' . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'email' )
			. "\n      <input ";
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="'.$email_input_type.'" id="fscf_email' . self::$form_id_num . '" name="email" value="';
			$string .= esc_attr( self::$form_content[$field['slug']] );
		$string .= '" ' . self::$aria_required;
        if($field['attributes'] != '')
				  $string .= ' '.$field['attributes'];
        if($auto_fill) //auto form fill logged in name and email
              $string .= ' readonly="readonly"';
         if ( 'true' == $field['placeholder'] && $email_default != '') {
            $string .= ' placeholder="'.esc_attr($email_default).'"';
            self::$placeholder = 1;
         }
        $string .= ' />'
        . "\n    </div>\n";

		if ( 'true' == self::$form_options['double_email'] ) {
            $string .= "  </div>\n</div>\n\n" . '<div id="fscf_div_clear' . self::$form_id_num.'_'.$key.'_2" '. self::get_this_css('clear_style') .'>' . "\n" . '  <div id="fscf_div_field' . self::$form_id_num.'_'.$key.'_2" '. self::get_this_css('field_left_style') .'>';
            $string .= "\n    <div " . self::get_this_css('title_style') .  ">\n";
                if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_email' . self::$form_id_num . '_2">';
			$string .= (self::$form_options['title_email2'] != '') ? self::$form_options['title_email2'] : __( 'Re-enter Email:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$string .= self::$req_field_ind;
                $string .= "</label>\n";
              }
        $string .= "    </div>\n    <div " . self::get_this_css('field_div_style') . '>' . self::echo_if_error( 'email2' )
                . "\n      <input ";
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="'.$email_input_type.'" id="fscf_email' . self::$form_id_num. '_2" name="email2" value="' . esc_attr( self::$form_content['email2'] ) . '" ' . self::$aria_required;
        if($field['attributes'] != '') // XXX same as email 1 though
					  $string .= ' '.$field['attributes'];
        if($auto_fill) //auto form fill logged in name and email
              $string .= ' readonly="readonly"';
         if ( 'true' == $field['placeholder'] && $email2_default != '') {
              $string .= ' placeholder="'.esc_attr($email2_default).'"';
              self::$placeholder = 1;
         }
  //$string .= ' onfocus="if(this.value==\''.esc_js($email2_default).'\')this.value=\'\';" onblur="if(this.value==\'\')this.value=\''.esc_js($email2_default).'\';"';
        $string .= ' />'
				. "\n    </div>\n";
		}
		return($string);
	}	// function display_email()

	static function display_field_text($key, $field) {
		// display function for a text, email, or url field type
		$string = '';
		if ( FSCF_NAME_FIELD == $field['standard'] ) return(self::display_name($key, $field));
		if ( FSCF_EMAIL_FIELD == $field['standard'] ) return(self::display_email($key, $field));

		if ( FSCF_SUBJECT_FIELD == $field['standard']) {
			// Display field title for special fields
			$string = '';
			$string .= "\n    <div " . self::get_this_css('title_style') . ">\n";
				if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_field'.self::$form_id_num.'_'.$key. '">';
			$string .= (self::$form_options['title_subj'] != '') ? self::$form_options['title_subj'] : __( 'Subject:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$string .= self::$req_field_ind;
                $string .= "</label>\n";
              }
$string .= "    </div>";
		}
        $type = $field['type'];
        //if ( 'email' == $field['type'] || 'url' == $field['type']) // XXX fix this later? email or url type was breaking the 'already posted' javascript
        //   $type = 'text';
        // Since then I have changed the 'already posted' javascript, so try email and url types again.
        $string .= "\n    <div " . self::get_this_css('field_div_style') . '>' . self::echo_if_error( $field['slug'] ) ."\n"
		. '      <input ';
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="' . $type . '" id="fscf_field' . self::$form_id_num . '_' . $key . '" name="' . $field['slug'] . '" value="';

        if ( 'url' == $field['type'] ) {
			$string .= esc_url( self::$form_content[$field['slug']] );
		} else {
			$string .= esc_attr( self::$form_content[$field['slug']] );
		}
		$string .= '" '. self::$aria_required;
		if ( $field['max_len'] != '' )
			$string .= ' maxlength="' . $field['max_len'] . '"';
		if ( $field['attributes'] != '' )
			$string .= ' ' . $field['attributes'];

        if ( 'true' == $field['placeholder'] && $field['default'] != '') {
           $string .= ' placeholder="'.esc_attr($field['default']).'"';
           self::$placeholder = 1;
        }
		$string .= " />\n    </div>\n";

		return($string);
	}	// end function display_field_text
	
	static function display_field_textarea($key, $field) {
		$string = '';

		if ( FSCF_MESSAGE_FIELD == $field['standard'] ) {
			// Standard field labels can be changed in settings, so print that here
			$string .= "\n    <div " . self::get_this_css('title_style') . ">\n";
				if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_field'.self::$form_id_num.'_'.$key. '">';
			$string .= (self::$form_options['title_mess'] != '') ? self::$form_options['title_mess'] : __( 'Message:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$string .= self::$req_field_ind;
                $string .= "</label>\n";
              }
$string .= "    </div>\n    <div " . self::get_this_css('field_div_style') . '>'
				. self::echo_if_error( 'message' ) . "\n      <textarea ";
			$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('textarea_style');
			$string .= ' id="fscf_field'.self::$form_id_num.'_'.$key . '" name="message" cols="30" rows="10" ' . self::$aria_required;
	        if($field['attributes'] != '')
					  $string .= ' '.$field['attributes'];
             if ( 'true' == $field['placeholder'] && $field['default'] != '') {
                $string .= ' placeholder="'.esc_attr($field['default']).'"';
                self::$placeholder = 1;
             }
				$string .= '>' . esc_textarea( self::$form_content[$field['slug']] ) . "</textarea>\n    </div>\n";
		} else {
			$string	.= "\n    <div " . self::get_this_css('field_div_style') . '>' . self::echo_if_error( $field['slug'] ) . "\n"
				.'      <textarea ';
			$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('textarea_style');
			$string .= ' id="fscf_field' . self::$form_id_num . '_' . $key . '" name="' . $field['slug'] . '" cols="30" rows="10" ' . self::$aria_required;
			if ( $field['attributes'] != '' )
				$string .= ' ' . $field['attributes'];
              if ( 'true' == $field['placeholder'] && $field['default'] != '') {
                 $string .= ' placeholder="'.esc_attr($field['default']).'"';
              }
			$string .= '>';
			$string .= (self::$form_options['textarea_html_allow'] == 'true') ? stripslashes( self::$form_content[$field['slug']] ) : esc_textarea( self::$form_content[$field['slug']] );
			$string .= "</textarea>\n    </div>\n";
			}
		return($string);
	}
	
	static function display_field_select($key, $field) {
		$string = '';
		
		// Get the options list
		$opts_array = explode("\n",$field['options']);
		$frm_id = self::$form_id_num;
		if ( '' == $opts_array[0] ) {
			// Error: no options were entered
			self::set_form_error("fscf_select$frm_id", __('Error: No options were entered for a select field in settings.', 'si-contact-form'));
			$string .= self::echo_if_error("fscf_select$frm_id");
		}

		// Display the field
		if ( FSCF_SUBJECT_FIELD == $field['standard']) {
			// Display field title for special fields
			$string = '';
			$string .= "\n    <div " . self::get_this_css('title_style') . ">\n";
				if ( 'false' == $field['hide_label'] ) {
$string .= '      <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('label_style');
		$string .= ' for="fscf_field' . self::$form_id_num . '_'.$key .'">';
			$string .= (self::$form_options['title_subj'] != '') ? self::$form_options['title_subj'] : __( 'Subject:', 'si-contact-form' );
                if ( 'true' == $field['req'] )
					$string .= self::$req_field_ind;
                $string .= "</label>\n";
              }
$string .= "    </div>";
			// Check for subject_id parm for backward compatibility
			if ( 0 == count(self::$form_content[$field['slug']]) && '' != self::$form_content['subject_id'])
				self::$form_content[$field['slug']][] = self::$form_content['subject_id'];
		}		

		$mult = ( 'select-multiple' == $field['type'] ) ? ' multiple="multiple"' : '';
		$string .= "\n    <div ".self::get_this_css('field_div_style').'>'.self::echo_if_error( $field['slug'] )
		."\n      <select ";
		$string .= ($field['input_css'] != '') ? self::convert_css($field['input_css']) : self::get_this_css('select_style');
		$string .= ' id="fscf_field'.self::$form_id_num.'_'.$key.'" name="' . $field['slug'].'[]"';
		if($field['attributes'] != '')
			$string .= ' '.$field['attributes'];
		$string .= $mult .">\n";

		$opts_cnt = 1;
		$selected = '';
		foreach ( $opts_array as $opt ) {
			$opt = trim($opt);
            if ( is_array(self::$form_content[$field['slug']]) ) {
			   if ( count(self::$form_content[$field['slug']]) > 0 ) {
				   if ( in_array( $opts_cnt, self::$form_content[$field['slug']] ) )
					   $selected = ' selected="selected"';
			   }
            }

			if ($opts_cnt == 1 && 'select-multiple' != $field['type'] && preg_match('/^\[(.*)]$/', $opt, $matches)) {// "[Please select]" becomes "Please select"
				$string .= '        <option value=""'.$selected.'>'.esc_attr($matches[1]).'</option>'."\n";
			} else {
                // is this key==value set?
                if ( preg_match('/^(.*)(==)(.*)$/', $opt, $matches) ) {
                      $opt = $matches[3];
                }
				$string .= '        <option value="' . $opts_cnt . '"' . $selected . '>'.esc_attr($opt).'</option>'."\n";
			    $opts_cnt++;
            }
			$selected = '';
		}
		$string .= "      </select>\n    </div>\n";

		return($string);
	}	// end function display_field_select()
	
	static function display_field_checkbox($key, $field) {
		// Displays checkbox, checkbox-multiple, and radio field types
		$string = '';
	
		// Get the options list
		$opts_array = explode("\n",$field['options']);
		if ( '' == $opts_array[0] ) {
			if ( 'checkbox' == $field['type']) {
				// use the field name as the option name
				$opts_array[0] = $field['label'];
			} else {
				// Error: no options were entered
				self::$contact_error = 1;
				self::$form_errors['fscf_checkbox'] = __('Error: No options were entered for a checkbox-multiple field in settings.', 'si-contact-form');
				$string .= self::echo_if_error('fscf_checkbox');
			}
		}
		if ( 'checkbox' == $field['type'] ) {
			// Single checkbox
			$string .= "\n    <div ".self::get_this_css('field_div_style').'>'. self::echo_if_error( $field['slug'] ) . "\n"
			. '      <span><input type="checkbox" ';
		$string .= ($field['input_css'] != '') ? self::convert_css($field['input_css']) : self::get_this_css('checkbox_style');
		$string .= ' id="fscf_field' . self::$form_id_num . '_'
			. $key . '" name="' . $field['slug'] . '" value="1"';
			if ( '1' == self::$form_content[$field['slug']] ) {
				$string .= ' checked="checked"';
			}

			if ( $field['attributes'] != '' )
				$string .= ' ' . $field['attributes'];
			$string .= ' /> <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('option_label_style');
		$string .= ' for="fscf_field' . self::$form_id_num . '_' . $key . '">'
			. $field['label'];
			$string .= ( 'true' == $field['req'] ) ? self::$req_field_ind : '';
			$string	.= "</label></span>\n    </div>\n";
	} else {
		// checkbox-multiple or radio
		if ( 'checkbox-multiple' == $field['type'] ) {
			$ftype = 'checkbox';
		} else {
			$ftype = 'radio';
		}
		$string .= "\n    <div ".self::get_this_css('field_div_style').'>'. self::echo_if_error( $field['slug'] ) . "\n";
		$opt_cnt = 1;

		foreach ( $opts_array as $opt ) {
			$opt = trim( $opt );
			if ( 'false' == $field['inline'] && $opt_cnt > 1 )
				$string .= "<br />\n";
            else if ($opt_cnt > 1)
                $string .= "\n";
			$string .= '      <span><input type="'. $ftype . '" ';
            if ($ftype == 'radio')
		        $string .= ($field['input_css'] != '') ? self::convert_css($field['input_css']) : self::get_this_css('radio_style');
            else
                $string .= ($field['input_css'] != '') ? self::convert_css($field['input_css']) : self::get_this_css('checkbox_style');
		$string .= ' id="fscf_field'
			. self::$form_id_num . '_' . $key . '_' . $opt_cnt . '" name="' . $field['slug'];
			if ( 'checkbox' == $ftype ) {
				$string .= '[' . $opt_cnt . ']" value="' . $opt_cnt . '"';
			} else {
				$string .= '" value="' . $opt_cnt . '"';
			}

			if ( 'checkbox-multiple' == $field['type'] ) {
				if ( count(self::$form_content[$field['slug']]) > 0 
					&& in_array($opt_cnt, self::$form_content[$field['slug']]) ) {
					$string .= ' checked="checked"';
					}
			} else if ( $opt_cnt == self::$form_content[$field['slug']] ) {
				$string .= ' checked="checked"';			
			}
            // is this key==value set? Just display the value
            if ( preg_match('/^(.*)(==)(.*)$/', $opt, $matches) ) {
                      $opt = $matches[3];
            }
			if ( $field['attributes'] != '' )
				$string .= ' ' . $field['attributes'];
				$string .= ' /> <label ';
	    $string .= ( $field['label_css'] != '') ? self::convert_css( $field['label_css'] ) : self::get_this_css('option_label_style');
		$string .= ' for="fscf_field' . self::$form_id_num . '_' . $key . '_'
				. $opt_cnt . '">' . $opt . "</label></span>";
			$opt_cnt++;
		}	// end foreach

		$string .= "\n    </div>\n";

		}  // end else

		return($string);
	}	// end function display_field_checkbox

	static function display_field_date($key, $field) {
		$string = '';

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

        $string .= "\n    <div " . self::get_this_css('field_div_style') . '>' . self::echo_if_error( $field['slug'] ) .
			"\n      <input ";
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('field_style');
		$string .= ' type="text" id="fscf_field' . self::$form_id_num . '_' . $key . '" name="' . $field['slug'] . '" value="';
		if ( isset( self::$form_content[$field['slug']] ) && self::$form_content[$field['slug']] != '') {
        	      $string .=  esc_attr( self::$form_content[$field['slug']] );
        } else {
                if ($field['default'] == '[today]') {
                    $date_formatting  = self::convert_date_for_php();
                    $string .=  esc_attr( date($date_formatting) );
                } else {
             	    $string .= $cal_date_array[self::$form_options['date_format']];
                }
        }
		$string .= '" ' . self::$aria_required . ' size="15" ';
		if ( $field['attributes'] != '' )
			$string .= ' ' . $field['attributes'];
		$string .= " />\n    </div>\n";

		return($string);
	}	// end function display_field_date

    static function convert_date_for_php() {

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
        return "m${delim}d${delim}Y";
	if ( $date_format == "dd${delim}mm${delim}yyyy" )
        return "d${delim}m${delim}Y";
	if ( $date_format == "yyyy${delim}mm${delim}dd" )
       return "Y${delim}m${delim}d";

       return "m${delim}d${delim}Y"; ;
   }

	static function display_field_attachment($key, $field) {
		$string = '';
		
		if ( self::$form_options['php_mailer_enable'] != 'php' ) {
			$string .= "\n    <div " . self::get_this_css('field_div_style') . '>' . self::echo_if_error( $field['slug'] )
				. "\n      <input " . self::get_this_css('field_style') . ' type="file" id="fscf_field'
				. self::$form_id_num . '_' . $key . '" name="' . $field['slug'] . '" ' . self::$aria_required . ' size="20"';
			if ( $field['attributes'] != '' )
				$string .= ' ' . $field['attributes'];
			$string .= ' />';
			// This was designed to print the tip if it was not printed on the previous field
			if ( ! self::$printed_tooltip_filetypes) {
				$file_type_message = "<br />\n      <span ".'id="fscf_hint_file_types' . self::$form_id_num.'_'.$key.'" '.self::get_this_css('hint_style').'>';
				$file_type_message .= sprintf( (self::$form_options['tooltip_filetypes'] != '') ? self::$form_options['tooltip_filetypes'] : __( 'Acceptable file types: %s.', 'si-contact-form' ), self::$form_options['attach_types'] );
                $file_type_message .= '<br />';
		        $file_type_message .= sprintf( (self::$form_options['tooltip_filesize'] != '') ? self::$form_options['tooltip_filesize'] :  __( 'Maximum file size: %s.', 'si-contact-form' ), self::$form_options['attach_size']) . "</span>\n";
                //filter hook for file attachment acceptable types message
                $file_type_message = apply_filters( 'si_contact_file_type_message', $file_type_message, self::$form_options,  self::$form_id_num);
                $string .= $file_type_message;
			}
			self::$printed_tooltip_filetypes++;
			$string .= "    </div>\n";
		}		

		return($string);
	}
	
	static function setup_calendar($date_fields) {
		// Set up the popup calendar display for date fields

        foreach ( $date_fields as $v ) {
		   self::$add_date_js_array[] = self::$form_id_num . '_' . $v;
		}

	   if	( self::$add_date_js == '' ) { // only add for 1st form with date fields
		self::$add_date_js = '
<!-- Fast Secure Contact Form plugin - begin date field js - form '.self::$form_id_num.' -->
<script type="text/javascript">
  var ctf_daylist = new Array( \'' . __( 'Su', 'si-contact-form' ) . '\',\'' . __( 'Mo', 'si-contact-form' ) . '\',\'' . __( 'Tu', 'si-contact-form' ) . '\',\'' . __( 'We', 'si-contact-form' ) . '\',\'' . __( 'Th', 'si-contact-form' ) . '\',\'' . __( 'Fr', 'si-contact-form' ) . '\',\'' . __( 'Sa', 'si-contact-form' ) . '\',\'' . __( 'Su', 'si-contact-form' ) . '\',\'' . __( 'Mo', 'si-contact-form' ) . '\',\'' . __( 'Tu', 'si-contact-form' ) . '\',\'' . __( 'We', 'si-contact-form' ) . '\',\'' . __( 'Th', 'si-contact-form' ) . '\',\'' . __( 'Fr', 'si-contact-form' ) . '\',\'' . __( 'Sa', 'si-contact-form' ) . '\' );
  var ctf_months_sh = new Array( \'' . __( 'Jan', 'si-contact-form' ) . '\',\'' . __( 'Feb', 'si-contact-form' ) . '\',\'' . __( 'Mar', 'si-contact-form' ) . '\',\'' . __( 'Apr', 'si-contact-form' ) . '\',\'' . __( 'May', 'si-contact-form' ) . '\',\'' . __( 'Jun', 'si-contact-form' ) . '\',\'' . __( 'Jul', 'si-contact-form' ) . '\',\'' . __( 'Aug', 'si-contact-form' ) . '\',\'' . __( 'Sep', 'si-contact-form' ) . '\',\'' . __( 'Oct', 'si-contact-form' ) . '\',\'' . __( 'Nov', 'si-contact-form' ) . '\',\'' . __( 'Dec', 'si-contact-form' ) . '\' );
  var ctf_monthup_title = \'' . __( 'Go to the next month', 'si-contact-form' ) . '\';
  var ctf_monthdn_title = \'' . __( 'Go to the previous month', 'si-contact-form' ) . '\';
  var ctf_clearbtn_caption = \'' . __( 'Clear', 'si-contact-form' ) . '\';
  var ctf_clearbtn_title = \'' . __( 'Clears any dates selected on the calendar', 'si-contact-form' ) . '\';
  var ctf_maxrange_caption = \'' . __( 'This is the maximum range', 'si-contact-form' ) . '\';
  var ctf_cal_start_day = ' . self::$form_options['cal_start_day'] . ';
  var ctf_date_format = \'';

		if ( self::$form_options['date_format'] == 'mm/dd/yyyy' )
			self::$add_date_js .= 'm/d/Y';
		if ( self::$form_options['date_format'] == 'dd/mm/yyyy' )
			self::$add_date_js .= 'd/m/Y';
		if ( self::$form_options['date_format'] == 'mm-dd-yyyy' )
			self::$add_date_js .= 'm-d-Y';
		if ( self::$form_options['date_format'] == 'dd-mm-yyyy' )
			self::$add_date_js .= 'd-m-Y';
		if ( self::$form_options['date_format'] == 'mm.dd.yyyy' )
			self::$add_date_js .= 'm.d.Y';
		if ( self::$form_options['date_format'] == 'dd.mm.yyyy' )
			self::$add_date_js .= 'd.m.Y';
		if ( self::$form_options['date_format'] == 'yyyy/mm/dd' )
			self::$add_date_js .= 'Y/m/d';
		if ( self::$form_options['date_format'] == 'yyyy-mm-dd' )
			self::$add_date_js .= 'Y-m-d';
		if ( self::$form_options['date_format'] == 'yyyy.mm.dd' )
			self::$add_date_js .= 'Y.m.d';

		self::$add_date_js .= '\';
';
        self::$fscf_use_window_onload = true;
        //filter hook to suppress window.onload = function(){} for sites where it can only happen once per page.
        self::$fscf_use_window_onload = apply_filters( 'si_contact_use_window_onload', self::$fscf_use_window_onload,  self::$form_id_num);
        }

	}	// end function setup_calendar()

	static function display_field_time($key, $field) {
		$string = '';
		
		// the time drop down list array will be made automatically by this code
        $string .= "\n    <div " . self::get_this_css('field_div_style') . '>' . self::echo_if_error( $field['slug'] )
			. "\n      <select ";
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('select_style');
		$string .= ' id="fscf_field' . self::$form_id_num . '_' . $key . '" name="' . $field['slug']
				. '[h]">' . "\n";

		$selected = '';
		// hours
        $string .= '        <option value=""></option>' . "\n";
		$tf_hours = (self::$form_options['time_format'] == '24') ? '23' : '12';
		for ( $keyi = (self::$form_options['time_format'] == '24') ? 0 : 1; $keyi <= $tf_hours; $keyi++ ) {
			$keyi = sprintf( "%02d", $keyi );
			if ( self::$form_content[$field['slug']]['h'] != '' ) {
				if ( self::$form_content[$field['slug']]['h'] == "$keyi" ) {
					$selected = ' selected="selected"';
				}
			}
			$string .= '        <option value="' . esc_attr( $keyi ) . '"' . $selected . '>' . esc_html( $keyi ) . '</option>' . "\n";
			$selected = '';
		}
		$string .= "      </select>:\n      <select ";
		$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('select_style');
		$string .= ' id="fscf_field' . self::$form_id_num . '_' . $key . 'm" name="' . $field['slug'] . '[m]">' . "\n";
		$selected = '';
		// minutes
        $string .= '        <option value=""></option>' . "\n";
		for ( $keyi = 00; $keyi <= 59; $keyi++ ) {
			$keyi = sprintf( "%02d", $keyi );
			if ( self::$form_content[$field['slug']]['m'] != '' ) {
				if ( self::$form_content[$field['slug']]['m'] == "$keyi" ) {
					$selected = ' selected="selected"';
				}
			}
			$string .= '        <option value="' . esc_attr( $keyi ) . '"' . $selected . '>' . esc_html( $keyi ) . '</option>' . "\n";
			$selected = '';
		}
		$string .= '      </select>';
		if ( self::$form_options['time_format'] == '12' ) {
			$string .= "\n      <select ";
			$string .= ($field['input_css'] != '') ? self::convert_css( $field['input_css'] ) : self::get_this_css('select_style');
			$string .= ' id="fscf_field' . self::$form_id_num . '_' . $key . 'ap" name="' . $field['slug'] . '[ap]">' . "\n";
			$selected = '';
			// am/pm
            $string .= '        <option value=""></option>' . "\n"; 
			foreach ( array( esc_html( __( 'AM', 'si-contact-form' ) ), esc_html( __( 'PM', 'si-contact-form' ) ) ) as $k ) {
				if ( self::$form_content[$field['slug']]['ap'] != '' ) {
					if ( self::$form_content[$field['slug']]['ap'] == "$k" ) {
						$selected = ' selected="selected"';
					}
				}
				$string .= '        <option value="' . esc_attr( $k ) . '"' . $selected . '>' . esc_html( $k ) . '</option>' . "\n";
				$selected = '';
			}
			$string .= '      </select>';
		}
		$string .= "\n    </div>\n";

		return($string);
	}	// end function display_field_time()
	
	
	static function get_contact_list($frm_id, $email_to) {
		// Returns a list of email contacts for display
		// $email_to = email to list from form settings
		
		if ( ! self::$global_options )
			self::$global_options = FSCF_Util::get_global_options ( );
		
		$contacts = array();
		$contacts[] = '';	// dummy entry to take up key 0

		// Check for a shortcode mail-to value.  Allowed shortcode email_to: 
		//    Webmaster,user1@example.com (must have name,email)
		// multiple emails allowed
		//    Webmaster,user1@example.com;user2@example.com

		if (self::$global_options['enable_php_sessions'] == 'true') { // this feature only works when PHP sessions are enabled		
		  if ( ! empty($_SESSION["fsc_shortcode_email_to_$frm_id"]) &&
               preg_match( "/,/", $_SESSION["fsc_shortcode_email_to_$frm_id"]  ) ) {
				list($key, $value) = preg_split( '#(?<!\\\)\,#', $_SESSION["fsc_shortcode_email_to_$frm_id"] ); //string will be split by "," but "\," will be ignored
				$key = trim( str_replace( '\,', ',', $key ) ); // "\," changes to ","
				$value = trim( str_replace( ';', ',', $value ) ); // ";" changes to ","
				if ( $key != '' && $value != '' ) {
					$contacts[] = array( 'CONTACT' => FSCF_Util::clean_input($key), 
										 'EMAIL'   => FSCF_Util::clean_input($value) );
				}
		  } else {
             unset($_SESSION["fsc_shortcode_email_to_$frm_id"]);
          }
        }				

		if ( count($contacts) == 1 ) {
			// Nothing from shortcode, so generate the mail-to list from the settings.
			// The drop down list array will be made automatically by this code
			// checks for properly configured Email To: addresses in options.

			$contacts_test = trim( $email_to );
			if ( ! preg_match( "/,/", $contacts_test ) ) {
				if ( FSCF_Util::validate_email( $contacts_test ) ) {
					// user1@example.com
					$contacts[] = array( 'CONTACT'	 => __( 'Webmaster', 'si-contact-form' ), 'EMAIL'		 => $contacts_test );
				}
			} else {
				$ctf_ct_arr = explode( "\n", $contacts_test );
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
									$contacts[] = array( 'CONTACT'	 => esc_html( $key ), 'EMAIL'		 => $value );
								}
							} else {
								// multiple emails here
								// Webmaster,user1@example.com;user2@example.com;user3@example.com;[cc]user4@example.com;[bcc]user5@example.com
								$multi_cc_arr = explode( ";", $value );
								$multi_cc_string = '';
								foreach ( $multi_cc_arr as $multi_cc ) {
									$multi_cc_t = str_replace( '[cc]', '', $multi_cc );
									$multi_cc_t = str_replace( '[bcc]', '', $multi_cc_t );
									if ( FSCF_Util::validate_email( $multi_cc_t ) ) {
										$multi_cc_string .= "$multi_cc,";
									}
								}
								if ( $multi_cc_string != '' ) { // multi cc emails
									$contacts[] = array( 'CONTACT'	 => esc_html( $key ), 'EMAIL'		 => rtrim( $multi_cc_string, ',' ) );
								}
							}
						}
					} // end foreach
				} // end if (is_array($ctf_ct_arr) ) {
			} // end else
		} // end outer if
		
		unset($contacts[0]); // remove dummy entry.. the array keys now start with 1
		return($contacts);
	}	// end function get_contact_list()
	
	static function convert_css( $string ) {
    if ( preg_match( "/^style=\"(.*)\"$/i", $string, $matches ) ) {
			return 'style="' . esc_attr( $matches[1] ) . '"';
		}
		if ( preg_match( "/^class=\"(.*)\"$/i", $string, $matches ) ) {
			return 'class="' . esc_attr( $matches[1] ) . '"';
		}
		return 'style="' . esc_attr( $string ) . '"';
	}	// end function convert_css
		
	static function get_var($form_id_num,$name) {
		// Gets field value, if any, from query parm
		$value = (isset( $_GET["$form_id_num$name"])) ? FSCF_Util::clean_input($_GET["$form_id_num$name"]) : '';
		return $value;
	}
	
	static function get_query_parms() {
		// Check for query arguements, and store them in self::$form_content
		// If nothing is set by query, put in the default, if one exists
		global $fscf_special_slugs;		// List of reserve slug names

		// initialize vars
		self::$have_attach = '';
		
		// Get any field values from query parms
		// Get special fields
        // $special_slugs = array( 'f_name', 'm_name', 'mi_name', 'l_name', 'email2', 'mailto_id', 'subject_id' );
		foreach ( $fscf_special_slugs as $fld_name ) {
			self::$form_content[$fld_name] = self::get_var(self::$form_id_num,$fld_name);
		}

        $default = self::$form_options['fields']['0']['default']; // name field
        $placeholder = self::$form_options['fields']['0']['placeholder'];
        if ( self::$form_options['name_format'] != 'name' && $default != '' && $placeholder != 'true'  ) {
           if ( self::$form_options['name_format'] == 'first_last' ) {
               // find the true default for first, last only
               // is there xx==xx
               if ( !preg_match('/^(.*)(==)(.*)$/', $default, $matches) )
                    $default = 'First Name==Last Name';  // default to proper format
               if ( preg_match('/^(.*)(==)(.*)$/', $default, $matches) ) {
                  if (self::$form_content['f_name'] == '') self::$form_content['f_name'] = $matches[1];
                  if (self::$form_content['l_name'] == '') self::$form_content['l_name'] = $matches[3];
               }
           } else if ( self::$form_options['name_format'] == 'first_middle_last' ) {
               // find the true default for first, middle, last
               // is there xx==xx==xx
               if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $default, $matches) )
                   $default = 'First Name==Middle Name==Last Name';  // default to proper format
               if ( preg_match('/^(.*)(==)(.*)(==)(.*)$/', $default, $matches) ) {
                  if (self::$form_content['f_name'] == '') self::$form_content['f_name'] = $matches[1];
                  if (self::$form_content['m_name'] == '') self::$form_content['m_name'] = $matches[3];
                  if (self::$form_content['l_name'] == '') self::$form_content['l_name'] = $matches[5];
               }
            } else if ( self::$form_options['name_format'] == 'first_middle_i_last' ) {
               // find the true default for first, middle initial, last
               // is there xx==xx==xx
               if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $default, $matches) )
                   $default = 'First Name==Middle Initial==Last Name';  // default to proper format
               if ( preg_match('/^(.*)(==)(.*)(==)(.*)$/', $default, $matches) ) {
                  if (self::$form_content['f_name'] == '') self::$form_content['f_name'] = $matches[1];
                  if (self::$form_content['mi_name'] == '') self::$form_content['mi_name'] = $matches[3];
                  if (self::$form_content['l_name'] == '') self::$form_content['l_name'] = $matches[5];
               }
            }
        }

		// XXX Might need to check for English standard field names, e.g. 'name', as well as the actual field name,
		// which might be translated.  If so, set the field name element to the entry for uame, and unset the name element
		
		// Get regular fields
		foreach (self::$form_options['fields'] as $key => $field ) {
			if ( 'true' == $field['disable'] ) continue;
			$fld_name = $field['slug'];
			switch ( $field['type'] ) {
				case 'time' :
					$vars = array();
					$vars['h']  = self::get_var(self::$form_id_num,$fld_name.'_h');
					$vars['m']  = self::get_var(self::$form_id_num,$fld_name.'_m');
					$vars['ap'] = self::get_var(self::$form_id_num,$fld_name.'_ap');
					self::$form_content[$fld_name] = $vars;
					// XXX need to add use of default for time as "xx:xx am/pm"
					break;

				case 'select' :
				case 'select-multiple' :
				case 'checkbox-multiple' :
					// Checkbox and radio are handled in the default case below
					$opts_array = explode("\n",$field['options']);
					$selected = array();
					if ( 'select-multiple' == $field['type'] || 'checkbox-multiple' == $field['type'] ) {
						$opt_cnt = 1;
						foreach ( $opts_array as $opt ) {
							$sel = self::get_var( self::$form_id_num, $fld_name . '_' . $opt_cnt );
							if ( '1' == $sel ) $selected[] = $opt_cnt;
							$opt_cnt++;
						}
					}

					if ( 0 == count($selected) ) {
						// see if a single option was specified
						$ind = self::get_var(self::$form_id_num,$fld_name);
						if ( is_numeric($ind) && isset($opts_array[+$ind-1]) ) {
                            // XXX to do: it would be nice to allow query input of key # or opt value
							$selected[] = $ind;
						} else if ( '' != $field['default'] ) {
							// Get the value from the default setting
							if ( ! false == strpos( $field['default'], ',') ) {
								// Parse a comma delimited option list
								$olist = explode(',', $field['default']);
								foreach ( $olist as $opt ) {
									if ( is_numeric($opt) && isset($opts_array[+$opt-1]) ) $selected[] = $opt;
								}
							} else if ( is_numeric($field['default']) && isset($opts_array[+$field['default']-1]) )
								$selected[] = $field['default'];
						}
					}
					self::$form_content[$fld_name] = $selected;
					break;

				case 'attachment' :
					self::$have_attach = 'enctype="multipart/form-data" '; // for <form post
					self::$form_content[$fld_name] = self::get_var(self::$form_id_num, $fld_name);
					// There is no default value for an attachment field
					break;

				case 'fieldset' :
				case 'fieldset-close' :
				case 'password' :
					break;

				case 'date' :
					// check to be sure that query or default date is a valid date, or do not use it
					$new_date = self::get_var( self::$form_id_num, $fld_name );
					if ( '' != $new_date & FSCF_Process::validate_date( $new_date, self::$form_id_num ) )
						self::$form_content[$fld_name] = $new_date;
					// XXX Consider adding an error or warning message if date field query parm is invalid
					if ( empty( self::$form_content[$fld_name] ) && '' != $field['default'] && FSCF_Process::validate_date( $field['default'], self::$form_id_num ) ) {
						self::$form_content[$fld_name] = $field['default'];
					}
					break;

				default :
					// Special case: the 'full_name' field has 'name' as the query name
					if ( 'full_name' == $fld_name )
						self::$form_content[$fld_name] = self::get_var(self::$form_id_num, 'name');
					else
						self::$form_content[$fld_name] = self::get_var(self::$form_id_num, $fld_name);
                    // fill in defaults if set
					if ( '' == self::$form_content[$fld_name] && '' != $field['default'] && 'true' != $field['placeholder'] ) {
                           if('message' == $fld_name || 'textarea' == $field['type'])
                               self::$form_content[$fld_name] = str_replace('\n', "\n", $field['default']);
                           else if ('email' == $fld_name && 'false' == self::$form_options['double_email'])
                               self::$form_content[$fld_name] = $field['default'];
                           else if ('email' != $fld_name )
                               self::$form_content[$fld_name] = $field['default'];
                    }

			}	// end switch

		}	// end foreach

        $default = self::$form_options['fields']['1']['default']; // email field
        $placeholder = self::$form_options['fields']['1']['placeholder'];
        if ( 'true' == self::$form_options['double_email'] && $default != '' && $placeholder != 'true'  ) {
            // find the true default for email, email2
            // is there xx==xx
            if ( !preg_match('/^(.*)(==)(.*)(==)(.*)$/', $default, $matches) )
                  $default = 'Email==Re-enter Email'; // default to proper format
            if ( preg_match('/^(.*)(==)(.*)$/', $default, $matches) ) {
                 if (self::$form_content['email'] == '') self::$form_content['email'] = $matches[1];
                 if (self::$form_content['email2'] == '') self::$form_content['email2'] = $matches[3];
            }
        }
		return;

	}	// end function get_query_parms()

	static function get_hidden_fields() {
		// Gets hidden fields from shortcode
		// This is called only if PHP Sessions are enabled
		// Returns an array of hidden fields
		$frm_id = self::$form_id_num;
		$hidden_fields = array();
		$hidden_fields_test = explode( ",", $_SESSION["fsc_shortcode_hidden_$frm_id"]);
		if ( ! empty( $hidden_fields_test ) ) {
			foreach ( $hidden_fields_test as $line ) {
				if ( preg_match( "/=/", $line ) ) {
					list($key, $value) = explode( "=", $line );
					$key = trim( $key );
					$value = trim( $value );
					if ( $key != '' && $value != '' ) 
						$hidden_fields[$key] = $value;
				}
			}
		}
		
		// Save the hidden fields in session for use in processing
		if ( ! empty( $hidden_fields ) )
			$_SESSION["fsc_shortcode_hidden_$frm_id"] = $hidden_fields;
		else
			unset($_SESSION["fsc_shortcode_hidden_$frm_id"]);

		return($hidden_fields);
	}  // end function get_hidden_fields()
	
	static function echo_if_error( $this_error ) {
		// shows contact form errors
		// Settings errors begin with fscf . $form_num (always shown)
		// Entry errors will only be in self::$form_errors for the submitted form
		if ( self::$contact_error ) {
			if ( isset( self::$form_errors[$this_error] ) ) {
				return '
         <div ' . self::get_this_css('error_style') . '>' . esc_html(self::$form_errors[$this_error]) . '</div>' . "\n";
			}
		}
	} // end function echo_if_error

	// needed for making temp directories for attachments and captcha session files
	static function init_temp_dir( $dir ) {
		$dir = trailingslashit( $dir );
		// make the temp directory
		wp_mkdir_p( $dir );
		//@chmod( $dir, 0733 );
		$htaccess_file = $dir . '.htaccess';
		if ( !file_exists( $htaccess_file ) ) {
			if ( $handle = @fopen( $htaccess_file, 'w' ) ) {
				fwrite( $handle, "Deny from all\n" );
				fclose( $handle );
			}
		}
		$php_file = $dir . 'index.php';
		if ( !file_exists( $php_file ) ) {
			if ( $handle = @fopen( $php_file, 'w' ) ) {
				fwrite( $handle, '<?php //do not delete ?>' );
				fclose( $handle );
			}
		}
	}	// end function init_temp_dir

	static function clean_temp_dir( $dir, $minutes = 30 ) {
		// needed for emptying temp directories for attachments and captcha session files
		// deletes all files over xx minutes old in a temp directory
		if ( !is_dir( $dir ) || !is_readable( $dir ) || !is_writable( $dir ) )
			return false;

		$count = 0;
		$list = array( );
		if ( $handle = @opendir( $dir ) ) {
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
	}

	static function is_captcha_enabled($frm_num) {
		// See if captcha is enabled for this user and this form
		// Returns true or false
		
		if ( ! self::$form_options )
			self::$form_options = FSCF_Util::get_form_options ( $frm_num, $use_defaults = true );

		$captcha_enabled = 'no';
		if ( self::$form_options['captcha_enable'] == 'true' ) {
			$captcha_enabled = 'sicaptcha'; // captcha setting is enabled for secure image captcha
		}
        if ( self::$form_options['recaptcha_enable']  == 'true' && (self::$global_options['recaptcha_public_key'] != '' && self::$global_options['recaptcha_secret_key'] != '') ) {
			$captcha_enabled = 'recaptcha'; // captcha setting is enabled for google recaptcha
		}
		// skip the captcha if user is loggged in and the settings allow
		if ( is_user_logged_in() && self::$form_options['captcha_perm'] == 'true' ) {
			// skip the CAPTCHA display if the minimum capability is met
			if ( current_user_can( self::$form_options['captcha_perm_level'] ) ) {
				$captcha_enabled = 'no';
			}
		}
		return ($captcha_enabled);
	}  // end function is_captcha_enabled()
	
	static function display_captcha() {
		// this function adds the captcha to the contact form

		$captchaRequiresError = '';

		$enable_php_sessions = 0;
		if ( self::$global_options['enable_php_sessions'] == 'true' )
			$enable_php_sessions = 1;

		$string = '';

		// Test for some required things, print error message right here if not OK.
		// Code moved in from function captchaCheckRequires() -- only called once
		$captcha_ok = true;
		// Test for some required things, print error message if not OK.
		if ( !extension_loaded( 'gd' ) || !function_exists( 'gd_info' ) ) {
			$captchaRequiresError .= '<p ' . self::get_this_css('error_style') . '>' . __( 'ERROR: si-contact-form.php plugin says GD image support not detected in PHP!', 'si-contact-form' ) . '</p>';
			$captchaRequiresError .= '<p>' . __( 'Contact your web host and ask them why GD image support is not enabled for PHP.', 'si-contact-form' ) . '</p>';
			$captcha_ok = false;
		}
		if ( !function_exists( 'imagepng' ) ) {
			$captchaRequiresError .= '<p ' . self::get_this_css('error_style') . '>' . __( 'ERROR: si-contact-form.php plugin says imagepng function not detected in PHP!', 'si-contact-form' ) . '</p>';
			$captchaRequiresError .= '<p>' . __( 'Contact your web host and ask them why imagepng function is not enabled for PHP.', 'si-contact-form' ) . '</p>';
			$captcha_ok = false;
		}
		if ( !file_exists( FSCF_CAPTCHA_PATH . '/securimage.php' ) ) {
			$captchaRequiresError .= '<p ' . self::get_this_css('error_style') . '>' . __( 'ERROR: si-contact-form.php plugin says captcha_library not found.', 'si-contact-form' ) . '</p>';
			$captcha_ok = false;
		}

		if ( $captcha_ok ) {

			// the captch html
			$string = "\n<div " . self::get_this_css('title_style') . ">\n</div>\n" . '<div id="fscf_captcha_image_div'.self::$form_id_num.'" ';

			// url for captcha image
			$captcha_url_cf = FSCF_Util::get_captcha_url_cf();
			$securimage_show_url = $captcha_url_cf . '/securimage_show.php?';

			$securimage_size = 'width="175" height="60"';
			if ( self::$form_options['captcha_small'] == 'true' ) {
				$securimage_show_url .= 'ctf_sm_captcha=1&amp;';
				$securimage_size = 'width="132" height="45"';
			}

			$parseUrl = parse_url( $captcha_url_cf );
			$securimage_url = $parseUrl['path'];

			if ( !$enable_php_sessions ) { // no sessions
				self::init_temp_dir( FSCF_CAPTCHA_PATH . '/cache/' );

				// clean out old captcha cache files
				self::clean_temp_dir( FSCF_CAPTCHA_PATH . '/cache/' );
				// pick new prefix token
				$prefix_length = 16;
				$prefix_characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
				$prefix = '';
				$prefix_count = strlen( $prefix_characters );
				while ( $prefix_length-- ) {
					$prefix .= $prefix_characters[mt_rand( 0, $prefix_count - 1 )];
				}
				$securimage_show_rf_url = $securimage_show_url . 'prefix=';
				$securimage_show_url .= 'prefix=' . $prefix;
			} else {  // no session
				$securimage_show_rf_url = $securimage_show_url . 'ctf_form_num=' . self::$form_id_num;
				$securimage_show_url .= 'ctf_form_num=' . self::$form_id_num;
			}

			$string .= (self::$form_options['captcha_small'] == 'true') ? self::get_this_css('captcha_div_style_sm') : self::get_this_css('captcha_div_style_m');
			$string .= ">\n" . '      <img id="fscf_captcha_image' . self::$form_id_num . '" ';
			$string .= self::get_this_css('captcha_image_style');
			$string .= ' src="' . $securimage_show_url . '" ' . $securimage_size . ' alt="';
			$string .= (self::$form_options['tooltip_captcha'] != '') ? esc_html( self::$form_options['tooltip_captcha'] ) : esc_html( __( 'CAPTCHA', 'si-contact-form' ) );
			$string .='" title="';
			$string .= (self::$form_options['tooltip_captcha'] != '') ? esc_html( self::$form_options['tooltip_captcha'] ) : esc_html( __( 'CAPTCHA', 'si-contact-form' ) );
			$string .= '" />' . "\n";

			if ( !$enable_php_sessions ) { // no sessions
				$string .= '      <input id="fscf_captcha_prefix' . self::$form_id_num . '" type="hidden" name="fscf_captcha_prefix' . self::$form_id_num . '" value="' . esc_attr( $prefix ) . '" />' . "\n";
			}

			$string .= '     <div id="fscf_captcha_refresh' . self::$form_id_num . '">' . "\n";
			$string .= '        <a href="#" rel="nofollow" title="';
			$string .= (self::$form_options['tooltip_refresh'] != '') ? esc_html( self::$form_options['tooltip_refresh'] ) : esc_html( __( 'Refresh', 'si-contact-form' ) );
			if ( !$enable_php_sessions ) { // no sessions
				$string .= '" onclick="fscf_captcha_refresh(\'' . self::$form_id_num . '\',\'' . $securimage_url . '\',\'' . $securimage_show_rf_url . '\'); return false;">' . "\n";
			} else {
				$string .= '" onclick="document.getElementById(\'fscf_captcha_image' . self::$form_id_num . '\').src = \'' . $securimage_show_url . '&amp;sid=\'' . ' + Math.random(); return false;">' . "\n";
			}
			$string .= '        <img src="' . $captcha_url_cf . '/images/refresh.png" width="22" height="20" alt="';
			$string .= (self::$form_options['tooltip_refresh'] != '') ? esc_html( self::$form_options['tooltip_refresh'] ) : esc_html( __( 'Refresh', 'si-contact-form' ) );
			$string .= '" ';
			$string .= self::get_this_css('captcha_reload_image_style');
			$string .= ' onclick="this.blur();" /></a>
     </div>
</div>

<div id="fscf_captcha_field'.self::$form_id_num.'" ' . self::get_this_css('title_style') . '>
     <label '.self::get_this_css('label_style').' for="fscf_captcha_code' . self::$form_id_num . '">';
			$string .= (self::$form_options['title_capt'] != '') ? self::$form_options['title_capt'] : __( 'CAPTCHA Code:', 'si-contact-form' );
			$string .= self::$req_field_ind . '</label>
</div>
<div ' . self::get_this_css('field_div_style') . '>'
					. self::echo_if_error( 'captcha' )
					. "\n     <input " . self::get_this_css('captcha_input_style')
					. ' type="text" value="" autocomplete="off" id="fscf_captcha_code' . self::$form_id_num . '" name="captcha_code" ' . self::$aria_required . ' />';
			$string .= "\n</div>";
		} else {
			$string .= $captchaRequiresError;
		}
		return $string;
	}	// end function display_captcha()


	static function display_recaptcha() {
		// this function adds the recaptcha div to the contact form

        self::$add_recaptcha_script = 1; // used in util to decide to print js or not

        $theme = (self::$form_options['recaptcha_dark'] == 'true') ? 'dark' : 'light';
        $size = (self::$form_options['captcha_small'] == 'true') ? 'compact' : 'normal';

        // make recaptcha ompatible with multiforms
        self::$add_recaptcha_js_array[] = self::$form_id_num."||".esc_attr(self::$global_options['recaptcha_public_key'])."||$size||$theme"; // used in util to build js
        $string = "\n<div " . self::get_this_css('title_style') . ">\n</div>\n" .  self::echo_if_error( 'captcha' )  . '<div id="fscf_recaptcha' . self::$form_id_num . '"></div>';

        return $string;

    }

	static function display_thank_you() {
		// Displays thank you message upon successful form submission

		// what gets printed after the form is sent, unless redirect is on.
		$ctf_form_style = FSCF_Display::get_this_css('form_style');

		$ctf_thank_you = '
<!-- Fast Secure Contact Form plugin '.esc_html(FSCF_VERSION).' - begin - FastSecureContactForm.com -->
<div id="FSContact' . self::$form_id_num . '" ' . $ctf_form_style.'>
';

		if (self::$form_options['border_enable'] == 'true') {
			$ctf_thank_you .= '<fieldset id="fscf_form_fieldset' . self::$form_id_num . '" '. FSCF_Display::get_this_css('border_style') . '>
';
		if (self::$form_options['title_border'] != '')
			$ctf_thank_you .= '      <legend>'.esc_html(self::$form_options['title_border']).'</legend>';
		}


		$ctf_thank_you .= '
	<div id="fscf_redirect'.self::$form_id_num.'" '.FSCF_Display::get_this_css('redirect_style').'>
';
		$text_message_sent = (self::$form_options['text_message_sent'] != '') ? self::$form_options['text_message_sent'] : __('Your message has been sent, thank you.', 'si-contact-form'); // can have HTML
         $ctf_thank_you .= $text_message_sent;
		if (FSCF_Process::$redirect_enable == 'true') {
			$ctf_thank_you .= '
		<br />
		<img id="fscf_redirect_image'.self::$form_id_num.'" src="'.plugins_url( 'si-contact-form/includes/ctf-loading.gif' ).'" alt="'.esc_attr(__('Redirecting', 'si-contact-form')).'" />'.
		'<span id="fscf_redirect_word'.self::$form_id_num.'">'.__('Redirecting', 'si-contact-form').'</span>';
		} else {
         if (self::$form_options['print_form_enable'] == 'true'){

 $ctf_thank_you .= '
<br />
<input type="button" id="fscf_print_button'.self::$form_id_num.'" value="';
 $ctf_thank_you .= (self::$form_options['text_print_button'] != '') ? self::$form_options['text_print_button'] : __('View / Print your message', 'si-contact-form');
 $ctf_thank_you .= '" onclick="fscfPrintContent(\'fscf_print_div\'); return false;" />
<div id="fscf_print_div" style="display:none">
<h2>'.$text_message_sent.'</h2>

<p>';

 $msg_print = esc_html(FSCF_Process::$email_msg_print);
 $msg_print = str_replace( '&lt;b&gt;', '<b>', $msg_print );
 $msg_print = str_replace( '&lt;/b&gt;', '</b>', $msg_print );
 $msg_print = str_replace( array( "\r\n", "\r", "\n" ), "<br />", $msg_print );
 $ctf_thank_you .= $msg_print;

 $ctf_thank_you .= '</p>

<script type="text/javascript">
function fscfPrintContent(id){
str=document.getElementById(id).innerHTML
newwin=window.open(\'\',\'printwin\',\'width=1000\')
newwin.document.write(\'<HT\')
newwin.document.write(\'ML>\n<HE\n\')
newwin.document.write(\'AD>\n\')
newwin.document.write(\'<TITLE>Print Window</TITLE>\n\')
newwin.document.write(\'<script>\n\')
newwin.document.write(\'function chkstate(){\n\')
newwin.document.write(\'if(document.readyState=="complete"){\n\')
newwin.document.write(\'window.close()\n\')
newwin.document.write(\'}\n\')
newwin.document.write(\'else{\n\')
newwin.document.write(\'setTimeout("chkstate()",2000)\n\')
newwin.document.write(\'}\n\')
newwin.document.write(\'}\n\')
newwin.document.write(\'function print_win(){\n\')
newwin.document.write(\'window.print();\n\')
newwin.document.write(\'chkstate();\n\')
newwin.document.write(\'}\n\')
newwin.document.write(\'<\/script>\n\')
newwin.document.write(\'</HE\')
newwin.document.write(\'AD>\n\')
newwin.document.write(\'<BO\')
newwin.document.write(\'DY onload="print_win()">\n\')
newwin.document.write(str)
newwin.document.write(\'</BO\')
 newwin.document.write(\'DY>\n\')
newwin.document.write(\'</HT\')
newwin.document.write(\'ML>\n\')
newwin.document.close()
}
</script>
</div>
';
}
        }
		$ctf_thank_you .= '
	</div>
';

if (!empty(self::$form_options['success_page_html'])) {
      $ctf_thank_you .= self::$form_options['success_page_html'] .'
';

}
		if (self::$form_options['border_enable'] == 'true') {
			$ctf_thank_you .= '   </fieldset>';
		}
		$ctf_thank_you .= '
</div>
';

		$ctf_thank_you .= '<!-- Fast Secure Contact Form plugin '.esc_html(FSCF_VERSION).' - end - FastSecureContactForm.com -->
';

     //filter hook for thank_you_message
	  return apply_filters( 'si_contact_thank_you_message', $ctf_thank_you,  self::$form_id_num);
	}
	
	static function set_form_error($fld, $msg) {
		// Sets a form error for field $fld with message $msg
		// This is called from FSCF_Process class functions
		// The key is 'field' plus the field index number, or a special name such as
		// capctha, f_name, fscf_select, etc.
		self::$form_errors[$fld] = $msg;
		self::$contact_error = 1;	// Set the error flag
	}

	static function check_form_errors() {
		// Returns 1 if there are form errors, and 0 if not
		// Called from FSCF_Process::process_form()
		return(self::$contact_error);
	}

	static function ctf_notes($notes) {
		$html = "\n    <div ".self::get_this_css('clear_style')."></div>\n$notes\n";
        // filter hook for html_before_after
		return apply_filters( 'si_contact_html_before_after', $html,  self::$form_id_num);
	}

	static function get_form_action_url() {
	// returns the URL for the WP page the form was on

		if ( function_exists( 'qtrans_convertURL' ) )
		// compatible with qtranslate plugin
		// In case of multi-lingual pages, the /de/ /en/ language url is used.
			$form_action_url = qtrans_convertURL( strip_tags( $_SERVER['REQUEST_URI'] ) );
		else
			$form_action_url =
					'http://' . strip_tags( $_SERVER['HTTP_HOST'] ) . strip_tags( $_SERVER['REQUEST_URI'] );

		// set the type of request (SSL or not)
		if ( is_ssl() )
			$form_action_url = preg_replace( '|http://|', 'https://', $form_action_url );

        //filter hook for form action URL
		return apply_filters( 'si_contact_form_action_url', $form_action_url,  self::$form_id_num);

	}  // end function form_action_url

   	static function is_vcita_activated() {
         if ( self::$form_options['vcita_approved'] == 'true' && !empty( self::$form_options['vcita_uid'] ) )
              return true;
         else
              return false;
    }

	static function display_vcita_scheduler_button( $string ) {
      // vcita_scheduling_button enabled and is_vcita_activated has already been checked
	  if (self::$form_options['vcita_scheduling_button'] == 'true'){
        $string .= '<div id="fscf_button_div_vcita' . self::$form_id_num . '" '.self::get_this_css('vcita_div_button_style'). ">\n<a ".'id="fscf_button_vcita' . self::$form_id_num . '" ' . self::get_this_css('vcita_button_style');
		$string .=  " target='_blank' class='vcita-set-meeting' href=\"http://".self::$global_options['vcita_site']."/meeting_scheduler?v=" . self::$form_options['vcita_uid'] . "\"";
		$string .= '>' . self::$form_options['vcita_scheduling_button_label'].'</a>';
        $string .= "\n</div>";
	 }

	  return($string);
  }	// end function display_vcita_scheduler_button

	
}  // end class FSCF_Display

// end of file
