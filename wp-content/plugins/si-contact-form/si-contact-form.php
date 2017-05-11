<?php
/*
Plugin Name: Fast Secure Contact Form
Plugin URI: http://www.FastSecureContactForm.com/
Description: Fast Secure Contact Form for WordPress. An easy and powerful form builder that lets your visitors send you email. Blocks all automated spammers. No templates to mess with. <a href="plugins.php?page=si-contact-form/si-contact-form.php">Settings</a> | <a href="https://www.FastSecureContactForm.com/donate">Donate</a>
Author: Mike Challis, Ken Carlson
Author URI: http://www.642weather.com/weather/scripts.php
Text Domain: si-contact-form
Domain Path: /languages
License: GPLv2 or later
Version: 4.0.50
*/

/*
Fast Secure Contact Form, a plugin for WordPress
Copyright (C) 2008-2017 Mike Challis (http://www.fastsecurecontactform.com/contact)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//do not allow direct access
if ( strpos(strtolower($_SERVER['SCRIPT_NAME']),strtolower(basename(__FILE__))) ) {
 header('HTTP/1.0 403 Forbidden');
 exit('Forbidden');
}


/********************
 * Global constants
 ********************/
define( 'FSCF_VERSION', '4.0.50' );
define( 'FSCF_BUILD', '178');		// Used to force load of latest .js files
define( 'FSCF_FILE', __FILE__ );	               // /path/to/wp-content/plugins/si-contact-form/si-contact-form.php
define( 'FSCF_PATH', plugin_dir_path(__FILE__) );  // /path/to/wp-content/plugins/si-contact-form/
define( 'FSCF_URL', plugin_dir_url( __FILE__ ) );  // http://www.yoursite.com/wp-content/plugins/si-contact-form/
define( 'FSCF_ADMIN_URL', admin_url( 'plugins.php?page=si-contact-form/si-contact-form.php'));
define( 'FSCF_PLUGIN_NAME', 'Fast Secure Contact Form' );
define( 'FSCF_CAPTCHA_PATH', FSCF_PATH . 'captcha');
define( 'FSCF_ATTACH_DIR', FSCF_PATH . 'attachments/' );
define( 'FSCF_MAX_SLUG_LEN', 40 );

// Set constants for standard field numbers
define( 'FSCF_NAME_FIELD', '1' );
define( 'FSCF_EMAIL_FIELD', '2' );
define( 'FSCF_SUBJECT_FIELD', '3' );
define( 'FSCF_MESSAGE_FIELD', '4' );

global $fscf_special_slugs;		// List of reserve slug names
$fscf_special_slugs = array( 'f_name', 'm_name', 'mi_name', 'l_name', 'email2', 'mailto_id', 'subject_id' );

/********************
 * Includes
 ********************/
require_once FSCF_PATH . 'includes/class-fscf-util.php';
require_once FSCF_PATH . 'includes/class-fscf-display.php';
require_once FSCF_PATH . 'includes/class-fscf-process.php';

if ( is_admin() ) {
	require_once FSCF_PATH . 'includes/class-fscf-action.php';	
	require_once FSCF_PATH . 'includes/class-fscf-options.php';
	require_once FSCF_PATH . 'includes/class-fscf-parse-vcita-callback.php';
}


// Initialize plugin settings and hooks
FSCF_Util::setup();

register_activation_hook( __FILE__, 'FSCF_Util::import' );

if (!class_exists('siContactForm')) {
   class siContactForm {
      function si_contact_form_short_code($atts) {
         // backwards compatibility with manual PHP call from 3.xx
         echo FSCF_Display::process_short_code($atts);
      }
   }
}
$si_contact_form = new siContactForm();

// Show activation time errors
//echo get_option( 'plugin_error' );

// end of file
