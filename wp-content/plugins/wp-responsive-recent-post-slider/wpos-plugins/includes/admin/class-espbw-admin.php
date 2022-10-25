<?php
/**
 * Admin Class
 * Handles the Admin side functionality of plugin
 *
 * @package Essential Plugins Bundle
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPOS_ESPBW_Admin {

	function __construct() {

		// Action to register admin menu
		add_action( 'admin_menu', array($this, 'espbw_register_menu'), 14 );
	}

	/**
	 * Function to register admin menus
	 * 
	 * @since 1.0
	 */
	function espbw_register_menu() {

		global $wpos_espbw_module;

		// Loop of menu
		if( ! empty( $wpos_espbw_module ) ) {
			foreach ($wpos_espbw_module as $module_key => $module_val) {

				// Dashboard Page
				add_submenu_page( $module_val['menu'], __('Essential Plugins Bundle', 'espbw'), '<span style="color:#2ECC71;">'.__('Install Popular Plugins From Us', 'espbw').'</span>', 'manage_options', "{$module_val['prefix']}-espbw-dashboard", array($this, 'espbw_dashboard_page'), $module_val['position'] );
			}
		}
	}

	/**
	 * Render Plugin Dashboard Page
	 * 
	 * @since 1.0
	 */
	function espbw_dashboard_page() {
		include_once( WPOS_ESPBW_DIR . '/includes/admin/views/dashboard.php' );
	}
}

$wpos_espbw_admin = new WPOS_ESPBW_Admin();