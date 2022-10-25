<?php
/**
 * Plugin generic functions file
 *
 * @package WP Responsive Recent Post Slider
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to get plugin image sizes array
 * 
 * @since 1.2.2
 */
function wppsac_get_unique() {

	static $unique = 0;
	$unique++;

	// For Elementor & Beaver Builder
	if( ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_POST['action'] ) && $_POST['action'] == 'elementor_ajax' )
	|| ( class_exists('FLBuilderModel') && ! empty( $_POST['fl_builder_data']['action'] ) )
	|| ( function_exists('vc_is_inline') && vc_is_inline() ) ) {
		$unique = current_time('timestamp') . '-' . rand();
	}

	return $unique;
}

/**
 * Function to get post featured image
 * 
 * @since 1.2.5
 */
function wppsac_get_post_featured_image( $post_id = '', $size = 'full') {
	$size   = ! empty( $size ) ? $size : 'full';
	$image  = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

	if( ! empty( $image ) ) {
		$image = isset( $image[0] ) ? $image[0] : '';
	}
	return $image;
}

/**
 * Function to get Taxonomies list 
 * 
 * @since 1.3.3
 */
function wppsac_get_category_list( $post_id = 0, $taxonomy = '' ) {

	$output = '';
	$terms  = get_the_terms( $post_id, $taxonomy );

	if( $terms && ! is_wp_error( $terms ) && ! empty( $taxonomy ) ) {
		$output .= '<ul class="wppsac-post-categories-list">';
		
		foreach ( $terms as $term ) {
			 $output .= '<li><a href="'.get_term_link( $term ).'">'. wp_kses_post( $term->name ) .'</a></li>';
		}
		
		$output .= '</ul>';
	}

	return $output;
}

/**
 * Sanitize Multiple HTML class
 * 
 * @since 2.3
 */
function wppsac_sanitize_html_classes($classes, $sep = " ") {
	$return = "";

	if( ! is_array( $classes ) ) {
		$classes = explode($sep, $classes);
	}

	if( ! empty( $classes ) ) {
		foreach( $classes as $class ){
			$return .= sanitize_html_class( $class ) . " ";
		}
		$return = trim( $return );
	}

	return $return;
}

/**
 * Function to get shortcode designs
 * 
 * @since 1.2.5
 */
function wppsac_slider_designs() {
	$design_arr = array(
		'design-1'  => __( 'Design 1', 'wp-responsive-recent-post-slider' ),
		'design-2'  => __( 'Design 2', 'wp-responsive-recent-post-slider' ),
		'design-3'  => __( 'Design 3', 'wp-responsive-recent-post-slider' ),
		'design-4'  => __( 'Design 4', 'wp-responsive-recent-post-slider' ),
	);

	return apply_filters( 'wppsac_slider_designs', $design_arr );
}

/**
 * Function to get carousel shortcode designs
 * 
 * @since 2.2
 */
function wppsac_carousel_designs() {
	$design_arr = array(
					'design-1' => __( 'Design 1', 'wp-responsive-recent-post-slider' ),
				);
	return apply_filters( 'wppsac_carousel_designs', $design_arr );
}

/**
 * Function to get post excerpt
 * 
 * @since 4.0
 */
function wppsac_get_post_excerpt( $post_id = null, $content = '', $word_length = '55', $more = '...' ) {

	global $post;

	if( empty( $post_id ) ) {
		$post_id = isset( $post->ID ) ? $post->ID : $post_id;
	}

	$word_length = ! empty( $word_length ) ? $word_length : 55;

	// If post id is passed
	if( ! empty( $post_id ) ) {
		if( has_excerpt( $post_id ) ) {
			$content = get_the_excerpt( $post_id );
		} else {
			$content = ! empty( $content ) ? $content : get_the_content( NULL, FALSE, $post_id );
		}
	}

	if( ! empty( $content ) ) {
		$content = strip_shortcodes( $content ); // Strip shortcodes
		$content = wp_trim_words( $content, $word_length, $more );
	}

	return $content;
}