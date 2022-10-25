<?php
/**
 * WPOS Analytics
 *
 * @author WP Online Support
 * @package Wpos Analytic
 * @since 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPOS_Analytics' ) ) :

/**
 * Main Analytics Class By WP Online Support.
 *
 * @since 1.0
 */
final class WPOS_Analytics {

	/**
	 * @var Instance
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Main Analytics Instance.
	 *
	 * Insures that only one instance of Analytics exists in memory at any one time.
	 * Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @uses WPOS_ANYLC::setup_constants() Setup the constants needed.
	 * @uses WPOS_ANYLC::includes() Include the required files.
	 * @uses WPOS_ANYLC::wpos_anylc_plugins_loaded() load the language files.
	 * @see PWPC()
	 * @return object The one true Analytics
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pwpc' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pwpc' ), '1.0' );
	}

	/**
	 * Plugin Constructor.
	 */
	public function __construct() {
		$this->setup_constants();
		$this->includes();

		do_action( 'wpos_anylc_loaded' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Setup plugin constants. Basic plugin definitions
	 *
	 * @access private
	 * @since 1.0
	 */
	private function setup_constants() {

		$this->define( 'WPOS_ANYLC_VERSION', '1.1' );
		$this->define( 'WPOS_ANYLC_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'WPOS_ANYLC_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 */
	private function includes() {

		// Functions file
		require_once WPOS_ANYLC_DIR .'/includes/wpos-anylc-function.php';

		// Script Class
		require_once WPOS_ANYLC_DIR .'/includes/class-anylc-script.php';

		// Admin Class
		require_once WPOS_ANYLC_DIR .'/includes/class-anylc-admin.php';
	}
}

/**
 *
 * The main function responsible for returning the one true Analytics
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpos_anylc = WPOS_ANYLC(); ?>
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function WPOS_ANYLC() {
	return WPOS_Analytics::instance();
}

/**
 *
 * Initialize Analytics Module
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function wpos_anylc_init_module( $args = array() ) {

	global $wpos_analytics_module, $wpos_analytics_product;

	$defaul_args = array(
						'id'			=> null,
						'file'			=> null,
						'name'  		=> null,
						'slug'  		=> null,
						'type'			=> 'plugin',
						'menu'			=> false,
						'icon'			=> '',
						'text_domain'	=> 'wpos_analytics',
					);

	$args = wp_parse_args( $args, $defaul_args );

	// If required data is not there then simply return
	if( empty($args['id']) || empty( $args['file'] ) || empty( $args['slug'] ) ) {
		return false;
	}

	// Additional args
	$promotion 				= array();
	$args['dir'] 			= pathinfo($args['file'], PATHINFO_DIRNAME);
	$args['icon']			= empty( $icon ) ? trailingslashit( WP_PLUGIN_URL ).$args['dir'].'/wpos-analytics/assets/images/icon.png' : $args['icon'];
	$args['brand_icon']		= plugin_dir_url( __FILE__ ).'assets/images/wpos-logo.png';
	$args['anylc_optin']	= 'wpos_anylc_pdt_'.$args['id'];

	if( isset( $args['promotion'] ) ) {
		foreach ($args['promotion'] as $promotion_key => $promotion_data) {
			if( empty( $promotion_data['name'] ) || empty( $promotion_data['file'] ) ) {
				continue;
			}

			$promotion[$promotion_key] = $promotion_data;
		}
	}
	$args['promotion'] = $promotion;

	// Taking some variables
	$wpos_analytics_module 	= !empty( $wpos_analytics_module ) 	? $wpos_analytics_module 	: array();
	$wpos_analytics_product = !empty( $wpos_analytics_product ) ? $wpos_analytics_product 	: array();

	if( is_array( $wpos_analytics_module ) ) {
		$wpos_analytics_module[ $args['file'] ] = $args;
	}

	if( is_array( $wpos_analytics_product ) ) {
		$wpos_analytics_product[ $args['slug'] ] = $args;
	}

	return $wpos_analytics_module;
}

/**
 *
 * Function on any plugin deactivation
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function wpos_anylc_plugin_activation( $plugin, $network_activation ) {

	// return if activating from network, or bulk
	if ( is_network_admin() ) {
		return;
	}

	global $wpos_analytics_module;

	if( isset( $wpos_analytics_module[ $plugin ] ) ) {

		$opt_in_data 	= get_option( $wpos_analytics_module[ $plugin ]['anylc_optin'] );
		$optin_status 	= isset( $opt_in_data['status'] ) ? $opt_in_data['status'] : -1;

		if( $optin_status == -1 ) {
			
			$redirect_link = add_query_arg( array('page' => $wpos_analytics_module[ $plugin ]['slug']), admin_url('admin.php') );
			update_option( 'wpos_anylc_redirect', $redirect_link );

		} elseif( ! empty( $wpos_analytics_module[ $plugin ]['redirect_page'] ) ) {

			$redirect_page	= $wpos_analytics_module[ $plugin ]['redirect_page'];
			$pos 			= strpos( $redirect_page, '?post_type' );
			$redirect_link 	= ( $pos !== false ) ? admin_url( $redirect_page ) : add_query_arg( array( 'page' => $redirect_page ), admin_url('admin.php') );

			update_option( 'wpos_anylc_redirect', $redirect_link );	
		}
	}
}
add_action( 'activated_plugin', 'wpos_anylc_plugin_activation', 10, 2 );

/**
 *
 * Initialize Analytics Class Once all stuff has been loaded
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function wpos_anylc_plugins_loaded() {
	
	// Get Analytics Running.
	WPOS_ANYLC();
}
add_action( 'plugins_loaded', 'wpos_anylc_plugins_loaded', 12 );

endif; // End if class_exists check.