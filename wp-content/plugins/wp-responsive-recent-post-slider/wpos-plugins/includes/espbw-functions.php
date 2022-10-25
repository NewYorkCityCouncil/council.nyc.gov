<?php
/**
 * Common Functions
 *
 * @package Essential Plugins Bundle
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to sort plugins api data
 * 
 * @since 1.0
 */
function wpos_espbw_sort_plugin_data( $a, $b ) {

	$a_active_installs = is_numeric( $a['active_installs'] ) ? $a['active_installs'] : 0;
	$b_active_installs = is_numeric( $b['active_installs'] ) ? $b['active_installs'] : 0;
	
	if ($a_active_installs == $b_active_installs) {
		return 0;
	}
	return ($a_active_installs > $b_active_installs) ? -1 : 1;
}

/**
 * Function to add script and style at admin side
 * 
 * @since 1.0
 */
function wpos_espbw_get_plugin_data() {

	// Get cache result
	$plugins_data = get_transient( 'espbw_plugins_data' );

	// If no cache is there
	if( empty( $plugins_data ) ) {

		// Call Plugin API
		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin-install.php';
		}

		$plugins_data = plugins_api( 'query_plugins', array(
											'per_page'	=> 60,
											'author'	=> 'wponlinesupport',
											'fields'	=> array(
																'icons'				=> true,
																'active_installs'	=> true,
															)
										) );

		if( is_wp_error( $plugins_data ) || empty( $plugins_data->plugins ) ) {

			$file = WPOS_ESPBW_DIR . 'plugins-data.json';

			// We don't need to write to the file, so just open for reading.
			$fp = fopen( $file, 'r' );

			// Pull data of the file in.
			$file_data = fread( $fp, 1024 * KB_IN_BYTES );

			// Close file handle
			fclose( $fp );

			$file_data				= utf8_encode($file_data); 
			$plugins_data_arr		= json_decode( $file_data, true );
			$plugins_data			= json_decode( $file_data );
			$plugins_data->plugins	= $plugins_data_arr['plugins'];
		}

		if( ! is_wp_error( $plugins_data ) && ! empty( $plugins_data->plugins ) ) {

			// Sort the data based on active install
			usort( $plugins_data->plugins, "wpos_espbw_sort_plugin_data" );

			set_transient( 'espbw_plugins_data', $plugins_data, (12 * HOUR_IN_SECONDS) );
		}
	}

	return $plugins_data;
}

/**
 * Add some filter classes for plugins.
 * 
 * @since 1.0
 */
