<?php

/**
 * Description of class-fscf-util
 * Utility class sets default variables, wp actions, and sanitize functions
 * Functions are called statically, so no need to instantiate the class
 * @authors Mike Challis and Ken Carlson
 */

class FSCF_Util {

	static $global_defaults, $form_defaults, $field_defaults;
	static $global_options, $form_options, $admin_notices;

	static function setup() {

        // Come here when the plugin is run

        // load plugin textdomain for languages
		add_action('plugins_loaded', 'FSCF_Util::fscf_init_languages');

        // imports old settings on plugin activate or first time upgrade from 3.xx to 4.xx
        add_action('init', 'FSCF_Util::import',1);

		// will start PHP session only if they are enabled (not enabled by default)
		add_action('init', 'FSCF_Util::fscf_init_session',1);

		// process the form POST logic
		add_action('init', 'FSCF_Process::process_form',10);

		// use shortcode to print the contact form or process contact form logic
		// can use dashes or underscores: [si-contact-form] or [si_contact_form]
		add_shortcode('si_contact_form', 'FSCF_Display::process_short_code', 1);
		add_shortcode('si-contact-form', 'FSCF_Display::process_short_code', 1);

		// If you want to use shortcodes in your widgets or footer
		add_filter('widget_text', 'do_shortcode');
		add_filter('wp_footer', 'do_shortcode');

        add_filter('script_loader_tag', 'FSCF_Util::make_fscf_script_async', 10, 3);  // for loading recaptcha js async

		if ( is_admin() ) {
			// Set up admin actions
			add_action( 'admin_menu', 'FSCF_Options::register_options_page' );

            // imports old settings on plugin activate or first time upgrade from 3.xx to 4.xx
            add_action( 'admin_init', 'FSCF_Util::import',1 );

			add_action( 'admin_init', 'FSCF_Options::initialize_options' );

			add_action( 'admin_notices', 'FSCF_Util::admin_notice' );

			add_action( 'admin_enqueue_scripts', 'FSCF_Util::enqueue_admin_scripts' );

            add_action( 'admin_footer', 'FSCF_Util::fscf_admin_footer' );

			// adds "Settings" link to the plugin action page
			add_filter( 'plugin_action_links', 'FSCF_Util::fscf_plugin_action_links',10,2);
		} else {
              add_action( 'wp_footer', 'FSCF_Util::fscf_wp_footer',11 );
		}

		return;
	}
	
	static function import() {
		// called all the time

		// Load global options
		self::$global_options = get_option( 'fs_contact_global' );
		if ( self::$global_options ) {
			// Update the options tables entries if necessary
			self::update_options_version();
		} else {
               // an import might be needed, run it now
               self::import_forced();
		}

		// New options table entries for individual forms will be created by FSCF_Options::get_options()
		// when it is called, so don't need to do it here.

		return;
	}

	static function import_forced($force = '') {
            // conditionally imports old settings only if they exist

			// see if upgrading from an older version
			$old_global_options = get_option('si_contact_form_gb');
			if ($old_global_options) {
                // import now
				require_once FSCF_PATH . 'includes/class-fscf-import.php';
				FSCF_Import::import_old_version($force);
			} else {
				// old options did not exist
				self::$global_options = FSCF_Util::get_global_options();
				// Is this is a really old version, prior to 2.6.5 (earlier versions did not have global options)
				$temp = get_option( 'si_contact_form' );
				if ( ! empty($temp) ) {
					FSCF_Util::add_admin_notice(__( '<b>Warning</b>: Fast Secure Contact Form cannot import settings because the previous version is too old. Installed as new.', 'si-contact-form' ), 'error');
					self::$global_options['import_success'] = false;
					self::$global_options['import_msg'] = true;
					update_option( 'fs_contact_global', self::$global_options );
				}
			}

		return;
	}

