<?php
/**
 * Admin Class
 *
 * Handles the admin functionality of plugin
 *
 * @package WP Responsive Recent Post Slider
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Wprps_Admin {

	function __construct() {

		// Admin init process
		add_action( 'admin_init', array($this, 'wprps_admin_init_process') );

		// Action to add admin menu
		add_action( 'admin_menu', array($this, 'wprps_register_menu') );
	}

	/**
	 * Function to add menu
	 * 
	 * @since 1.0.0
	 */
	function wprps_register_menu() {

		// Getting Started page
		add_menu_page( __( 'Recent Post Slider', 'wp-responsive-recent-post-slider' ), __( 'Recent Post Slider', 'wp-responsive-recent-post-slider' ), 'manage_options', 'wprps-about', array( $this, 'wprps_getting_started_page' ), 'dashicons-sticky' );

		// Setting page
		add_submenu_page( 'wprps-about', __( 'Solutions & Features - Responsive Recent Post Slider', 'wp-responsive-recent-post-slider' ), '<span style="color:#2ECC71">'. __( 'Solutions & Features', 'wp-responsive-recent-post-slider' ).'</span>', 'manage_options', 'wprps-solutions-features', array( $this, 'wprps_solutions_features_page' ) );

		// Premium page load
		add_submenu_page( 'wprps-about', __( 'Upgrade To PRO - Responsive Recent Post Slider', 'wp-responsive-recent-post-slider' ), '<span style="color:#ff2700">' . __( 'Upgrade To Premium ', 'wp-responsive-recent-post-slider' ) . '</span>', 'manage_options', 'wprps-premium', array( $this, 'wprps_premium_page' ) );
	}

	/**
	 * How it Work Page Html
	 * 
	 * @since 1.0
	 */
	function wprps_getting_started_page() {
		include_once( WPRPS_DIR . '/includes/admin/wprps-how-it-work.php' );
	}

	/**
	 * Solutions Features Page Html
	 * 
	 * @since 1.0
	 */
	function wprps_solutions_features_page() {
		include_once( WPRPS_DIR . '/includes/admin/settings/solutions-features.php' );	
	}

	/**
	 * Premium Page Html
	 * 
	 * @since 1.0.0
	 */
	function wprps_premium_page() {
		include_once( WPRPS_DIR . '/includes/admin/settings/premium.php' );
	}

	/**
	 * Function to notification transient
	 * 
	 * @since 1.4.3
	 */
	function wprps_admin_init_process() {

		// If plugin notice is dismissed
		if( isset($_GET['message']) && 'wprps-plugin-notice' == $_GET['message'] ) {
			set_transient( 'wprps_install_notice', true, 604800 );
		}
	}
}

$wprps_admin = new Wprps_Admin();