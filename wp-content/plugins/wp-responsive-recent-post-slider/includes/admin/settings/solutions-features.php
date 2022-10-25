<?php
/**
 * Plugin Solutions & Features Page
 *
 * @package WP Responsive Recent Post Slider/Carousel
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Taking some variables
$wppsac_add_link = add_query_arg( array( 'post_type' =>WPRPS_POST_TYPE ), admin_url( 'post-new.php' ) );
?>

<div id="wrap">
	<div class="wppsac-sf-wrap">
		<div class="wppsac-sf-inr">
		
			<div class="wppsac-sf-features-section wppsac-sf-team wppsac-sf-center">
				<h1 class="wppsac-sf-heading">Now get best value from <span class="wppsac-sf-blue">Annual</span> OR <span class="wppsac-sf-blue">Lifetime</span> deal</h1>
				<h2>And Build <span class="bg-highlight">better websites</span>, <span class="bg-highlight">landing pages</span> & <span class="bg-highlight">conversion flow</span></h2>
				<h2>With <span class="wppsac-sf-blue">35+ plugins</span>, <span class="wppsac-sf-blue">2000+ templates</span> & $600 saving in <span class="wppsac-sf-blue">Essential Plugin Bundle</span></h2>
				<a href="<?php echo WPRPS_PLUGIN_BUNDLE_LINK; ?>"  target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> Grab Now This Deal</a>
			</div>
			<div class="wppsac-sf-features-section wppsac-sf-team wppsac-sf-center">
				<h1 class="wppsac-sf-heading">Powerful Team Behind <span class="wppsac-sf-blue">Post Slider</span></h1>
					<div class="wppsac-sf-cont">Alone we can do so little; together we can do so much. Our love language is helping small businesses grow and compete with the big guys.  Every time you see growth in your business, our little hearts go flip-flop!</div>
					<p></p>
					<div class="wppsac-sf-cont">This is why I wanted to introduce you to <span class="wppsac-sf-blue">Anoop Ranawat & Team</span> at EssentialPlugin.com</div>
					<img class="wppsac-sf-image" src="<?php echo WPRPS_URL; ?>/assets/images/wpos-team.png" alt="wpos team" />
			</div>

			<!-- Start - Welcome Box -->
			<div class="wppsac-sf-welcome-wrap">
				<div class="wppsac-sf-welcome-inr wppsac-sf-center">
						<h5 class="wppsac-sf-content">Experience <span class="wppsac-sf-blue">4 Layouts</span>, <span class="wppsac-sf-blue">50+ stunning designs</span>  with which show your recent blogs/posts in a slider/carousel form with excerpts and unique slider & carousel designs.</h5>
						<h5 class="wppsac-sf-content"><span class="wppsac-sf-blue">30,000+ </span>websites are using <span class="wppsac-sf-blue">Post Slider/Carousel</span>.</h5>
						<a href="<?php echo esc_url( $wppsac_add_link ); ?>" class="wppsac-sf-btn">Launch Post Slider With Free Features</a> <br /><b>OR</b> <br /> <a href="<?php echo WPRPS_PLUGIN_BUNDLE_LINK; ?>"  target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> Grab Now With Essential Bundle</a>
						<div class="wppsac-rc-wrap">
							<div class="wppsac-rc-inr wppsac-rc-bg-box">
								<div class="wppsac-rc-icon">
									<img src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/popup-icon/14-days-money-back-guarantee.png" alt="14-days-money-back-guarantee" title="14-days-money-back-guarantee" />
								</div>
								<div class="wppsac-rc-cont">
									<h3>14 Days Refund Policy</h3>
									<p>14-day No Question Asked Refund Guarantee</p>
								</div>
							</div>
							<div class="wppsac-rc-inr wppsac-rc-bg-box">
								<div class="wppsac-rc-icon">
									<img src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/popup-icon/popup-design.png" alt="popup-design" title="popup-design" />
								</div>
								<div class="wppsac-rc-cont">
									<h3>Include Done-For-You Recent Post Slider Setup</h3>
									<p>Our experts team will design 1 free Recent Post Slider for you as per your need.</p>
								</div>
							</div>
						</div>
					<div class="wppsac-sf-welcome-left"></div>
					<div class="wppsac-sf-welcome-right"></div>
				</div>
			</div>
			<!-- End - Welcome Box -->

			<!-- Start - WP Responsive Recent Post Slider/Carousel - Features -->
			<div class="wppsac-features-section">
				<div class="wppsac-center wppsac-features-ttl">
					<h1 class="wppsac-sf-heading">Powerful Pro Features, Simplified</h1>
				</div>
				<div class="wppsac-sf-welcome-wrap wppsac-sf-center">
					<div class="wppsac-features-box-wrap">
						<ul class="wppsac-features-box-grid">
							<li>
							<div class="wppsac-popup-icon"><img src="<?php echo WPRPS_URL; ?>/assets/images/popup-icon/slider.png" /></div>
							Recent Post Slider View</li>
							<li>
							<div class="wppsac-popup-icon"><img src="<?php echo WPRPS_URL; ?>/assets/images/popup-icon/slider-carousel.png" /></div>
							Recent Post Carousel View</li>
							<li>
							<div class="wppsac-popup-icon"><img src="<?php echo WPRPS_URL; ?>/assets/images/popup-icon/grid-box.png" /></div>
							Recent Post Gridbox View</li>
							<!-- <li>
							<div class="wppsac-popup-icon"><img src="<?php //echo WPRPS_URL; ?>/assets/images/popup-icon/centermode.png" /></div>
							Center Mode View</li> -->
						</ul>
					</div>
					<a href="<?php echo WPRPS_PLUGIN_BUNDLE_LINK; ?>" target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> Grab Now Pro Features</a>
					<div class="wppsac-rc-wrap">
						<div class="wppsac-rc-inr wppsac-rc-bg-box">
							<div class="wppsac-rc-icon">
								<img src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/popup-icon/14-days-money-back-guarantee.png" alt="14-days-money-back-guarantee" title="14-days-money-back-guarantee" />
							</div>
							<div class="wppsac-rc-cont">
								<h3>14 Days Refund Policy. 0 risk to you.</h3>
								<p>14-day No Question Asked Refund Guarantee</p>
							</div>
						</div>
						<div class="wppsac-rc-inr wppsac-rc-bg-box">
							<div class="wppsac-rc-icon">
								<img src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/popup-icon/popup-design.png" alt="popup-design" title="popup-design" />
							</div>
							<div class="wppsac-rc-cont">
								<h3>Include Done-For-You Recent Post Slider Setup</h3>
								<p>Our  experts team will design 1 free Recent Post Slider for you as per your need.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End - Logo Showcase - Features -->

			<!-- Start - Testimonial Section -->
			<div class="wppsac-sf-testimonial-wrap">
				<div class="wppsac-center wppsac-features-ttl">
					<h1 class="wppsac-sf-heading">Looking for a Reason to Use Recent Post Slider? Here are 80+...</h1>
				</div>
				<div class="wppsac-testimonial-section-inr">
					<div class="wppsac-testimonial-box-wrap">
						<div class="wppsac-testimonial-box-grid">
							<h3 class="wppsac-testimonial-title">Nice looking result with a very simple setup</h3>
							<div class="wppsac-testimonial-desc">Thank you for the work done with this plugin. It was the solution we were looking for.</div>
							<div class="wppsac-testimonial-clnt">@dfasite</div>
							<div class="wppsac-testimonial-rating"><img src="<?php echo WPRPS_URL; ?>/assets/images/rating.png" /></div>
						</div>
						<div class="wppsac-testimonial-box-grid">
							<h3 class="wppsac-testimonial-title">Excellent plugin & support</h3>
							<div class="wppsac-testimonial-desc">Excellent plugin & support The perfect solution i need</div>
							<div class="wppsac-testimonial-clnt">@vitomartin_com</div>
							<div class="wppsac-testimonial-rating"><img src="<?php echo WPRPS_URL; ?>/assets/images/rating.png" /></div>
						</div>
						<div class="wppsac-testimonial-box-grid">
							<h3 class="wppsac-testimonial-title">Nice plugin and best support</h3>
							<div class="wppsac-testimonial-desc">A simple but very nice plugin that does what it promises. In addition, I had an incompatibility problem with my theme (Extra theme from ET) with which they have helped me quickly and effectively, so I highly recommend them!</div>
							<div class="wppsac-testimonial-clnt">@dtemporetti</div>
							<div class="wppsac-testimonial-rating"><img src="<?php echo WPRPS_URL; ?>/assets/images/rating.png" /></div>
						</div>
						<div class="wppsac-testimonial-box-grid">
							<h3 class="wppsac-testimonial-title">Good plugin and best support!</h3>
							<div class="wppsac-testimonial-desc">I’ve had issue with incompatibility with my theme, but support responded quickly and resolved the issue even quicker! Kudos to them! Earned my recommendation.</div>
							<div class="wppsac-testimonial-clnt">@skyteamdesign2020</div>
							<div class="wppsac-testimonial-rating"><img src="<?php echo WPRPS_URL; ?>/assets/images/rating.png" /></div>
						</div>
						<div class="wppsac-testimonial-box-grid">
							<h3 class="wppsac-testimonial-title">A must have for every blog and news website</h3>
							<div class="wppsac-testimonial-desc">Best and awesome plugin out there even compare to premium ones. A truly working plugin with a lot of flexibility.Support RTL languages like my native Persian out of the box. And last but not least a great support team. </div>
							<div class="wppsac-testimonial-clnt">@tikroute</div>
							<div class="wppsac-testimonial-rating"><img src="<?php echo WPRPS_URL; ?>/assets/images/rating.png" /></div>
						</div>
						<div class="wppsac-testimonial-box-grid">
							<h3 class="wppsac-testimonial-title">One of the best plug in I ever use</h3>
							<div class="wppsac-testimonial-desc">This plugin is easy to set up and their customer support is extremely helpful which make this plugin one of the best in the market.</div>
							<div class="wppsac-testimonial-clnt">@fahad121</div>
							<div class="wppsac-testimonial-rating"><img src="<?php echo WPRPS_URL; ?>/assets/images/rating.png" /></div>
						</div>
					</div>
					<a href="https://wordpress.org/support/plugin/wp-responsive-recent-post-slider/reviews/?filter=5" target="_blank" class="wppsac-sf-btn"><span class="dashicons dashicons-star-filled"></span> View All Reviews</a> OR <a href="<?php echo WPRPS_PLUGIN_BUNDLE_LINK; ?>"  target="_blank" class="wppsac-sf-btn wppsac-sf-btn-orange"><span class="dashicons dashicons-cart"></span> Grab Now Pro Features</a>
					<div class="wppsac-rc-wrap">
						<div class="wppsac-rc-inr wppsac-rc-bg-box">
							<div class="wppsac-rc-icon">
								<img src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/popup-icon/14-days-money-back-guarantee.png" alt="14-days-money-back-guarantee" title="14-days-money-back-guarantee" />
							</div>
							<div class="wppsac-rc-cont">
								<h3>14 Days Refund Policy. 0 risk to you.</h3>
								<p>14-day No Question Asked Refund Guarantee</p>
							</div>
						</div>
						<div class="wppsac-rc-inr wppsac-rc-bg-box">
							<div class="wppsac-rc-icon">
								<img src="<?php echo esc_url( WPRPS_URL ); ?>assets/images/popup-icon/popup-design.png" alt="popup-design" title="popup-design" />
							</div>
							<div class="wppsac-rc-cont">
								<h3>Include Done-For-You Recent Post Slider Setup</h3>
								<p>Our  experts team will design 1 free Recent Post Slider for you as per your need.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End - Testimonial Section -->
		</div>
	</div><!-- end .wppsac-sf-wrap -->
</div><!-- end .wrap -->