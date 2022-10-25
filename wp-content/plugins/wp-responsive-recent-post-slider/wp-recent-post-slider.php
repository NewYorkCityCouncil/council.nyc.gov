<?php
/**
 * Plugin Name: WP Responsive Recent Post Slider
 * Plugin URI: https://www.essentialplugin.com/wordpress-plugin/wp-responsive-recent-post-slider/
 * Text Domain: wp-responsive-recent-post-slider
 * Domain Path: /languages/
 * Description: Add and display Responsive WordPresss Recent Post Slider and Carousel on your website with 4 designs (Slider) and 1 designs (Carousel) using a shortcode. Also added Gutenberg block support. 
 * Author: WP OnlineSupport, Essential Plugin
 * Version: 3.1
 * Author URI: https://www.essentialplugin.com/wordpress-plugin/wp-responsive-recent-post-slider/
 *
 * @package WP Responsive Recent Post Slider
 * @author WP OnlineSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Basic plugin definitions
 * 
 * @since 1.0.0
 */

if( ! defined( 'WPRPS_VERSION' ) ) {
	define( 'WPRPS_VERSION', '3.1' ); // Version of plugin
}

if( ! defined( 'WPRPS_NAME' ) ) {
	define( 'WPRPS_NAME', 'Responsive Recent Post Slider/Carousel' ); // Version of plugin
}

if( ! defined( 'WPRPS_DIR' ) ) {
	define( 'WPRPS_DIR', dirname( __FILE__ ) ); // Plugin dir
}

