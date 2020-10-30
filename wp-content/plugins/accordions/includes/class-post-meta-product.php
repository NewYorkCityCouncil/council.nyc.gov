<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_accordions_post_meta_product{
	
	public function __construct(){

		//meta box action for "accordions"
		add_action('add_meta_boxes', array($this, '_post_meta_product'));
		add_action('save_post', array($this, '_post_meta_product_save'));



		}


	public function _post_meta_product($post_type){

        add_meta_box('accordions_product_metabox',__( 'Product FAQ Tab', 'accordions' ),array($this, 'meta_box_accordions_data'), 'product', 'side', 'high');

		}






	public function meta_box_accordions_data($post) {


        global $post;
        wp_nonce_field( 'meta_boxes_accordions_wc_input', 'meta_boxes_accordions_wc_input_nonce' );


        $accordions_id = get_post_meta( $post->ID, 'accordions_id', true );
        $accordions_tab_title = get_post_meta( $post->ID, 'accordions_tab_title', true );

        //var_dump($accordions_id);
        ?>


        <select style="width: 100%;" id="accordions_id" name="accordions_id">
            <option>Select accordion</option>
            <?php if(!empty($accordions_id)): ?>
            <option value="<?php echo $accordions_id; ?>" selected><?php echo get_the_title($accordions_id); ?></option>
            <?php endif; ?>
        </select>

        <span class="clear-faq-tab button">CLear</span>

        <p>
            <input style="width: 100%;" type="text" placeholder="Tab title" value="<?php echo $accordions_tab_title; ?>" name="accordions_tab_title">
        </p>


        <script>
            jQuery(document).ready(function($) {
                $(document).on('click', ".clear-faq-tab", function() {
                    $('#accordions_id').select2('destroy').val('').select2();
                })

                console.log(accordions_ajax.nonce);

                $('#accordions_id').select2({
                    ajax: {
                        url: accordions_ajax.accordions_ajaxurl, // AJAX URL is predefined in WordPress admin
                        dataType: 'json',
                        delay: 250, // delay in ms while typing when to perform a AJAX search
                        data: function (params) {
                            return {
                                q: params.term, // search query
                                action: 'accordions_ajax_wc_get_accordions', // AJAX action for admin-ajax.php
                                "nonce" : accordions_ajax.nonce,
                            };
                        },
                        processResults: function( data ) {
                            var options = [];
                            if ( data ) {

                                // data is the array of arrays, and each of them contains ID and the Label of the option
                                $.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                                    options.push( { id: text[0], text: text[1]  } );
                                });

                            }
                            return {
                                results: options
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 3, // the minimum of symbols to input before perform a search
                    allowClear: true,
                });
            })

        </script>

        <?php


    }



	public function _post_meta_product_save($post_id){

        global $post;


        $active_plugins = get_option('active_plugins');

        if( !empty($post) && $post->post_type=='product' && in_array( 'woocommerce/woocommerce.php', (array) $active_plugins ) ){



            /*
             * We need to verify this came from the our screen and with proper authorization,
             * because save_post can be triggered at other times.
             */

            // Check if our nonce is set.
            if ( ! isset( $_POST['meta_boxes_accordions_wc_input_nonce'] ) )
                return $post_id;

            $nonce = $_POST['meta_boxes_accordions_wc_input_nonce'];

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $nonce, 'meta_boxes_accordions_wc_input' ) )
                return $post_id;

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return $post_id;



            /* OK, its safe for us to save the data now. */

            // Sanitize user input.
            $accordions_id = sanitize_text_field( $_POST['accordions_id'] );
            $accordions_tab_title = sanitize_text_field( $_POST['accordions_tab_title'] );

            update_post_meta( $post_id, 'accordions_id', $accordions_id );
            update_post_meta( $post_id, 'accordions_tab_title', $accordions_tab_title );

        }


    }
	}


new class_accordions_post_meta_product();