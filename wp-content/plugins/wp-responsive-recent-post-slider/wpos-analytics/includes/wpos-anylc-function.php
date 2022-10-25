<?php
/**
 * Common Functions
 *
 * @package Wpos Analytic
 * @since 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Retrieve the translation of $text.
 *
 * @package Wpos Analytic
 * @since 1.0
 */
function wpos_anylc_text( $text, $echo = false ) {
	
	if( $echo ) {
		_e( $text, '' );
	} else {
		__( $text, '' );
	}
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * 
 * @since 1.0
 */
function wpos_anylc_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wpos_anylc_clean', $var );
	} else {
		$data = is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		return wp_unslash($data);
	}
}

/**
 * Check Multidimention Array
 *
 * @package Wpos Analytic
 * @since 1.0
 */
function wpos_anylc_is_multi_arr( $arr ) {
    rsort( $arr );
    return isset( $arr[0] ) && is_array( $arr[0] );
}

/**
 * Get site unique id
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_site_uid() {

	$site_uid = get_option( 'wpos_anylc_site_uid' );

	// Generate new site id if not exist
	if( empty( $site_uid ) ) {
		$site_url = untrailingslashit( get_bloginfo('wpurl') );
		$site_uid = md5( $site_url . SECURE_AUTH_KEY );

		update_option( 'wpos_anylc_site_uid', $site_uid );		
	}

	return $site_uid;
}

/**
 * Get Optin Data
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_optin_data( $anylc_pdt = false, $return_url = '' ) {

	// Skip if not admin area
	if ( !is_admin() ) {
		return false;
	}

	global $current_user, $wpos_analytics_product;

	// Takind some data
	$theme_data 	= wp_get_theme();
	$page 			= isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : false;

	// If product is not passed
	if( ! $anylc_pdt ) {
		$anylc_pdt 		= !empty( $_GET['wpos_anylc_pdt'] ) 			? sanitize_text_field( $_GET['wpos_anylc_pdt'] ) 	: '';
		$anylc_pdt 		= ( ! $anylc_pdt && !empty( $_GET['page'] ) ) 	? sanitize_text_field( $_GET['page'] ) 				: $anylc_pdt;
	}

	// If a valid product is there
	if( $anylc_pdt && !empty( $wpos_analytics_product[ $anylc_pdt ] ) ) {

		$analy_product 	= $wpos_analytics_product[ $anylc_pdt ];

		if( empty( $return_url ) ) {
			$return_url 	= add_query_arg( array( 'page' => $page ), admin_url('admin.php') );
			$return_url		= wp_nonce_url( $return_url, 'wpos_anylc_act' );
		}

		// Getting data according to type
		if( $analy_product['type'] == 'theme' ) {

			$product_name		= $theme_data->get( 'Name' );
			$product_version	= $theme_data->get( 'Version' );

		} else {

			if( !function_exists('get_plugin_data') ) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$plugin_data 		= get_plugin_data( trailingslashit(WP_PLUGIN_DIR) . $analy_product['file'] );
			$product_name		= !empty( $plugin_data['Name'] ) ? $plugin_data['Name'] : '';
			$product_version	= !empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
		}
	}

	$optin_data = array(
						'site_url' 			=> untrailingslashit( get_bloginfo('wpurl') ),
						'site_name'			=> get_bloginfo( 'name' ),
						'wp_version'		=> get_bloginfo( 'version' ),
						'language'			=> get_bloginfo( 'language' ),
						'is_rtl'			=> is_rtl() ? 1 : 0,
						'php_version'		=> phpversion(),
						'sdk_version'		=> WPOS_ANYLC_VERSION,
						'product_name'		=> isset( $product_name ) ? $product_name : '',
						'product_version'	=> isset( $product_version ) ? $product_version : '',
						'product_id'		=> !empty( $analy_product['id'] ) ? $analy_product['id'] : 0,
						'product_type'		=> !empty( $analy_product['type'] ) ? $analy_product['type'] : '',
						'theme_name'		=> $theme_data->get( 'Name' ),
						'theme_uri'			=> $theme_data->get( 'ThemeURI' ),
						'theme_author'		=> $theme_data->get( 'Author' ),
						'theme_author_uri'	=> $theme_data->get( 'AuthorURI' ),
						'theme_version'		=> $theme_data->get( 'Version' ),
						'user_firstname'    => $current_user->user_firstname,
						'user_lastname'     => $current_user->user_lastname,
						'user_nickname'     => $current_user->user_nicename,
						'user_email'		=> get_bloginfo( 'admin_email' ),
						'ip_address'		=> wpos_anylc_get_ip_address(),
						'site_uid'			=> wpos_anylc_site_uid(),
						'return_url'		=> $return_url,
					);
	return $optin_data;
}

/**
 * Get IP Address
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_get_ip_address() {
	if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
		return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );  // WPCS: input var ok, CSRF ok.
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // WPCS: input var ok, CSRF ok.
		// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
		// Make sure we always only send through the first IP in the list which should always be the client IP.
		return (string) rest_is_ip_address( trim( current( preg_split( '/[,:]/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // WPCS: input var ok, CSRF ok.
	} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // @codingStandardsIgnoreLine
		return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // @codingStandardsIgnoreLine
	}
	return '127.0.0.1';
}

/**
 * Get Product Optin Data
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_get_option( $key = '' ) {

	$opt_in_data = array();

	if( !empty( $key ) ) {
		$opt_in_data = get_option( $key );
		$opt_in_data = ( !empty($opt_in_data) && is_array($opt_in_data) ) ? $opt_in_data : array();
	}
	return $opt_in_data;
}

/**
 * Get Product Optin Data
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_update_option( $key = '', $data = array() ) {

	$opt_in_data = array();

	if( !empty( $key ) ) {
		$opt_in_data = wpos_anylc_get_option( $key );

		if( is_array($data) ) {
			$opt_in_data = array_merge( $opt_in_data, $data );
			update_option( $key, $opt_in_data );
		}
	}
	return $opt_in_data;
}

/**
 * Get Analytic Product Optin URL
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_optin_url( $module_data = '', $optin_status = null ) {

	$optin_url = false;

	// Optin Status
	if( ! isset( $optin_status ) ) {
		$opt_in_data 	= get_option( $module_data['anylc_optin'] );
		$optin_status 	= isset( $opt_in_data['status'] ) ? $opt_in_data['status'] : null;
	}

	if( !empty( $module_data['menu'] ) && !empty( $module_data['slug'] ) ) {
		$url_data 	= parse_url( $module_data['menu'], PHP_URL_QUERY );
		$query_data	= !empty( $url_data ) ? parse_str( $url_data, $query_arr ) : array();

		if( !empty( $query_arr['post_type'] ) && $optin_status >= 0 ) { // If Optin is done and post type menu
			
			$optin_url = add_query_arg( array( 'post_type' => $query_arr['post_type'], 'page' => $module_data['slug'], 'anylc_optin_menu' => true ), admin_url('edit.php') );

		} else if( empty( $query_arr['post_type'] ) && $optin_status >= 0 ) { // If Optin is done and simple admin menu

			$optin_url = add_query_arg( array( 'page' => $module_data['slug'], 'anylc_optin_menu' => true ), admin_url('admin.php') );

		} else {
			$optin_url = add_query_arg( array( 'page' => $module_data['slug'] ), admin_url('admin.php') );
		}
	}

	return $optin_url;
}

/**
 * Get Analytic Product Opt Out URL
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_optout_url( $module_data = '', $optin_status = null, $redirect_url = false ) {

	$opt_out_link = false;

	// Optin Status
	if( !isset( $optin_status ) ) {
		$opt_in_data 	= get_option( $module_data['anylc_optin'] );
		$optin_status 	= isset( $opt_in_data['status'] ) ? $opt_in_data['status'] : null;
	}

	if( $optin_status == 1 ) {

		if( ! $redirect_url ) {
			$plugin_status 	= isset( $_GET['plugin_status'] ) 	? sanitize_text_field( $_GET['plugin_status'] ) 	: false;
			$paged 			= isset( $_GET['paged'] ) 			? sanitize_text_field( $_GET['paged'] ) 			: false;
			$s 				= isset( $_GET['s'] ) 				? sanitize_text_field( $_GET['s'] ) 				: false;

			$redirect_url 	= add_query_arg( array( 'plugin_status' => $plugin_status, 'paged' => $paged, 's' => $s ), admin_url( 'plugins.php' ) );
		}

		$opt_out_link 	= add_query_arg( array( 'wpos_anylc_action' => 'optout', 'wpos_anylc_pdt' => $module_data['slug'], 'redirect' => $redirect_url ), $redirect_url );
		$opt_out_link	= wp_nonce_url( $opt_out_link, 'wpos_anylc_act'.'|'.$module_data['slug'] );
	}

	return $opt_out_link;
}

/**
 * Get Analytic Product URL
 * 
 * @package Wpos Analytic
 * @since 1.0.0
 */
