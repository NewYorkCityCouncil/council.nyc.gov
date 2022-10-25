<?php
/**
 * Script Class
 * Handles the script and style functionality of plugin
 *
 * @package Essential Plugins Bundle
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPOS_ESPBW_Script {

	function __construct() {

		// Action to add style at admin side
		add_action( 'admin_enqueue_scripts', array($this, 'espbw_admin_script_style') );
	}

	/**
	 * Function to add script and style at admin side
	 * 
	 * @since 1.0
	 */
	function espbw_admin_script_style( $hook ) {

		// Taking pages array
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		// Registring admin css
		wp_register_style( 'espbw-admin-css', WPOS_ESPBW_URL.'assets/css/admin-style.css', array(), WPOS_ESPBW_VERSION );

		// Registring admin script
		wp_register_script( 'espbw-admin-script', WPOS_ESPBW_URL.'assets/js/admin-script.js', array('jquery'), WPOS_ESPBW_VERSION, true );

		// Olny for dashboard screen
		if( strpos( $page, 'espbw-dashboard' ) !== false ) {

			// enqueing admin css
			wp_enqueue_style( 'espbw-admin-css' );

			// enqueing admin script
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
			wp_localize_script( 'updates', '_wpUpdatesItemCounts', array(
																		'totals' => wp_get_update_data(),
																	));
			add_thickbox();

			wp_enqueue_script( 'espbw-admin-script' );
		}
	}
}

$wpos_espbw_script = new WPOS_ESPBW_Script();