	static function fscf_init_languages() {
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('si-contact-form', false, 'si-contact-form/languages' );
		}
	}
	
	static function fscf_init_session() {
		self::get_global_options();
		// start the PHP session if enabled - used by shortcode attributes (and the CAPTCHA, but only when enable_php_sessions)
        // PHP Sessions are no longer enabled by default allowing for best compatibility with servers, caching, themes, and other plugins.
        // This should resolve any PHP sessions related issues some users had.
		if ( self::$global_options['enable_php_sessions'] == 'true' ) {
			FSCF_Util::start_session();
		}
	}

	static function enqueue_admin_scripts( $hook ) {
		// Add jquery and css for tabs on options page only for this plugin
		if( strpos ( $hook, 'si-contact-form' ) > 0 ) {
            wp_enqueue_script('thickbox'); // for constant contact addon
            wp_enqueue_style('thickbox'); // for constant contact addon
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-sortable' );  // used for drag and drop ordering of fields
			wp_enqueue_script( 'fscf_scripts_admin', plugins_url( 'si-contact-form/includes/fscf-scripts-admin.js' ), false, FSCF_BUILD );
		   	wp_enqueue_script( 'fscf_scripts', plugins_url( 'si-contact-form/includes/fscf-scripts.js' ), false, FSCF_BUILD );
			// Load jquery-ui css, depending on WP version
			if ( version_compare( get_bloginfo('version'), '3.5', '<' ) )
				wp_enqueue_style( 'jquery-ui', plugins_url( 'si-contact-form/includes/jquery-ui-1820.css' ) );
			else
				wp_enqueue_style( 'jquery-ui', plugins_url( 'si-contact-form/includes/jquery-ui-191.css' ) );

			// Set up array of phrases used in scripts-admin.js for translation
			// NOTE: Some of the phrases below are used to match button values in FSCF_Option.
			// These must match EXACTLY, or the button presses will not be recognized
			// XXX consider storing button values in constants so that they WILL be the same
			$translation_array = array(
                'show_details' => __('Show Details', 'si-contact-form'),
                'hide_details' => __('Hide Details', 'si-contact-form'),
				'save_changes' => __('Save Changes', 'si-contact-form'),
				'send_test' => __('Send Test', 'si-contact-form'),
				'copy_settings' => __('Copy Settings', 'si-contact-form'),
				'backup_settings' => __('Backup Settings', 'si-contact-form'),
				'restore_settings' => __('Restore Settings', 'si-contact-form'),
				'confirm_change' => __('Are you sure you want to permanently make this change?', 'si-contact-form'),
				'unsaved_changes' => __('You have unsaved changes.', 'si-contact-form'),
				'reset_form' => __('Reset Form', 'si-contact-form'),
                'reset_all_styles' => __('Reset Styles on all forms', 'si-contact-form'),
				'delete_form' => __('Delete Form', 'si-contact-form'),
                'import_old_forms' => __('Import forms from 3.xx version', 'si-contact-form'),
				);
			wp_localize_script( 'fscf_scripts_admin', 'fscf_transl', $translation_array );
			wp_enqueue_style( 'fscf-styles-admin', plugins_url( 'si-contact-form/includes/fscf-styles-admin.css' ), false, FSCF_BUILD );
		}
	}

     static function add_recaptcha_js() {
       global $fs_recaptcha_add_script;

     // for loading recaptcha js in footer conditionally if form has recaptcha enabled or not
     // makes multiforms compatible on same page
                $string = '
<!-- Fast Secure Contact Form plugin - begin recaptcha js -->
<script type="text/javascript">
';
		foreach ( FSCF_Display::$add_recaptcha_js_array as $v ) {
                  //"self::$form_id_num||$site_key||$size||$theme";
                   $pieces = explode("||", $v);
			$string .= "var fscf_recaptcha$pieces[0];
";
        }

      $string .= 'var fscfReCAPTCHA = function() {
// render all collected fscf recaptcha instances
// note if you have other recaptcha plugins, one of the plugins might not load any recaptchas
// however this plugin is compatible with the recaptcha on Fast Secure reCAPTCHA plugin';

       foreach ( FSCF_Display::$add_recaptcha_js_array as $v ) {
                  //"self::$form_id_num||$site_key||$size||$theme";
          $pieces = explode("||", $v);
		  $string .= "
fscf_recaptcha".esc_js($pieces[0])." = grecaptcha.render('fscf_recaptcha".esc_js($pieces[0])."', {'sitekey' : '".esc_js($pieces[1])."', 'size' : '".esc_js($pieces[2])."', 'theme' : '".esc_js($pieces[3])."'});";

	   }
   if ($fs_recaptcha_add_script) { // try to detect Fast Secure reCAPTCHA plugin and fire the FS reCAPTCHA too
       wp_dequeue_script( 'fast-secure-recaptcha' );
     $string .= "
     // Fast Secure reCAPTCHA plugin detected, enabling automatic compatibility with it's javacripts, fire it's onload function now
     fsReCAPTCHA();";
   }
        $string .= "
};
</script>
<!-- Fast Secure Contact Form plugin - end recaptcha js -->  \n\n";

        echo $string;

     }

    static function add_date_js() {
         // add js for forms with date fields
         // makes multiforms compatible on same page
        //wp_enqueue_style( 'fscf_date_style', plugins_url( 'si-contact-form/date/ctf_epoch_styles.css' ), false, FSCF_BUILD );
        wp_enqueue_script( 'fscf_date_js', plugins_url( 'si-contact-form/date/ctf_epoch_classes.js' ), false, FSCF_BUILD );

        echo FSCF_Display::$add_date_js;

        $string = '  var';
		$date_var_string = '';
		foreach ( FSCF_Display::$add_date_js_array as $v ) {
			$date_var_string .= ' dp_cal' . "$v,";
		}
		$date_var_string = substr( $date_var_string, 0, -1 );
		$string .= "$date_var_string;\n";
        if (FSCF_Display::$fscf_use_window_onload)
		    $string .= '  window.onload = function () {
';
		foreach ( FSCF_Display::$add_date_js_array as $v ) {
			$string .= "    dp_cal$v = new Epoch('epoch_popup$v','popup',document.getElementById('fscf_field$v'));\n";
		}
        if (FSCF_Display::$fscf_use_window_onload)
		   $string .= "  };\n";

        $string .= "</script>\n";
        echo $string;
?>
<script type="text/javascript">
//<![CDATA[
var fscf_css = "\n\
<style type='text/css'>\n\
@import url('<?php echo plugins_url( 'si-contact-form/date/ctf_epoch_styles.css').'?ver='.FSCF_BUILD; ?>');\n\
</style>\n\
";
jQuery(document).ready(function($) {
$('head').append(fscf_css);
});
//]]>
</script>
<?php

        echo "<!-- Fast Secure Contact Form plugin - end date field js -->\n\n";

    }

	static function fscf_wp_footer() {
		// Add js and css needed for the forms

		if ( isset(FSCF_Display::$add_fscf_script) && FSCF_Display::$add_fscf_script ) {
			// only include if a form is on this page or post
		   	wp_enqueue_script( 'jquery-ui-core' ); // needed for the feature "Has the form already been submitted?  If so, reset the form"
			//wp_enqueue_style( 'fscf-styles', plugins_url( 'si-contact-form/includes/fscf-styles.css' ), false, FSCF_BUILD );
			wp_enqueue_script( 'fscf_scripts', plugins_url( 'si-contact-form/includes/fscf-scripts.js' ), false, FSCF_BUILD );
		}
        if ( isset(FSCF_Display::$add_placeholder_script) && FSCF_Display::$add_placeholder_script ) {
            // makes placeholder work on old browsers
            wp_enqueue_script( 'fscf_placeholders', plugins_url( 'si-contact-form/includes/fscf-placeholders.min.js' ), false, FSCF_BUILD );
        }
        if ( isset(FSCF_Display::$add_date_js) && FSCF_Display::$add_date_js != '' ) {
            // add js for forms with date fields
            FSCF_Util::add_date_js();
        }
        if ( isset(FSCF_Display::$add_recaptcha_script) && FSCF_Display::$add_recaptcha_script ) {
           //wp_enqueue_script( 'fscf-recaptcha', 'https://www.google.com/recaptcha/api.js' );
              // loads conditionally if forms have recaptcha enabled
              // makes multiforms compatible on same page
            wp_enqueue_script( 'fscf-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=fscfReCAPTCHA&render=explicit' );
            FSCF_Util::add_recaptcha_js();
        }
	}

    static function fscf_admin_footer() {
		// add placeholder javascript in form preview page only if needed
        if ( isset(FSCF_Display::$placeholder) && FSCF_Display::$placeholder) {
             // makes placeholder work on old browsers
             wp_enqueue_script( 'fscf_placeholders', plugins_url( 'si-contact-form/includes/fscf-placeholders.min.js' ), false, FSCF_BUILD );
        }
        if ( isset(FSCF_Display::$add_date_js) && FSCF_Display::$add_date_js != '' ) {
            // add js for forms with date fields
            FSCF_Util::add_date_js();
        }
        if ( isset(FSCF_Display::$add_recaptcha_script) && FSCF_Display::$add_recaptcha_script ) {
           //wp_enqueue_script( 'fscf-recaptcha', 'https://www.google.com/recaptcha/api.js' );
           // loads conditionally if forms have recaptcha enabled
           // makes multiforms compatible on same page
           wp_enqueue_script( 'fscf-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=fscfReCAPTCHA&render=explicit' );
           FSCF_Util::add_recaptcha_js();
        }
    }

   static function make_fscf_script_async( $tag, $handle, $src ) {
     // to support multiple forms on one page, google recaptcha js needs to be loded async
     // make wordpress wp_enqueue_script load js async, but only for this fscf-recaptcha handle
    if ( 'fscf-recaptcha' != $handle ) {
        return $tag;
    }
    return str_replace( '<script', '<script async defer', $tag );
  }



	static function admin_notice() {
		// Displays admin notices, if any, at top of admin screen
		// The notice will appear the next time the WP 'admin_notices' action occurs
		self::get_global_options();
		if ( ! empty(self::$global_options['admin_notices'])) {
			foreach ( self::$global_options['admin_notices'] as $notice ) {
				echo $notice;
			}
			unset(self::$global_options['admin_notices']);
			update_option( 'fs_contact_global', self::$global_options );
		}
	}
	
	static function add_admin_notice($text, $type) {
		// Adds an admin notice, to be displayed at the top of the admin screen
		self::get_global_options ();
		self::$global_options['admin_notices'][] = '    <div class="' . $type . '">
        <p>' . $text . '</p>
    </div>
';
		update_option( 'fs_contact_global', self::$global_options );
	}
	
	static function fscf_plugin_action_links( $links, $file ) {
		//Static so we don't call plugin_basename on every plugin row.
		static $this_plugin;
		if ( ! $this_plugin )
			$this_plugin = plugin_basename( FSCF_FILE );

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="options-general.php?page=si-contact-form/si-contact-form.php">' . __( 'Settings', 'si-contact-form' ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}
		return $links;
	} // end function fscf_plugin_action_links

	static function start_session() {
		// Start PHP Session  - used optionally by CAPTCHA and shortcode attributes
        // NOTE: PHP sessions are OFF by default! and not recommended for best compatibility with servers, caching, themes, and other plugins
		// this has to be set before any header output
		// start cookie session
		if ( !isset( $_SESSION ) ) { // play nice with other plugins
			//set the $_SESSION cookie into HTTPOnly mode for better security
			if ( version_compare( PHP_VERSION, '5.2.0' ) >= 0 )  // supported on PHP version 5.2.0  and higher
				@ini_set( "session.cookie_httponly", 1 );
			session_cache_limiter( 'private, must-revalidate' );
			session_start();
		}

	} // end function start_session
	
	static function get_global_options() {
		// get plugin options from the WP Options table
		// if the options array does not exist, use the defaults

		// Load global options
			self::$global_options = get_option( 'fs_contact_global' );
			if ( ! self::$global_options ) {
				// Global options array does not exist, so create it
				if ( !isset( self::$global_defaults ) )
					self::set_defaults( );
				update_option( 'fs_contact_global', self::$global_defaults );
				self::$global_options = get_option( 'fs_contact_global' );
			}

        // if I added any new $global_defaults settings without changing the plugin version number
        // fill in any missing $global_options with the $global_defaults value so there will be no errors
        if ( ! isset( self::$global_defaults ) )
			self::set_defaults ( );
        if ( is_array(self::$global_options) )
               self::$global_options = array_merge( self::$global_defaults, self::$global_options );
			
		return(self::$global_options);
	}  // end function get_global_options()
	
	static function get_form_options( $form_num, $use_defaults ) {
		// Get form options for $form_num from WP Options table
		// If $use_defaults is true, use defaults if form does not exist

		if ( ! isset( self::$global_defaults ) )
			self::set_defaults ( );
		// Load global options if necessary
		if ( ! isset( self::$global_options ) )
			self::get_global_options();
		
		$form_options = false;
		if ( is_numeric($form_num) && $form_num > 0 ) {
			// Load form options
			$form_option_name = 'fs_contact_form' . $form_num;
			$form_options = get_option( $form_option_name );
			if ( ! $form_options && $use_defaults ) {
				// Form options array doesn't exist, so create it
				if ( "" == self::$form_defaults['form_name'] )
					self::$form_defaults['form_name'] = __('Form', 'si-contact-form') .' '. $form_num;
				update_option( $form_option_name, self::$form_defaults );
				$form_options = get_option( $form_option_name );
			}
		}
        // if I added any new $form_defaults settings without changing the plugin version number
        // fill in any missing $form_options with the $form_defaults value so there will be no errors
        if ( is_array($form_options) )
               $form_options = array_merge( self::$form_defaults, $form_options );

		return($form_options);
	}
	
	static function update_options_version() {
		// Updates the global and form options table entries if necessary
		// Called on plugin activation if global options exist

		if ( version_compare( FSCF_VERSION, self::$global_options['fscf_version'], '>' ) ) {
			// Update the global options
			// Unset any removed global options here:
			//

			if ( version_compare( self::$global_options['fscf_version'], '4.0', '<' ) ) {
				// this is where you can run code on a specific version increase specified above
			}
			
			// Merge in global defaults in case there are new global options entries
			if ( !isset( self::$global_defaults ) )
				self::set_defaults();
			self::$global_options = array_merge( self::$global_defaults, self::$global_options );
			self::$global_options['fscf_version'] = FSCF_VERSION;
			update_option( 'fs_contact_global', self::$global_options );

			// Update the form options
			foreach ( self::$global_options['form_list'] as $key => $form ) {
				self::$form_options = get_option( 'fs_contact_form' . $key );
				if ( self::$form_options ) {
					self::$form_options = array_merge( self::$form_defaults, self::$form_options );
					// Note: any deleted form options will be removed the next time the form is saved
					// Update the field arrays
					foreach ( self::$form_options['fields'] as $k => $fld ) {
						self::$form_options['fields'][$k] = array_merge ( self::$field_defaults, $fld );
					}
					update_option( 'fs_contact_form' . $key, self::$form_options );
				}

			}	// end outer foreach
		}
	}	// end function update_options_version()


	static function set_defaults() {
		// Set up default values

		// Default global options array
		self::$global_defaults = array(
			'fscf_version'		  => FSCF_VERSION,
			'donated'			  => 'false',
            'recaptcha_public_key'	=> '',
            'recaptcha_secret_key'	=> '',
            'vcita_auto_install'  => 'false',  // vCita Global Settings
            'vcita_dismiss'       => 'false', // vCita Global Settings
            'vcita_initialized'   => 'false', // vCita Global Settings
            'vcita_show_disable_msg'   => 'false', // vCita Global Settings
            'vcita_site'          => 'www.vcita.com', // vCita Global Settings
			'enable_php_sessions' => 'false',
			'num_standard_fields' => '4',  // Number of fields defined as standard fields
			// .. if you change this, there are lots of other changes needed to the code!
			'max_form_num'	      => '2', // Highest form ID (used to assign ID to new form)
			// When forms are deleted, the remaining forms are NOT renumberd, so max_form_num might be greater than
			// the number of existing forms
			// import may add a setting: 'import_success' = 'true' (successful) or 'false" (couldn't import)
			'form_list'			 => array(
				'1'	 => 'Form 1',
				'2'	 => 'Form 2'
			)
		);
		
		// Default style settings
		$style_defaults = self::set_style_defaults();
		
		// Default options for a single contact form
		self::$form_defaults = array(
			 'form_name' => __('New Form', 'si-contact-form'),
			 'welcome' => __('<p>Comments or questions are welcome.</p>', 'si-contact-form'),
             'after_form_note' => '',
			 'email_to' => __('Webmaster', 'si-contact-form').','.get_option('admin_email'),
			 'php_mailer_enable' => 'wordpress',
			 'email_from' => '',
			 'email_from_enforced' => 'false',
			 'email_reply_to' => '',
			 'email_bcc' => '',
			 'email_subject' => get_option('blogname') . ' ' .__('Contact:', 'si-contact-form'),
			 'email_subject_list' => '',
			 'name_format' => 'name',
			 'preserve_space_enable' => 'false',
			 'double_email' => 'false',
			 'name_case_enable' => 'false',
			 'sender_info_enable' => 'true',
			 'domain_protect' => 'true',
			 'domain_protect_names' => '',
			 'anchor_enable' => 'true',
			 'email_check_dns' => 'false',
             'email_check_easy' => 'false',
			 'email_html' => 'false',
			 'email_inline_label' => 'false',
             'email_hide_empty' => 'false',
             'print_form_enable' => 'false',
             'email_keep_attachments' => 'false',
			 'akismet_disable' => 'false',
			 'akismet_send_anyway' => 'true',
			 'captcha_enable' => 'true',
			 'captcha_small' => 'false',
			 'captcha_perm' => 'false',
			 'captcha_perm_level' => 'read',
             'recaptcha_enable' => 'false',
             'recaptcha_dark' => 'false',
             'honeypot_enable' => 'false',
			 'redirect_enable' => 'true',
			 'redirect_seconds' => '3',
			 'redirect_url' => get_option('home'),
			 'redirect_query' => 'false',
			 'redirect_ignore' => '',
			 'redirect_rename' => '',
			 'redirect_add' => '',
			 'redirect_email_off' => 'false',
			 'silent_send' => 'off',
			 'silent_url' => '',
			 'silent_ignore' => '',
			 'silent_rename' => '',
			 'silent_add' => '',
             'silent_conditional_field' => '',
             'silent_conditional_value' => '',
			 'silent_email_off' => 'false',
			 'export_ignore' => '',
			 'export_rename' => '',
			 'export_add' => '',
			 'export_email_off' => 'false',
			 'date_format' => 'mm/dd/yyyy',
			 'cal_start_day' => '0',
			 'time_format' => '12',
			 'attach_types' =>  'doc,docx,pdf,txt,gif,jpg,jpeg,png',
			 'attach_size' =>   '1mb',
			 'textarea_html_allow' => 'false',
			 'enable_areyousure' => 'false',
             'enable_submit_oneclick' => 'true',
			 'auto_respond_enable' => 'false',
			 'auto_respond_html' => 'false',
			 'auto_respond_from_name' => get_option('blogname'),
			 'auto_respond_from_email' => get_option('admin_email'),
			 'auto_respond_reply_to' => get_option('admin_email'),
			 'auto_respond_subject' => '',
			 'auto_respond_message' => '',
			 'req_field_indicator_enable' => 'true',
			 'req_field_label_enable' => 'true',
			 'req_field_indicator' => ' *',
			 'border_enable' => 'false',
             'external_style' => 'false',
			 'aria_required' => 'false',
			 'auto_fill_enable' => 'true',
             'form_attributes' => '',
             'submit_attributes' => '',
             'success_page_html' => '',
			 'title_border' => __( 'Contact Form', 'si-contact-form' ),
			 'title_dept' => '',
			 'title_select' => '',
			 'title_name' => '',
			 'title_fname' => '',
			 'title_mname' => '',
			 'title_miname' => '',
			 'title_lname' => '',
			 'title_email' => '',
			 'title_email2' => '',
			 'title_subj' => '',
			 'title_mess' => '',
			 'title_capt' => '',
			 'title_submit' => '',
             'title_submitting' => '',
			 'title_reset' => '',
			 'title_areyousure' => '',
			 'text_message_sent' => '',
			 'text_print_button' => '',
			 'tooltip_required' => '',
			 'tooltip_captcha' => '',
			 'tooltip_refresh' => '',
			 'tooltip_filetypes' => '',
			 'tooltip_filesize' => '',
			 'enable_reset' => 'false',
			 'enable_credit_link' => 'false',
			 'error_contact_select' => '',
			 'error_name'           => '',
			 'error_email'          => '',
			 'error_email_check'    => '',
			 'error_email2'         => '',
			 'error_url'            => '',
             'error_date'           => '',
             'error_time'           => '',
             'error_maxlen'         => '',
			 'error_field'          => '',
			 'error_subject'        => '',
			 'error_select'         => '',
			 'error_input'          => '',
			 'error_captcha_blank'  => '',
			 'error_captcha_wrong'  => '',
			 'error_correct'        => '',
             'error_spambot'        => '',
			 'fields'				=> array(),
		     'vcita_scheduling_button' => 'false',
		     'vcita_scheduling_button_label' => '',
		     'vcita_approved'       => 'false',
		     'vcita_uid'            => '',
		     'vcita_email'          => '',
		     'vcita_email_new'      => ((get_option('admin_email') == 'user@example.com') ? '' : get_option('admin_email')),
		     'vcita_confirm_token'	=> '',
		     'vcita_confirm_tokens'	=> '',
	    	 'vcita_initialized'	=> 'false',
		     'vcita_first_name'	    => '',
		     'vcita_last_name'	    => '',
		     'vcita_scheduling_button_label' => 'Schedule an Appointment',
		);
		
		// Merge in the style settings
		// Do it this way so we also have the style settings in a separate array to make validation easier
		self::$form_defaults = array_merge(self::$form_defaults, $style_defaults);
		
		self::get_field_defaults();

		// Add the standard fields (Name, Email, Subject, Message)
		// The main plugin file defines constants to refer to the standard field codes
		$name = array(
			'standard'		 => '1',		// standard field number, otherwise '0' (internal) NEW
			'req'			 => 'true',
			'label'			 => __('Name:', 'si-contact-form'),
			'slug'			 =>	'full_name',
			'type'			 => 'text'
		);
		 $email = array(
			'standard'		 => '2',		// standard field number, otherwise '0' (internal) NEW
			'req'			 => 'true',
			'label'			 => __('Email:', 'si-contact-form'),
			'slug'			 =>	'email',
			'type'			 => 'text'
		);

		$subject = array(
			'standard'		 => '3',		// standard field number, otherwise '0' (internal) NEW
			'req'			 => 'true',
			'label'			 => __('Subject:', 'si-contact-form'),
			'slug'			 =>	'subject',
			'type'			 => 'text'
		);
		$message = array(
			'standard'		 => '4',		// standard field number, otherwise '0' (internal) NEW
			'req'			 => 'true',
			'label'			 => __('Message:', 'si-contact-form'),
			'slug'			 =>	'message',
			'type'			 => 'textarea'
		);

		// Add the standard fields to the form fields array
		self::$form_defaults['fields'][] = array_merge(self::$field_defaults, $name);
		self::$form_defaults['fields'][] = array_merge(self::$field_defaults, $email);
		self::$form_defaults['fields'][] = array_merge(self::$field_defaults, $subject);
		self::$form_defaults['fields'][] = array_merge(self::$field_defaults, $message);

		return(self::$form_defaults);
	}	// end function set_form_defaults()

		static function set_style_defaults() {
		// Set up default style values
		// Called by set_defaults() and FSCF_Options::validate()

		$style_defaults = array(

            // labels on top (default)

            // Alignment DIVs
		    'form_style'           => 'width:99%; max-width:555px;',   // Form DIV, how wide is the form DIV
		    'left_box_style'       => 'float:left; width:55%; max-width:270px;',   // left box DIV, container for vcita
            'right_box_style'      => 'float:left; width:235px;',   // right box DIV, container for vcita
		    'clear_style'          => 'clear:both;',   // clear both
		    'field_left_style'     => 'clear:left; float:left; width:99%; max-width:550px; margin-right:10px;',   // field left (wider)
 		    'field_prefollow_style' => 'clear:left; float:left; width:99%; max-width:250px; margin-right:10px;',   // field pre follow (narrower)
		    'field_follow_style'   => 'float:left; padding-left:10px; width:99%; max-width:250px;',   // field follow
			'title_style'          => 'text-align:left; padding-top:5px;', // Input labels alignment DIV
			'field_div_style'      => 'text-align:left;',   // Input fields alignment DIV
			'captcha_div_style_sm' => 'width:175px; height:50px; padding-top:2px;',  // Small CAPTCHA DIV
			'captcha_div_style_m'  => 'width:250px; height:65px; padding-top:2px;',  // Large CAPTCHA DIV
			'captcha_image_style' => 'border-style:none; margin:0; padding:0px; padding-right:5px; float:left;', // CAPTCHA image alignment
			'captcha_reload_image_style' => 'border-style:none; margin:0; padding:0px; vertical-align:bottom;', // CAPTCHA reload image alignment
			'submit_div_style'     => 'text-align:left; clear:both; padding-top:15px;', // Submit DIV
            'border_style'         => 'border:1px solid black; width:99%; max-width:550px; padding:10px;', // style of the form fieldset box (if enabled)

            // Styles of labels, fields and text
            'required_style'       => 'text-align:left;',   // required field indicator
            'required_text_style'  => 'text-align:left;',   // required field text
			'hint_style'           => 'font-size:x-small; font-weight:normal;',  // small text hints like please enter your email again
            'error_style'          => 'text-align:left; color:red;', // Input validation messages
            'redirect_style'       => 'text-align:left;', // Redirecting message
            'fieldset_style'       => 'border:1px solid black; width:97%; max-width:500px; padding:10px;', // style of the fieldset box (for field)
            'label_style'          => 'text-align:left;', // Field labels
  			'option_label_style'   => 'display:inline;', // Options labels

 			'field_style'          => 'text-align:left; margin:0; width:99%; max-width:250px;', // Input text fields  (out of place here?)
  			'captcha_input_style'  => 'text-align:left; margin:0; width:50px;', // CAPTCHA input field
 			'textarea_style'       => 'text-align:left; margin:0; width:99%; max-width:250px; height:120px;',  // Input Textarea
            'select_style'         => 'text-align:left;',  // Input Select
 			'checkbox_style'       => 'width:22px; height:32px;',  // Input checkbox
            'radio_style'          => 'width:22px; height:32px;',  // Input radio
            'placeholder_style'    => 'opacity:0.6; color:#333333;', // placeholder style

			'button_style'         => 'cursor:pointer; margin:0;', // Submit button
			'reset_style'          => 'cursor:pointer; margin:0;', // Reset button
			'vcita_button_style'   => 'text-decoration:none; display:block; text-align:center; background:linear-gradient(to bottom, #ed6a31 0%, #e55627 100%); color:#fff !important; padding:8px;',
            'vcita_div_button_style' => 'border-left:1px dashed #ccc; margin-top:25px; padding:8px 20px;', // vCita button div box
 			'powered_by_style'     => 'font-size:x-small; font-weight:normal; padding-top:5px; text-align:center;', // the "powered by" link


		);


		return($style_defaults);
	}	// end function set_style_defaults()

	static function get_field_defaults() {
		// Default array for a single field
		self::$field_defaults = array(
			'standard'		 => '0',		// standard field number, otherwise '0' (internal) NEW
			'options'		 => '',			// Options list for select, radio, and checkbox-multiple
			'default'		 => '',
			'inline'		 => 'false',	// Should checkboxes and radio buttons be displayed inline?
			'req'			 => 'false',	// required field?
			'disable'		 => 'false',
			'follow'		 => 'false',	// controls if this field will be displayed following the previous one on the same line
            'hide_label'	 => 'false',	// controls if this field will have a hidden label on the form
            'placeholder'	 => 'false',	// controls if the default text will be a placeholder
			'label'			 => __('New Field:', 'si-contact-form'),
			'slug'			 => '',			// slug used for query vars, subject and email tags
			'type'			 => 'text',
			'max_len'		 => '',
			'label_css'		 => '',
			'input_css'		 => '',
			'attributes'	 => '',
			'regex'			 => '',
			'regex_error'	 => '',
			'notes'			 => '',
			'notes_after'	 => '',
		);

		return (self::$field_defaults);
	}

	static function get_form_defaults() {
		// Returns the defaults for a form
		if ( empty(self::$form_defaults) )
			self::set_defaults ( );
		return(self::$form_defaults);
	}

	static function update_lang(&$form_options) {
		//  global FSCF_Options::$form_options, FSCF_Options::$form_optionsion_defaults;
		// Update a few language options in the form options array
		// $form_options is a form options array, passed by reference so it can be changed here.
		// Had to do this becuse the options were actually needed to be set before the language translator was initialized
		// Update translation for these options (for when switched from English to another lang)
		if ( $form_options['welcome'] == '<p>Comments or questions are welcome.</p>' ) {
			$form_options['welcome'] = __( '<p>Comments or questions are welcome.</p>', 'si-contact-form' );
		}

		if ( $form_options['email_to'] == 'Webmaster,' . get_option( 'admin_email' ) ) {
			$form_options['email_to'] = __( 'Webmaster', 'si-contact-form' ) . ',' . get_option( 'admin_email' );
		}

		if ( $form_options['email_subject'] == get_option( 'blogname' ) . ' ' . 'Contact:' ) {
			$form_options['email_subject'] = get_option( 'blogname' ) . ' ' . __( 'Contact:', 'si-contact-form' );
		}
	}	// end function si_contact_update_lang

	// checks proper email syntax (not perfect, none of these are, but this is the best I can find)
	static function validate_email($email) {
	   //check for all the non-printable codes in the standard ASCII set,
	   //including null bytes and newlines, and return false immediately if any are found.
	   if (preg_match("/[\\000-\\037]/",$email)) {
		  return false;
	   }
       // There's no perfect regular expression to validate email addresses!
	   // http://fightingforalostcause.net/misc/2006/compare-email-regex.php
	   $pattern = "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,12}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD";
       // 09/17/2014 above is updated for new generic top-level domains (gTLDs) released in 2014 and beyond up to 12 characters like .training
       // (note: does not do IPv6, does not support Internationalized Domain Names, sorry)

       if (!empty(FSCF_Process::$form_options['email_check_easy']) && FSCF_Process::$form_options['email_check_easy'] == 'true') {
         $pattern = "/^\S+@\S+$/"; // check for @ sign with non whitespace on either side
       }

	   if(!preg_match($pattern, $email)){
	   	  return false;
	   }

	   // Make sure the domain exists with a DNS check (if enabled in options)
	   // MX records are not mandatory for email delivery, this is why this function also checks A and CNAME records.
	   // if the checkdnsrr function does not exist (skip this extra check, the syntax check will have to do)
	   // checkdnsrr available in Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
	   if (!empty(FSCF_Process::$form_options['email_check_dns']) && FSCF_Process::$form_options['email_check_dns'] == 'true') {
		  if( function_exists('checkdnsrr') ) {
			 list($user,$domain) = explode('@',$email);
			 if(!checkdnsrr($domain.'.', 'MX') &&
				!checkdnsrr($domain.'.', 'A') &&
				!checkdnsrr($domain.'.', 'CNAME')) {
				// domain not found in DNS
				return false;
			 }
		  }
	   }
	   return true;
	} // end function validate_email

	static function get_captcha_url_cf() {
	  // The captcha URL cannot be on a different domain as the site rewrites to or the cookie won't work
	  // also the path has to be correct or the image won't load.
	  // WP_PLUGIN_URL was not getting the job done! this code should fix it.

	  //http://media.example.com/wordpress   WordPress address get_option( 'siteurl' )
	  //http://tada.example.com              Blog address      get_option( 'home' )

	  //http://example.com/wordpress  WordPress address get_option( 'siteurl' )
	  //http://example.com/           Blog address      get_option( 'home' )
      // even works on multisite, network activated
	  $site_uri = parse_url(get_option('home'));
	  $home_uri = parse_url(get_option('siteurl'));

	  $captcha_url_cf  = plugins_url( 'captcha' , FSCF_FILE );

	  if ($site_uri['host'] == $home_uri['host']) {
		  // use $captcha_url_cf above
	  } else {
		  $captcha_url_cf  = get_option( 'home' ) . '/'.PLUGINDIR.'/si-contact-form/captcha';
	  }
	  // set the type of request (SSL or not)
	  if ( is_ssl() ) {
			$captcha_url_cf = preg_replace('|http://|', 'https://', $captcha_url_cf);
	  }
	  return $captcha_url_cf;
	}
	
	
	static function trim_array(&$a) {
		// Trim string elements in an array, recursing nested arrays
		// Parameter: $a is an array, passed by reference so we can change its value
		foreach ($a as $key => $val) {
			if ( is_array($val) ) {
				self::trim_array($val);
				$a[$key] = $val;
			} else if ( is_string($val) ) {
				$a[$key] = trim($val);
			}
		}
	}

	static function unencode_html(&$a) {
		// Unencode html entities in an array, recursing nested arrays
        // unencode < > & " ' (less than, greater than, ampersand, double quote, single quote).
		// Parameter: $a is an array, passed by reference so we can change its value
		foreach( $a as $key => $val ) {
			if ( is_array( $val ) ) {
				self::unencode_html($val);
				$a[$key] = $val;
			} else if ( is_string( $val ) ) {
				$a[$key] = str_replace('&lt;','<',$val);
				$a[$key] = str_replace('&gt;','>',$val);
				$a[$key] = str_replace('&#39;',"'",$val);
				$a[$key] = str_replace('&quot;','"',$val);
				$a[$key] = str_replace('&amp;','&',$val);
			}
		}
	}

	// functions for protecting and validating form input vars
	static function clean_input($string, $preserve_space = 0) {
		// cleans an input string, or an array of strings
		if ( is_string($string) ) {
		   if ( $preserve_space )
			  return self::sanitize_string(strip_tags(stripslashes($string)),$preserve_space);
		   return trim(self::sanitize_string(strip_tags(stripslashes($string))));
		} elseif ( is_array($string) ) {
		  reset($string);
		  while (list($key, $value) = each($string)) {
			$string[$key] = self::clean_input($value,$preserve_space);
		  }
		  return $string;
		} else {
		  return $string;
		}
	} // end function clean_input

	// functions for protecting and validating form vars
	static function sanitize_string($string, $preserve_space = 0) {
		if(!$preserve_space)
		  $string = preg_replace("/ +/", ' ', trim($string));

		return preg_replace("/[<>]/", '_', $string);
	} // end function sanitize_string


     /**
     * disallow potentially unsafe shell characters.
     * @param string $string The string to be validated
     * @access protected
     * @return boolean
     */
   static function fsc_is_shell_safe($string) {
        // Future-proof
        if (escapeshellcmd($string) !== $string
            or !in_array(escapeshellarg($string), array("'$string'", "\"$string\""))
        ) {
            return false;
        }

        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $c = $string[$i];

            // All other characters have a special meaning in at least one common shell, including = and +.
            // Full stop (.) has a special meaning in cmd.exe, but its impact should be negligible here.
            // Note that this does permit non-Latin alphanumeric characters based on the current locale.
            if (!ctype_alnum($c) && strpos('@_-.', $c) === false) {
                return false;
            }
        }

        return true;
  }

	static function name_case($name) {
	// A function knowing about name case (i.e. caps on McDonald etc)
	// Usage: $name = name_case($name);	
	// Consider moving this function to FSCF_Process

		if ( FSCF_Process::$form_options['name_case_enable'] !== 'true' ) {
			return $name; // name_case setting is disabled for si contact
		}
		if ( $name == '' )
			return '';
		$break = 0;
		$newname = strtoupper( $name[0] );
		for ( $i = 1; $i < strlen( $name ); $i++ ) {
			$subed = substr( $name, $i, 1 );
			if ( ((ord( $subed ) > 64) && (ord( $subed ) < 123)) ||
					((ord( $subed ) > 48) && (ord( $subed ) < 58)) ) {
				$word_check = substr( $name, $i - 2, 2 );
				if ( !strcasecmp( $word_check, 'Mc' ) || !strcasecmp( $word_check, "O'" ) ) {
					$newname .= strtoupper( $subed );
				} else if ( $break ) {
					$newname .= strtoupper( $subed );
				} else {
					$newname .= strtolower( $subed );
				}
				$break = 0;
			} else {
				// not a letter - a boundary
				$newname .= $subed;
				$break = 1;
			}
		}
		return $newname;
	} // end function name_case

	// checks proper url syntax (not perfect, none of these are, but this is the best I can find)
	//   tutorialchip.com/php/preg_match-examples-7-useful-code-snippets/
	static function validate_url($url) {

		$regex = "((https?|ftp)\:\/\/)?"; // Scheme
		$regex .= "([a-zA-Z0-9+!*(),;?&=\$_.-]+(\:[a-zA-Z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		$regex .= "([a-zA-Z0-9-.]*)\.([a-zA-Z]{2,12})"; // Host or IP
		$regex .= "(\:[0-9]{2,5})?"; // Port
		$regex .= "(\/#\!)?"; // Path hash bang  (twitter) (mike challis added)
		$regex .= "(\/([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor

		return preg_match("/^$regex$/", $url);

	}  // end function validate_url

}  // end class FSCF_Util

// end of file  
