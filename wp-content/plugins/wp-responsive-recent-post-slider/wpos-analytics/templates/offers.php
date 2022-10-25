<?php
/**
 * Offers Page
 *
 * @package Wpos Analytic
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<style type="text/css">
	.notice, .error, div.fs-notice.updated, div.fs-notice.success, div.fs-notice.promotion{display:none !important;}
</style>

<div class="wrap wpos-anylc-offers">

	<?php foreach ($analy_product['offers'] as $offer_key => $offer_data) {

		// If status wise offer is there
		if( wpos_anylc_is_multi_arr( $offer_data ) ) {
			$offer_data = isset( $offer_data[ $opt_in ] ) ? $offer_data[ $opt_in ] : false;
		}

		if( empty( $offer_data ) ) {
			continue;
		}

		$has_offer	= true;
		$link 		= isset( $offer_data['link'] )		? $offer_data['link'] : '';
		$image 		= !empty( $offer_data['image'] ) 	? add_query_arg( array('v' => time()), $offer_data['image'] ) : '';
	?>

		<div class="wpos-anylc-offer-wrap">
			<?php if( !empty( $offer_data['name'] ) ) { ?>
			<div class="wpos-anylc-offer-title wpos-anylc-center"><?php echo $offer_data['name']; ?></div>
			<?php } ?>

			<?php if( $image ) { ?>
			<div class="wpos-anylc-offer-body wpos-anylc-center">
				<?php if( $link ) { ?>
				<a href="<?php echo esc_url( $link ); ?>" target="_blank">
					<img src="<?php echo esc_url( $image ); ?>" alt="" />
				</a>
				<?php } else { ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="" />
				<?php } ?>
			</div>
			<?php } ?>

			<?php if( !empty( $offer_data['desc'] ) ) { ?>
			<div class="wpos-anylc-offer-desc wpos-anylc-center"><?php echo wpautop( $offer_data['desc'] ); ?></div>
			<?php } ?>

			<?php if( !empty( $offer_data['button'] ) ) { ?>
			<div class="wpos-anylc-offer-footer wpos-anylc-center"><a href="<?php echo esc_url( $link ); ?>" class="button button-primary button-large wpos-anylc-btn" target="_blank"><?php echo $offer_data['button']; ?></a></div>
			<?php } ?>
		</div>

	<?php } // End of foreach

	// If no offer to display then redirect to main plugin screen
	if( empty( $has_offer ) ) { 
		$redirect_url = wpos_anylc_pdt_url( $analy_product ); // Redirect URL
	?>
		Please Wait... Redirecting to plugin screen. <a href="<?php echo $redirect_url; ?>">Click Here</a> in case you are not auto redirect.
		<script type="text/javascript">
			window.location = "<?php echo $redirect_url; ?>";
		</script>
	<?php } ?>

</div><!-- end .wrap -->