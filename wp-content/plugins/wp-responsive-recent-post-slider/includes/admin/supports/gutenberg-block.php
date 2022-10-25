<?php
/**
 * Blocks Initializer
 * 
 * @package WP Responsive Recent Post Slider
 * @since 2.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wprps_register_guten_block() {

	// Block Editor Script
	wp_register_script( 'wprps-free-block-js', WPRPS_URL.'assets/js/blocks.build.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor', 'wp-components' ), WPRPS_VERSION, true );
	wp_localize_script( 'wprps-free-block-js', 'Wprpsf_Block', array(
																'pro_demo_link'		=> 'https://demo.essentialplugin.com/prodemo/post-slider-pro/',
																'free_demo_link'	=> 'https://demo.essentialplugin.com/recent-post-slider-demo/',
																'pro_link'			=> WPRPS_PLUGIN_LINK_UNLOCK,
															));

	// Register block and explicit attributes for grid
	register_block_type( 'wprps/recent-post-slider', array(
		'attributes' => array(

			'limit' => array(
							'type'		=> 'number',
							'default'	=> 10,
						),
			'design' => array(
							'type'		=> 'string',
							'default'	=> 'design-1',
						),
			'category' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'show_date' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'show_category_name' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'show_content' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'content_words_limit' => array(
							'type'		=> 'number',
							'default'	=> 20,
						),
			'dots' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'arrows' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'autoplay' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'autoplay_interval' => array(
							'type'		=> 'number',
							'default'	=> 3000,
						),
			'speed' => array(
							'type'		=> 'number',
							'default'	=> 500,
						),
			'lazyload' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'posts' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'hide_post' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'post_type' => array(
							'type'		=> 'string',
							'default'	=> 'post',
						),
			'taxonomy' => array(
							'type'		=> 'string',
							'default'	=> 'category',
						),
			'show_author' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'show_read_more' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'media_size' => array(
							'type'		=> 'string',
							'default'	=> 'full',
						),
			'align' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'className' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
		),
		'render_callback' => 'wprps_recent_post_slider',
	));

	//Register block, and explicitly define the attributes for slider
	register_block_type( 'wprps/recent-post-carousel', array(
		'attributes' => array(
			'limit' => array(
							'type'		=> 'number',
							'default'	=> 10,
						),
			'design' => array(
							'type'		=> 'string',
							'default'	=> 'design-1',
						),
			'category' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'show_date' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'show_category_name' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'show_content' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'content_words_limit' => array(
							'type'		=> 'number',
							'default'	=> 20,
						),
			'slides_to_show' => array(
							'type'		=> 'number',
							'default'	=> 3,
						),
			'slides_to_scroll' => array(
							'type'		=> 'number',
							'default'	=> 1,
						),
			'dots' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'arrows' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'autoplay' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'autoplay_interval' => array(
							'type'		=> 'number',
							'default'	=> 3000,
						),
			'speed' => array(
							'type'		=> 'number',
							'default'	=> 500,
						),
			'lazyload' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'posts' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'hide_post' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'post_type' => array(
							'type'		=> 'string',
							'default'	=> 'post',
						),
			'taxonomy' => array(
							'type'		=> 'string',
							'default'	=> 'category',
						),
			'show_author' => array(
							'type'		=> 'boolean',
							'default'	=> true,
						),
			'show_read_more' => array(
							'type'		=> 'string',
							'default'	=> 'true',
						),
			'media_size' => array(
							'type'		=> 'string',
							'default'	=> 'full',
						),
			'align' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
			'className' => array(
							'type'		=> 'string',
							'default'	=> '',
						),
		),
		'render_callback' => 'wprps_post_carousel',
	));

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'wprps-free-block-js', 'wp-responsive-recent-post-slider', WPRPS_DIR . '/languages' );
	}

}
add_action( 'init', 'wprps_register_guten_block' );

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @since 2.3
 */
function wprps_block_assets() {	
}
add_action( 'enqueue_block_assets', 'wprps_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * 
 * @since 2.3
 */
function wprps_editor_assets() {

	// Block Editor CSS
	if( ! wp_style_is( 'wpos-free-guten-block-css', 'registered' ) ) {
		wp_register_style( 'wpos-free-guten-block-css', WPRPS_URL.'assets/css/blocks.editor.build.css', array( 'wp-edit-blocks' ), WPRPS_VERSION );
	}

	// Block Editor Script
	wp_enqueue_style( 'wpos-free-guten-block-css' );
	wp_enqueue_script( 'wprps-free-block-js' );

}
add_action( 'enqueue_block_editor_assets', 'wprps_editor_assets' );

/**
 * Adds an extra category to the block inserter
 *
 * @since 2.3
 */
function wprps_add_block_category( $categories ) {

	$guten_cats = wp_list_pluck( $categories, 'slug' );

	if( ! in_array( 'essp_guten_block', $guten_cats ) ) {
		$categories[] = array(
							'slug'	=> 'essp_guten_block',
							'title'	=> __( 'Essential Plugin Blocks', 'wp-responsive-recent-post-slider' ),
							'icon'	=> null,
						);
	}

	return $categories;
}
add_filter( 'block_categories_all', 'wprps_add_block_category' );