<?php
/**
 * 'recent_post_carousel' Shortcode
 * 
 * @package WP Responsive Recent Post Slider
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wprps_post_carousel( $atts, $content = null ) {

	// Taking some global
	global $post;

	// SiteOrigin Page Builder Gutenberg Block Tweak - Do not Display Preview
	if( isset( $_POST['action'] ) && ($_POST['action'] == 'so_panels_layout_block_preview' || $_POST['action'] == 'so_panels_builder_content_json') ) {
		return "[recent_post_carousel]";
	}

	// Divi Frontend Builder - Do not Display Preview
	if( function_exists( 'et_core_is_fb_enabled' ) && isset( $_POST['is_fb_preview'] ) && isset( $_POST['shortcode'] ) ) {
		return '<div class="wppsac-builder-shrt-prev">
					<div class="wppsac-builder-shrt-title"><span>'.esc_html__('Recent Carousel View', 'wp-responsive-recent-post-slider').'</span></div>
					recent_post_carousel
				</div>';
	}

	// Fusion Builder Live Editor - Do not Display Preview
	if( class_exists( 'FusionBuilder' ) && (( isset( $_GET['builder'] ) && $_GET['builder'] == 'true' ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'get_shortcode_render' )) ) {
		return '<div class="wppsac-builder-shrt-prev">
					<div class="wppsac-builder-shrt-title"><span>'.esc_html__('Recent Carousel View', 'wp-slick-slider-and-image-carousel').'</span></div>
					recent_post_carousel
				</div>';
	}

	extract(shortcode_atts(array(
		'limit' 				=> 10,
		'design' 				=> 'design-1',
		'category'              => '',
		'show_date' 			=> 'true',
		'show_category_name' 	=> 'true',
		'show_content' 			=> 'true',
		'content_words_limit' 	=> 20,
		'slides_to_show' 		=> 3,
		'slides_to_scroll' 		=> 1,
		'dots'     				=> 'true',
		'arrows'     			=> 'true',
		'autoplay'     			=> 'true',
		'autoplay_interval' 	=> 3000,
		'speed'             	=> 500,
		'hide_post'        		=> array(),
		'posts'					=> array(),
		'post_type'       		=> 'post',
		'taxonomy'				=> 'category',
		'show_author' 			=> 'true',
		'show_read_more' 		=> 'true',
		'media_size'			=> 'full',
		'rtl'                  	=> 'false',
		'lazyload'				=> '',
		'className'				=> '',
		'align'					=> '',
		'extra_class'			=> '',
	), $atts, 'recent_post_carousel'));

	$unique 			= wppsac_get_unique();
	$shortcode_designs 	= wppsac_carousel_designs();
	$posts_per_page 	= ! empty( $limit ) 				? $limit 						: 10;
	$cat 				= ! empty( $category ) 				? explode( ',', $category ) 	: '';
	$design 			= ( $design && ( array_key_exists( trim( $design ), $shortcode_designs ) ) ) ? trim($design) : 'design-1';
	$showCategory 		= ( $show_category_name == 'true' ) ? true 							: false;
	$showContent 		= ( $show_content == 'true' ) 		? true 							: false;
	$showDate 			= ( $show_date == 'true') 			? true 							: false;
	$showAuthor 		= ( $show_author == 'true') 		? true 							: false;
	$showreadmore 		= ( $show_read_more == 'false') 	? false 						: true;
	$words_limit 		= ! empty( $content_words_limit ) 	? $content_words_limit	 		: 20;
	$slides_to_show 	= ! empty( $slides_to_show ) 		? $slides_to_show 				: 3;
	$slides_to_scroll 	= ! empty( $slides_to_scroll ) 		? $slides_to_scroll 			: 1;
	$dots 				= ( $dots == 'false' ) 				? 'false' 						: 'true';
	$arrows 			= ( $arrows == 'false' ) 			? 'false' 						: 'true';
	$autoplay 			= ( $autoplay == 'false' ) 			? 'false' 						: 'true';
	$autoplay_interval 	= ! empty( $autoplay_interval ) 	? $autoplay_interval 			: 3000;
	$speed 				= ! empty( $speed ) 				? $speed 						: 500;
	$post_type 			= ! empty( $post_type )             ? $post_type 					: 'post';
	$taxonomy 			= ! empty( $taxonomy )				? $taxonomy						: 'category';
	$media_size 		= ! empty( $media_size ) 			? $media_size 					: 'full'; // you can use thumbnail, medium, medium_large, large, full
	$exclude_post		= ! empty( $hide_post )				? explode( ',', $hide_post ) 	: array();
	$posts				= ! empty( $posts )					? explode( ',', $posts ) 		: array();
	$lazyload 			= ( $lazyload == 'ondemand' || $lazyload == 'progressive' ) ? $lazyload 	: ''; // ondemand or progressive
	$align				= ! empty( $align )					? 'align'.$align				: '';
	$extra_class		= $extra_class .' '. $align .' '. $className;
	$extra_class		= wppsac_sanitize_html_classes( $extra_class );

	// For RTL
	if( empty( $rtl ) && is_rtl() ) {
		$rtl = 'true';
	} elseif ( $rtl == 'true' ) {
		$rtl = 'true';
	} else {
		$rtl = 'false';
	}

	// Shortcode file
	$design_file_path 	= WPRPS_DIR . '/templates/carousel/' . $design . '.php';
	$design_file 		= file_exists( $design_file_path ) ? $design_file_path : '';

	// Enqueus required script
	wp_enqueue_script( 'wpos-slick-jquery' );
	wp_enqueue_script( 'wppsac-public-script' );

	// Slider configuration
	$carousel_conf = compact( 'slides_to_show', 'slides_to_scroll', 'dots', 'arrows', 'autoplay', 'autoplay_interval','speed', 'rtl', 'lazyload' );

	ob_start();

	// WP Query Parameters
	$args = array (
		'post_type'			=> $post_type,
		'post_status'		=> array( 'publish' ),
		'orderby'			=> 'date',
		'order'				=> 'DESC',
		'posts_per_page'	=> $posts_per_page,
		'post__not_in'		=> $exclude_post,
		'post__in'			=> $posts,
	);

	// Category Parameter
	if( $cat != "" ) {

		$args['tax_query'] = array(
								array(
									'taxonomy'	=> $taxonomy,
									'terms'		=> $cat,
									'field'		=> 'term_id',
								)
							);
	}

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) : ?>
		<div class="wppsac-wrap wppsac-slick-carousel-wrp wppsac-clearfix <?php echo esc_attr( $extra_class ); ?>" data-conf="<?php echo htmlspecialchars( json_encode( $carousel_conf )); ?>">
			<div id="wppsac-post-carousel-<?php echo esc_attr( $unique ); ?>" class="wppsac-post-slider-init wppsac-post-carousel <?php echo esc_attr( $design ); ?>">
				<?php while ( $query->have_posts() ) : $query->the_post();

					$post_id 			= isset( $post->ID ) ? $post->ID : '';
					$cat_list			= wppsac_get_category_list($post->ID, $taxonomy);
					$slider_orig_img 	= wppsac_get_post_featured_image( $post->ID, $media_size, true );
					$feat_image			= $slider_orig_img;

					if ( $lazyload ) {
						$feat_image	= WPRPS_URL.'assets/images/spacer.gif';
					}

					if( $design_file ) {
						include( $design_file );
					}
				endwhile; ?>
			</div>
		</div>
	<?php
	endif;

	wp_reset_postdata();

	$content .= ob_get_clean();
	return $content;
}

// Recent Post Carousel Shortcode
add_shortcode('recent_post_carousel', 'wprps_post_carousel');