function wpos_espbw_plugins_filter() {

	$plugin_filters = array(
		'sp-faq'						=> array(
												'class' => 'espbw-recommended espbw-showcase',
												'tags'	=> 'faq, faq list, faq plugin, faqs, wp faq with category, jquery ui accordion, faq with accordion, frequently asked questions, wordpress faq',
											),
		'app-mockups-carousel'			=> array(
												'class' => 'espbw-sliders',
												'tags'	=> 'app mockups carousel, mockups, device mockup, mockup slider, app gallery slider, app gallery Carousel, device gallery carousel, app mockups carousel, mockups carousel',
											),
		'countdown-timer-ultimate'		=> array(
												'class' => 'espbw-recommended espbw-showcase',
												'tags'	=> 'countdown timer, timer, timer countdown, countdown, event countdown timer, animated countdown timer, birthday countdown, clock, count down, countdown, countdown clock, countdown generator, countdown system, countdown timer, countdown timer, date countdown, event countdown, flash countdown, jQuery countdown, time counter, website countdown, wp countdown, wp countdown timer',
											),
		'featured-post-creative'		=> array(
												'class' => 'espbw-post espbw-showcase',
												'tags'	=> 'featured post, featured post grid, featured post widget, responsive featured post grid, responsive featured post, featured post brick layout, featured posts',
											),
		'footer-mega-grid-columns'		=> array(
												'class' => '',
												'tags'	=> 'footer, footer widgets, footer widgets in grid, website footer, footer, mega footer, megafooter',
											),
		'hero-banner-ultimate'			=> array(
												'class' => '',
												'tags'	=> 'hero image, hero banner, hero header, hero video, video background, hero video, youtube video background, vimeo video background',
											),
		'inboundwp-lite'				=> array(
												'class' => 'espbw-marketing',
												'tags'	=> 'Spin Wheel, WhatsApp chat Support, Inbound, Inbound marketing, Better Heading, Social Proof, Testimonial, Review, Deal Countdown Timer, Marketing PopUp',
											),
		'popup-anything-on-click'		=> array(
												'class' => 'espbw-recommended',
												'tags'	=> 'modal popup, popup, modal, full screen popup, html popup, image popup, popup on click, modal popup on click, full screen popup on click, click popup',
											),
		'portfolio-and-projects'		=> array(
												'class' => 'espbw-recommended',
												'tags'	=> 'portfolio, portfolio listing, projects, project grid, project portfolio, Responsive Portfolio, portfolio categories, add portfolio, add portfolio plugin, portfolio gallery, portfolio plugin, career portfolio, googole image style, best portfolio, portfolio display, project management',
											),
		'maintenance-mode-with-timer'	=> array(
												'class' => '',
												'tags'	=> 'maintenance mode, coming soon, maintenance mode with timer, maintenance mode with countdown timer, countdown timer, coming soon with countdown timer, offline, site is offline, site offline, under construction, launch, launch page, maintenance',
											),
		'preloader-for-website'			=> array(
												'class' => '',
												'tags'	=> 'page loader, loader, page load animations, animated pre-loader, animated preloader, colorful, customize, Jquery Loader, jquery pre-loader, jquery preloader, loader, pre-loader, preload, preloader',
											),
		'search-and-navigation-popup'	=> array(
												'class' => '',
												'tags'	=> 'serchbox popup, menubar popup, navigation popup, serchbox popup',
											),
		'smooth-scroll-by-wpos'			=> array(
												'class' => '',
												'tags'	=> 'mousewheel scroll, scroll, smooth scroll, scrolling, go to top, back to top, scroll to element, scroll to section, smooth scroll to element, smooth scroll to section',
											),
		'ticker-ultimate'				=> array(
												'class' => 'espbw-recommended espbw-post espbw-showcase',
												'tags'	=> 'wponlinesupport, ticker, news ticker, blog ticker, post ticker, ticker slider, ticker vertical slider, ticker horizontal slider',
											),
		'wp-blog-and-widgets'			=> array(
												'class' => 'espbw-recommended espbw-post espbw-showcase',
												'tags'	=> 'blog design, blog layout, wordpress blog , custom blog template, wordpress blog widget, blog layout design, custom blog layout, Free wordpress blog, blog custom post type, blog menu, blog page with custom post type, blog, latest blog, custom post type, cpt, widget',
											),
		'sp-news-and-widget'			=> array(
												'class' => 'espbw-recommended espbw-post espbw-showcase',
												'tags'	=> 'wordpress news plugin, news website, news page scrolling , wordpress vertical news plugin widget, wordpress horizontal news plugin widget, scrolling news wordpress plugin, scrolling news widget wordpress plugin, WordPress set post or page as news, WordPress dynamic news, news, latest news, custom post type, cpt, widget, vertical news scrolling widget, news widget',
											),
		'wp-testimonial-with-widget'	=> array(
												'class' => 'espbw-recommended espbw-showcase',
												'tags'	=> 'testimonial, Testimonial, testimonials, Testimonials, widget, Best testimonial slider, Responsive testimonial slider, client testimonial slider, easy testimonial slider, testimonials with widget, wordpress testimonial with widget, testimonial rotator, testimonial slider, Testimonial slider, testimonial with shortcode, client testimonial, client quote',
											),
		'timeline-and-history-slider'	=> array(
												'class' => 'espbw-recommended espbw-post espbw-showcase',
												'tags'	=> 'timeline slider, life history, history slider, company story timeline, process slider, process, responsive timeline, about us, achievements, Activity Log, awesome company timeline, biography, events timeline, history, history timeline, life achievements, lifestream, story, personal timeline',
											),
		'wp-team-showcase-and-slider'	=> array(
												'class' => 'espbw-recommended espbw-showcase',
												'tags'	=> 'team, teamshowcase, team slider, responsive teamshowcase, teamshowcase rotator, employees, meet team, members, skills, staff, team, v-card, members profile, my team, our team, responsive team display, responsive team, team members, team members profile, team profile, team showcase, tlp team, WordPress Team Member',
											),
		'recent-posts-widget-designer'	=> array(
												'class' => '',
												'tags'	=> 'post widget, post widget with thumbnail, post widget designer, post widget designs, recent post widget with thumbnail, recent post widget designer, recent post widget designs',
											),
		'styles-for-wp-pagenavi-addon'	=> array(
												'class' => '',
												'tags'	=> 'navigation, pagination, paging, pages, navigation, pagenavi style, wp pagenavi styling, pagenavi styling, pagenavi css',
											),
		'post-grid-and-filter-ultimate'	=> array(
												'class' => 'espbw-post espbw-showcase',
												'tags'	=> 'post grid, post, post filter, post category filter, custom post grid, grid display, grid, content grid, filter, post designs, grid designs',
											),
		'accordion-and-accordion-slider'			=> array(
															'class' => 'espbw-showcase',
															'tags'	=> 'accordion, accordion image slider, accordion, horizontal accordion, vertical accordion, responsive accordion, accordion carousel,',
														),
		'html5-videogallery-plus-player'			=> array(
															'class' => 'espbw-recommended espbw-showcase',
															'tags'	=> 'video, youtube video gallery, vimeo video gallery, youtube video gallery with popup, Youtube-video, youtube embed, youtube gallery, youtube player, magnific Popup, vimeo video gallery gallery, HTML5 video player, HTML5 video gallery, wordpress HTML5 video, wordpress HTML5 video player, wordpress HTML5 video gallery, responsive, wordpress responsive video gallery',
														),
		'wp-featured-content-and-slider'			=> array(
															'class' => 'espbw-recommended espbw-showcase',
															'tags'	=> 'content slider, slider, features, services, featured content, featured services, featured content rotator, featured content slider, featured content slideshow, featured posts, featured content slider',
														),
		'wp-responsive-recent-post-slider'			=> array(
															'class' => 'espbw-recommended espbw-post espbw-showcase',
															'tags'	=> 'post slider, posts slider, recent post slider, recent posts slider, slider, responsive post slider, responsive posts slider, responsive recent post slider, responsive recent posts slider, wordpress posts slider, post slideshow, posts slideshow, recent posts slideshow',
														),
		'blog-designer-for-post-and-widget'			=> array(
															'class' => 'espbw-recommended espbw-post espbw-showcase',
															'tags'	=> 'post, post design, post designer, post designs, post layout, post layout design, post widget, blog, blog designs, blog design, stylist post, post slider, post grid, recent post, recent post slider, recent post designs, posts in page, post carousel slider',
														),
		'wp-slick-slider-and-image-carousel'		=> array(
															'class' => 'espbw-recommended espbw-sliders espbw-showcase',
															'tags'	=> 'slick, image slider, slick slider, slick image slider, slider, image slider, header image slider, responsive image slider, responsive content slider, carousel, image carousel, carousel slider, content slider, coin slider, touch slider, text slider, responsive slider, responsive slideshow, Responsive Touch Slider, wp slider, wp image slider, wp header image slider, photo slider, responsive photo slider',
														),
		'wp-trending-post-slider-and-widget'		=> array(
															'class' => 'espbw-post espbw-showcase',
															'tags'	=> 'popular post, popular posts, trending, trending posts carousel trending post, trending posts, trending posts carousel, popular posts slider, trending posts slider, widget, shortcodes, slider, post slick slider, trending posts widget, popular posts widget, daily popular, page views, popular posts, top posts',
														),
		'audio-player-with-playlist-ultimate'		=> array(
															'class' => 'espbw-showcase',
															'tags'	=> 'audio player with playlist, album art, artist, audio player, audio player with playlist, multiple player, music player, repeat, shuffle, single player, song title',
														),
		'sliderspack-all-in-one-image-sliders'		=> array(
															'class' => 'espbw-recommended espbw-sliders espbw-showcase',
															'tags'	=> 'logo ticker, bxslider, meta slider, flexslider, fancybox, nivo slider, owl slider, unslider , wallop slider , bx slider, flex slider, rolling slider, image slider, slider, 3d slider, 3d image slider, 3d image carousel, image carousel, carousel, swiper, swiper carousel, Cascade Slider',
														),
		'album-and-image-gallery-plus-lightbox'		=> array(
															'class' => 'espbw-recommended espbw-showcase',
															'tags'	=> 'album, image album, gallery, magnific image slider, image gallery, responsive image gallery, image slider, image gallery slider, gallery slider, album slider, lightbox, albums, best gallery plugin, photo gallery, galleries, gallery, image captions, media gallery, photo albums, photo gallery, photography, Picture Gallery, pictures, responsive galleries, responsive gallery, slideshow galleries, slideshow gallery, thumbnail galleries, thumbnail gallery, wordpress gallery plugin, wordpress photo gallery plugin, wordpress responsive gallery, wp gallery, wp gallery plugins',
														),
		'wp-modal-popup-with-cookie-integration'	=> array(
															'class' => '',
															'tags'	=> 'popup',
														),
		'meta-slider-and-carousel-with-lightbox'	=> array(
															'class' => 'espbw-recommended espbw-sliders',
															'tags'	=> 'frontend gallery slider, frontend gallery Carousel, image slider, image carousel, meta gallery slider, meta gallery carousel, gallery slider, gallery',
														),
		'post-category-image-with-grid-and-slider'	=> array(
															'class' => 'espbw-sliders espbw-showcase',
															'tags'	=> 'category, category image, post category image, post category image grid, post category image slider, customization, custom category image, category featured image, category grid, category slider',
														),
		'wp-logo-showcase-responsive-slider-slider'	=> array(
															'class' => 'espbw-recommended espbw-showcase',
															'tags'	=> 'logo slider, logo slider, widget, client logo carousel, client logo slider, client, customer, image carousel, carousel, logo showcase, Responsive logo slider, Responsive logo carousel, WordPress logo slider, WordPress logo carousel, slick carousel, Best logo showcase, easy logo slider, logo carousel wordpress, logo slider wordpress, sponsors, sponsors slider, sponsors carousel',
														),
		'product-categories-designs-for-woocommerce'		=> array(
																	'class' => 'espbw-woocommerce espbw-showcase',
																	'tags'	=> 'woocommerce, categories designs, categories slider, categories grid, WooCommerce categories designs, WooCommerce categories slider, WooCommerce categories grid',
																),
		'woo-product-slider-and-carousel-with-category'		=> array(
																	'class' => 'espbw-woocommerce',
																	'tags'	=> 'woocommerce, best selling products, best selling products slider, slick slider, best selling products by category, shortcode, template code, featured product, featured product slider, Featured product by category, autoplay slider, best product slider, best product slider for woo shop, carousel, clean woo product slider, multiple product slider, product carousel, product content slider, product contents carousel, product slider, product slider carousel for woo, products slider, responsive product slider, responsive product carousel, slider, smooth product slider woo product slider, advance slider, woo best selling products, woo category slider, latest products, most selling products, product carousel slider, recent product carousel, recent product slider',
																),
		'slider-and-carousel-plus-widget-for-instagram'		=> array(
																	'class' => 'espbw-recommended espbw-sliders espbw-showcase',
																	'tags'	=> 'Custom Instagram Feed, feed, hashtag, instagram, Instagram feed, instagram gallery, Instagram images, Instagram photos, Instagram posts, Instagram wall, lightbox, photos, instagram social feed, show instagram post, responsive instgram, beautiful instagram, instagram widget, instgram plugin, artistic instagram, instagram wordpress, smashing instgram',
																),
		'frontend-gallery-slider-for-advanced-custom-field' => array(
																	'class' => 'espbw-recommended espbw-sliders espbw-showcase',
																	'tags'	=> 'frontend gallery slider, frontend gallery Carousel, slider, acf frontend gallery slider, acf frontend gallery Carousel, acf gallery, acf',
																),
	);

	return $plugin_filters;
}