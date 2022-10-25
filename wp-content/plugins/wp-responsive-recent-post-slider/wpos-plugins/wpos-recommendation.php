<?php
/**
 * WPOS Recommended Plugins
 *
 * @author WP Online Support
 * @package Essential Plugins Bundle
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPOS_ESPBW' ) ) :

/**
 * Main Recommended Plugins Class By WP Online Support.
 *
 * @since 1.0
 */
final class WPOS_ESPBW {

	/**
	 * @var Instance
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Main Instance.
	 *
	 * Insures that only one instance of Analytics exists in memory at any one time.
	 * Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @uses WPOS_ESPBW::setup_constants() Setup the constants needed.
	 * @uses WPOS_ESPBW::includes() Include the required files.
	 * @uses WPOS_ESPBW::wpos_espbw_plugins_loaded() load the language files.
	 * @see WPOS_ESPBW()
	 * @return object the one true instance
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0' );
	}

	/**
	 * Plugin Constructor.
	 */
	public function __construct() {
		$this->setup_constants();
		$this->includes();
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
		$this->define( 'WPOS_ESPBW_VERSION', '1.0' );
		$this->define( 'WPOS_ESPBW_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'WPOS_ESPBW_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 */
	private function includes() {

		// Functions file
		require_once WPOS_ESPBW_DIR .'/includes/espbw-functions.php';

		// Script Class
		require_once WPOS_ESPBW_DIR .'/includes/class-espbw-script.php';

		// Admin Class
		require_once WPOS_ESPBW_DIR .'/includes/admin/class-espbw-admin.php';
	}
}

/**
 * The main function responsible for returning the one true
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpos_espbw = WPOS_ESPBW(); ?>
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function WPOS_ESPBW_RECOMMEND() {
	return WPOS_ESPBW::instance();
}

/**
 *
 * Initialize Analytics Module
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function wpos_espbw_init_module( $args = array() ) {

	global $wpos_espbw_module;

	$defaul_args = array(
						'prefix'	=> '',
						'menu'		=> false,
						'position'	=> 4,
					);

	$args = wp_parse_args( $args, $defaul_args );

	// If required data is not there then simply return
	if( empty( $args['menu'] ) ) {
		return false;
	}

	// Taking some variables
	$wpos_espbw_module		= ! empty( $wpos_espbw_module ) ? $wpos_espbw_module : array();
	$wpos_espbw_module[]	= $args;

	return $wpos_espbw_module;
}

/**
 *
 * Initialize Analytics Class Once all stuff has been loaded
 *
 * @since 1.0
 * @return object The one true Analytics Instance.
 */
function wpos_espbw_plugins_loaded() {

	// Get Analytics Running.
	WPOS_ESPBW_RECOMMEND();
}
add_action( 'plugins_loaded', 'wpos_espbw_plugins_loaded', 12 );

endif; // End if class_exists check