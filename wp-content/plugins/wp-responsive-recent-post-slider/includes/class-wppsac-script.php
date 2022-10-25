<?php
/**
 * Script Class
 *
 * Handles the script and style functionality of plugin
 *
 * @package WP Responsive Recent Post Slider
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Wppsac_Script {

	function __construct() {

		// Action to add style in backend
		add_action( 'admin_enqueue_scripts', array($this, 'wppsac_admin_style_script') );

		// Action to add style and script at front side
		add_action( 'wp_enqueue_scripts', array($this, 'wppsac_front_style_script') );
	}

	/**
	 * Enqueue admin styles
	 * 
	 * @since 2.5.2
	 */
	function wppsac_register_admin_assets() {

		/* Styles */
		// Registring admin css
		wp_register_style( 'wppsac-admin-style', WPRPS_URL.'assets/css/wppsac-admin-style.css', array(), WPRPS_VERSION );


		/* Scripts */
		// Registring admin script
		wp_register_script( 'wppsac-admin-script', WPRPS_URL.'assets/js/wppsac-admin.js', array('jquery'), WPRPS_VERSION );
	}

	/**
	 * Enqueue admin styles
	 * 
	 * @since 2.5
	 */
	function wppsac_admin_style_script( $hook ) {

		$this->wppsac_register_admin_assets();

		if( $hook == 'toplevel_page_wprps-about' ) {
			wp_enqueue_script( 'wppsac-admin-script' );
		}

		if( $hook == 'recent-post-slider_page_wprps-solutions-features' ) {
			wp_enqueue_style( 'wppsac-admin-style' );
		}
	}

	/**
	 * Function to add style and script at front side
	 * 
	 * @since 1.0.0
	 */
	function wppsac_front_style_script() {

		global $post;

		// Determine Elementor Preview Screen
		// Check elementor preview is there
		$elementor_preview = ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_GET['elementor-preview'] ) && $post->ID == (int) $_GET['elementor-preview'] ) ? 1 : 0;

		/* Styles */
		// Registring and enqueing slick slider css
		if( ! wp_style_is( 'wpos-slick-style', 'registered' ) ) {
			wp_register_style( 'wpos-slick-style', WPRPS_URL.'assets/css/slick.css', array(), WPRPS_VERSION );
		}

		// Registring and enqueing public css
		wp_register_style( 'wppsac-public-style', WPRPS_URL.'assets/css/recent-post-style.css', array(), WPRPS_VERSION );

		wp_enqueue_style( 'wpos-slick-style' );
		wp_enqueue_style( 'wppsac-public-style' );


		/* Scripts */
		// Registring slick slider script
		if( ! wp_script_is( 'wpos-slick-jquery', 'registered' ) ) {
			wp_register_script( 'wpos-slick-jquery', WPRPS_URL.'assets/js/slick.min.js', array('jquery'), WPRPS_VERSION, true );
		}

		// Registring and enqueing public script
		wp_register_script( 'wppsac-public-script', WPRPS_URL.'assets/js/wppsac-public.js', array('jquery'), WPRPS_VERSION, true );
		wp_localize_script( 'wppsac-public-script', 'Wppsac', array(
																	'elementor_preview'	=> $elementor_preview,
																	'is_mobile'			=> ( wp_is_mobile() )	? 1 : 0,
																	'is_rtl'			=> ( is_rtl() ) 		? 1 : 0,
																	'is_avada'			=> ( class_exists( 'FusionBuilder' ) ) ? 1 : 0,
																));

		// Register Elementor script
		wp_register_script( 'wppsac-elementor-script', WPRPS_URL.'assets/js/elementor/wppsac-elementor.js', array('jquery'), WPRPS_VERSION, true );

		// Enqueue Script for Elementor Preview
		if ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_GET['elementor-preview'] ) && $post->ID == (int) $_GET['elementor-preview'] ) {

			wp_enqueue_script( 'wpos-slick-jquery' );
			wp_enqueue_script( 'wppsac-public-script' );
			wp_enqueue_script( 'wppsac-elementor-script' );
		}

		// Enqueue Style & Script for Beaver Builder
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {

			$this->wppsac_register_admin_assets();

			wp_enqueue_script( 'wppsac-admin-script' );
			wp_enqueue_script( 'wpos-slick-jquery' );
			wp_enqueue_script( 'wppsac-public-script' );
		}

		// Enqueue Admin Style & Script for Divi Page Builder
		if( function_exists( 'et_core_is_fb_enabled' ) && isset( $_GET['et_fb'] ) && $_GET['et_fb'] == 1 ) {
			$this->wppsac_register_admin_assets();

			wp_enqueue_style( 'wppsac-admin-style');
		}

		// Enqueue Admin Style for Fusion Page Builder
		if( class_exists( 'FusionBuilder' ) && (( isset( $_GET['builder'] ) && $_GET['builder'] == 'true' ) ) ) {
			$this->wppsac_register_admin_assets();

			wp_enqueue_style( 'wppsac-admin-style');
		}
	}
}

$wppsac_script = new Wppsac_Script();