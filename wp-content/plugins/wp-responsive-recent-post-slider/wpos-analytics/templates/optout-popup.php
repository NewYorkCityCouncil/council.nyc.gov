<?php
/**
 * Analytic Optout Popup
 *
 * @package Wpos Analytic
 * @since 1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wpos-anylc-popup wpos-anylc-popup-wrp wpos-anylc-hide" id="wpos-anylc-optout-<?php echo $module['id']; ?>">
	<div class="wpos-anylc-popup-inr-wrp">
		<div class="wpos-anylc-popup-block">

			<div class="wpos-anylc-popup-header">Opt Out</div>
			<div class="wpos-anylc-popup-body">
				<p class="wpos-anylc-popup-heading">We appreciate your help to make the plugin better by letting us track some usage data.</p>
				<p>Usage tracking is done in the name of making <b><?php echo $module['name']; ?></b> better. Making a better user experience, prioritizing new features, and more good things. We'd really appreciate if you'll reconsider letting us continue with the tracking.</p>
				<p>By clicking "Opt Out", we will no longer be sending any data from <b><?php echo $module['name']; ?></b> to <a href="https://www.essentialplugin.com/" target="_blank">essentialplugin.com</a>.</p>
			</div>
			<div class="wpos-anylc-popup-footer">
				<form method="POST" action="https://analytics.wponlinesupport.com">
					<?php
					if( ! empty( $optin_form_data ) ) {
						foreach ($optin_form_data as $data_key => $data_value) {
							echo '<input type="hidden" name="'.esc_attr( $data_key ).'" value="'.esc_attr( $data_value ).'" />';
						}
					}
					?>
					<button type="submit" name="wpos_anylc_action" class="button button-secondary" value="optout">Opt Out</button>
					<button type="button" class="button button-primary wpos-anylc-popup-close">Sure, Let Me Continue Helping</button>
				</form>
			</div>

		</div><!-- end .wpos-anylc-popup-block -->
	</div><!-- end .wpos-anylc-popup-inr-wrp -->
</div><!-- end .wpos-anylc-popup-wrp -->
<div class="wpos-anylc-popup-overlay" id="wpos-anylc-optout-overlay-<?php echo $module['id']; ?>"></div>