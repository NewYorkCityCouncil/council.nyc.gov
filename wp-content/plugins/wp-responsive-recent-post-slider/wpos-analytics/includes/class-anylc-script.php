<?php
/**
 * Script Class
 *
 * Handles the script and style 
 *
 * @package Wpos Analytic
 * @since 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Wpos_Anylc_Script {

	function __construct() {

        // Action to add style backend
		add_action( 'admin_enqueue_scripts', array($this, 'wpos_anylc_admin_script_style') );
	}

     /**
	 * Enqueue script for backend
	 * 
	 * @package Wpos Analytic
	 * @since 1.0
	 */
    function wpos_anylc_admin_script_style( $hook ) {

		// Process Promotion Data
		if( !empty($_GET['message']) && $_GET['message'] == 'wpos_anylc_promotion' && !empty($_GET['wpos_anylc_pdt']) && !empty($_GET['wpos_anylc_promo_pdt']) ) {
			global $wpos_analytics_product;

			$promotion 				= 1;
			$wpos_anylc_promo_pdt	= sanitize_text_field( $_GET['wpos_anylc_promo_pdt'] );
			$promotion_pdt 			= explode( ',', $wpos_anylc_promo_pdt );

			$anylc_pdt 		= sanitize_text_field( $_GET['wpos_anylc_pdt'] );
			$anylc_pdt_data = isset( $wpos_analytics_product[ $anylc_pdt ] ) ? $wpos_analytics_product[ $anylc_pdt ] : false;

			if( !empty($promotion_pdt) ) {
				foreach ($promotion_pdt as $pdt_key => $pdt) {
					if( isset( $anylc_pdt_data['promotion'][$pdt]['file'] ) ) {
						$promotion_pdt_data[] = $anylc_pdt_data['promotion'][$pdt]['file'];
					}
				}
			}
		}

    	// Registring admin Style
		wp_register_style( 'wpos-anylc-admin-style', WPOS_ANYLC_URL.'assets/css/wpos-anylc-admin.css', null, WPOS_ANYLC_VERSION );
		wp_enqueue_style( 'wpos-anylc-admin-style' );

		// Registring admin script
		wp_register_script( 'wpos-anylc-admin-script', WPOS_ANYLC_URL.'assets/js/wpos-anylc-admin.js', array('jquery'), WPOS_ANYLC_VERSION, true );
		wp_localize_script( 'wpos-anylc-admin-script', 'WposAnylc', array(
																		'promotion' 	=> isset($promotion) ? 1 : 0,
																		'promotion_pdt' => isset( $promotion_pdt_data ) ? $promotion_pdt_data : 0,
																	));
		wp_enqueue_script( 'wpos-anylc-admin-script' );
    }
}

$wpos_anylc_script = new Wpos_Anylc_Script();