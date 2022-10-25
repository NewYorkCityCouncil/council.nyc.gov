<?php
/**
 * Template for Slider - Design 4
 *
 * @package WP Responsive Recent Post Slider
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wppsac-post-slides">
	<div class="wppsac-post-list">
		<div class="wppsac-post-list-content">
			<?php if ( ! empty( $slider_orig_img ) ) { ?>
			<div class="wp-medium-5 wpcolumns">
				<div class="wppsac-post-image-bg">
					<a href="<?php the_permalink(); ?>">
						<img class="wppsac-post-image" <?php if( $lazyload ) { ?>data-lazy="<?php echo esc_url( $slider_orig_img ); ?>" <?php } ?> src="<?php echo esc_url( $feat_image ); ?>" alt="<?php the_title_attribute(); ?>" />
					</a>
				</div>
			</div>
			<?php } ?>

			<div class="<?php if ( ! empty( $feat_image ) ) { ?> wp-medium-7 <?php } else { ?> wp-medium-12 <?php } ?> wpcolumns">
				<?php if( $showCategory ) { ?>
				<div class="wppsac-post-categories"><?php echo wp_kses_post( $cat_list ); ?></div>
				<?php } ?>

				<h2 class="wppsac-post-title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>

				<?php if( $showDate || $showAuthor ) { ?>
				<div class="wppsac-post-date">
					<?php if( $showAuthor ) { ?> 
						<span><?php  esc_html_e( 'By', 'wp-responsive-recent-post-slider' ); ?> <?php the_author(); ?></span>
					<?php }

					echo ( $showAuthor && $showDate ) ? ' / ' : '';

					if( $showDate ) { echo get_the_date(); } ?>
				</div>
				<?php }

				if( $showContent ) {  ?>
				<div class="wppsac-post-content">
					<div class="wppsac-sub-content"><?php echo wppsac_get_post_excerpt( NULL, get_the_content(), $words_limit ); ?></div>

					<?php if( $showreadmore ) { ?>
					<a class="wppsac-readmorebtn" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Click to read more', 'wp-responsive-recent-post-slider' ); ?></a>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>