if( ! defined( 'WPRPS_URL' ) ) {
	define( 'WPRPS_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}

if( ! defined( 'WPRPS_POST_TYPE' ) ) {
	define( 'WPRPS_POST_TYPE', 'post' ); // Plugin post type
}

if( ! defined( 'WPRPS_PLUGIN_LINK' ) ) {
	define( 'WPRPS_PLUGIN_LINK', 'https://www.essentialplugin.com/pricing/?utm_source=WP&utm_medium=Post-Slider&utm_campaign=Features-PRO' ); // Plugin link
}

if( ! defined( 'WPRPS_PLUGIN_BUNDLE_LINK' ) ) {
	define('WPRPS_PLUGIN_BUNDLE_LINK', 'https://www.essentialplugin.com/pricing/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Welcome-Screen'); // Plugin link
}

if( ! defined( 'WPRPS_PLUGIN_LINK_UNLOCK' ) ) {
	define('WPRPS_PLUGIN_LINK_UNLOCK', 'https://www.essentialplugin.com/wordpress-plugin/wp-responsive-recent-post-slider/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Features-PRO#wpos-epb'); // Plugin link
}

if( ! defined( 'WPRPS_PLUGIN_LINK_UPGRADE' ) ) {
	define('WPRPS_PLUGIN_LINK_UPGRADE', 'https://www.essentialplugin.com/wordpress-plugin/wp-responsive-recent-post-slider/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Upgrade-PRO#wpos-epb'); // Plugin Check link
}

if( ! defined( 'WPRPS_SITE_LINK' ) ) {
	define('WPRPS_SITE_LINK', 'https://www.essentialplugin.com'); // Plugin link
}

/**
 * Load Text Domain
 * This gets the plugin ready for translation
 * 
 * @since 2.3
 */
function wprps_load_textdomain() {

	global $wp_version;

	// Set filter for plugin's languages directory
	$wprps_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$wprps_lang_dir = apply_filters( 'wprps_languages_directory', $wprps_lang_dir );

	// Traditional WordPress plugin locale filter.
	$get_locale = get_locale();

	if ( $wp_version >= 4.7 ) {
		$get_locale = get_user_locale();
	}

	// Traditional WordPress plugin locale filter
	$locale = apply_filters( 'plugin_locale',  $get_locale, 'wp-responsive-recent-post-slider' );
	$mofile = sprintf( '%1$s-%2$s.mo', 'wp-responsive-recent-post-slider', $locale );

	// Setup paths to current locale file
	$mofile_global  = WP_LANG_DIR . '/plugins/' . basename( WPRPS_DIR ) . '/' . $mofile;

	if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/plugin-name folder
		load_textdomain( 'wp-responsive-recent-post-slider', $mofile_global );
	} else { // Load the default language files
		load_plugin_textdomain( 'wp-responsive-recent-post-slider', false, $wprps_lang_dir );
	}
}
add_action( 'plugins_loaded', 'wprps_load_textdomain' );

/**
 * Activation Hook
 * Register plugin activation hook.
 * 
 * @since 1.0.0
 */
function wprps_install() {
	if( is_plugin_active('wp-responsive-recent-post-slider-pro/wp-recent-post-slider.php') ) {
		add_action('update_option_active_plugins', 'wprps_deactivate_pro_version');
	}
}
register_activation_hook( __FILE__, 'wprps_install' );

/**
 * Deactivation Hook
 * Register plugin deactivation hook.
 * 
 * @since 1.0.0
 */
function wprps_uninstall() {
	
	// IMP need to flush rules for custom registered post type
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wprps_uninstall');

/**
 * Deactivate free plugin
 * 
 * @since 1.0.0
 */
function wprps_deactivate_pro_version() {
   deactivate_plugins('wp-responsive-recent-post-slider-pro/wp-recent-post-slider.php',true);
}

/**
 * Function to display admin notice of activated plugin.
 * 
 * @since 1.0.0
 */
function wprps_plugin_admin_notice() {
	global $pagenow;

	// If not plugin screen
	if( 'plugins.php' != $pagenow ) {
		return;
	}

	// Check Lite Version
	$dir = WP_PLUGIN_DIR . '/wp-responsive-recent-post-slider-pro/wp-recent-post-slider.php';

	if( ! file_exists( $dir ) ) {
		return;
	}

	$notice_link        = add_query_arg( array('message' => 'wprps-plugin-notice'), admin_url('plugins.php') );
	$notice_transient   = get_transient( 'wprps_install_notice' );

	// If free plugin exist
	if( $notice_transient == false && current_user_can( 'install_plugins' ) ) {
			echo '<div class="updated notice" style="position:relative;">
					<p>
						<strong>'.sprintf( esc_html__('Thank you for activating %s', 'wp-responsive-recent-post-slider'), 'WP Responsive Recent Post Slider').'</strong>.<br/>
						'.sprintf( esc_html__('It looks like you had PRO version %s of this plugin activated. To avoid conflicts the extra version has been deactivated and we recommend you delete it.', 'wp-responsive-recent-post-slider'), '<strong>(<em>WP Responsive Recent Post Slider PRO</em>)</strong>' ).'
					</p>
					<a href="'.esc_url( $notice_link ).'" class="notice-dismiss" style="text-decoration:none;"></a>
				</div>';
	}
}

// Action to display notice
add_action( 'admin_notices', 'wprps_plugin_admin_notice');

// Function file
require_once( WPRPS_DIR . '/includes/wppsac-function.php' );

// Script Fils
require_once( WPRPS_DIR . '/includes/class-wppsac-script.php' );

// Admin class
require_once( WPRPS_DIR . '/includes/admin/class-wprps-admin.php' );

// Shortcodes
require_once( WPRPS_DIR . '/includes/shortcodes/wppsac-slider.php' );
require_once( WPRPS_DIR . '/includes/shortcodes/wppsac-carousel.php' );

// Gutenberg Block Initializer
if ( function_exists( 'register_block_type' ) ) {
	require_once( WPRPS_DIR . '/includes/admin/supports/gutenberg-block.php' );
}

/* Recommended Plugins Starts */
if ( is_admin() ) {
	require_once( WPRPS_DIR . '/wpos-plugins/wpos-recommendation.php' );

	wpos_espbw_init_module( array(
							'prefix'		=> 'wprps',
							'menu'		=> 'wprps-about',
							'position'	=> 1,
						));
}
/* Recommended Plugins Ends */

/* Plugin Wpos Analytics Data Starts */
function wpos_analytics_anl18_load() {

	require_once dirname( __FILE__ ) . '/wpos-analytics/wpos-analytics.php';

	$wpos_analytics =  wpos_anylc_init_module( array(
							'id'			=> 18,
							'file'			=> plugin_basename( __FILE__ ),
							'name'			=> 'WP Responsive Recent Post Slider',
							'slug'			=> 'wprps-post-slider',
							'type'			=> 'plugin',
							'menu'			=> 'wprps-about',
							'redirect_page'	=> 'wprps-solutions-features',
							'text_domain'	=> 'wp-responsive-recent-post-slider',
						));

	return $wpos_analytics;
}

// Init Analytics
wpos_analytics_anl18_load();
/* Plugin Wpos Analytics Data Ends */