function wpos_anylc_pdt_url( $module_data = '', $type = false ) {

	$redirect_url 	= false;
	$redirect_page	= ! empty( $module_data['redirect_page'] ) ? $module_data['redirect_page'] : $module_data['menu'];

	if( ! empty( $redirect_page ) ) {

		$pos 			= strpos( $redirect_page, '?post_type' );
		$redirect_url 	= ( $pos !== false ) ? admin_url( $redirect_page ) : add_query_arg( array( 'page' => $redirect_page ), admin_url('admin.php') );

		switch ( $type ) {
			case 'promotion':

				$promotion = !empty( $_GET['promotion'] ) ? wpos_anylc_clean( $_GET['promotion'] ) : '';

				if( !empty( $promotion ) ) {
					$promotion 		= is_array( $promotion ) ? implode( ',', $promotion ) : $promotion;
					$redirect_url 	= add_query_arg( array( 'message' => 'wpos_anylc_promotion', 'wpos_anylc_pdt' => $module_data['slug'], 'wpos_anylc_promo_pdt' => $promotion ), $redirect_url );
				}
				break;

			case 'offer':

				if( !empty( $module_data['offers'] ) ) {
					$redirect_url = add_query_arg( array( 'page' => $module_data['slug'].'-offers' ), $redirect_url );
				}
				break;

			case 'offer-promotion':

				$promotion = !empty( $_GET['promotion'] ) ? wpos_anylc_clean( $_GET['promotion'] ) : '';

				if( !empty( $module_data['offers'] ) ) {
					$redirect_url = add_query_arg( array( 'page' => $module_data['slug'].'-offers' ), $redirect_url );
				}
				if( !empty( $promotion ) ) {
					$promotion 		= is_array( $promotion ) ? implode( ',', $promotion ) : $promotion;
					$redirect_url 	= add_query_arg( array( 'message' => 'wpos_anylc_promotion', 'wpos_anylc_pdt' => $module_data['slug'], 'wpos_anylc_promo_pdt' => $promotion ), $redirect_url );
				}
				break;
		}
	}
	return $redirect_url;
}