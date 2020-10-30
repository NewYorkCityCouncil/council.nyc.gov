<?php
if ( ! defined('ABSPATH')) exit;  // if direct access




add_filter( 'woocommerce_product_tabs', 'accordions_product_tab' );
function accordions_product_tab( $tabs ) {

	$prouct_id = get_the_id();
	$accordions_id = get_post_meta( $prouct_id, 'accordions_id', true );
	$accordions_tab_title = get_post_meta( $prouct_id, 'accordions_tab_title', true );

    $accordions_tab_title = !empty($accordions_tab_title) ? $accordions_tab_title : __( 'FAQ', 'accordions' );

	if(!empty($accordions_id)):
		$tabs['accordions_faq'] = array(
			'title' 	=> $accordions_tab_title,
			'priority' 	=> 50,
			'callback' 	=> 'woo_product_tab_accordions_content'
		);
    endif;


	return $tabs;

}
function woo_product_tab_accordions_content() {

    $prouct_id = get_the_id();
	// The new tab content
	$accordions_id = get_post_meta( $prouct_id, 'accordions_id', true );


	if(!empty($accordions_id)):
		echo do_shortcode('[accordions id="'.$accordions_id.'"]');
    endif;


}

function accordions_ajax_wc_get_accordions(){

	$return = array();

    $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';

    //error_log($nonce);

   if(wp_verify_nonce( $nonce, 'accordions_nonce' )) {

        if(current_user_can( 'manage_options' )) {
            // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
            $search_results = new WP_Query(array(
                's' => $_GET['q'], // the search query
                'post_type' => 'accordions',
                'post_status' => 'publish', // if you don't want drafts to be returned
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1 // how much to show at once
            ));
            if ($search_results->have_posts()) :
                while ($search_results->have_posts()) : $search_results->the_post();
                    // shorten the title a little
                    $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                    $return[] = array($search_results->post->ID, $title); // array( Post ID, Post Title )
                endwhile;
            endif;
        }
    }
	echo json_encode( $return );
	die;

}


add_action('wp_ajax_accordions_ajax_wc_get_accordions', 'accordions_ajax_wc_get_accordions');
add_action('wp_ajax_nopriv_accordions_ajax_wc_get_accordions', 'accordions_ajax_wc_get_accordions');