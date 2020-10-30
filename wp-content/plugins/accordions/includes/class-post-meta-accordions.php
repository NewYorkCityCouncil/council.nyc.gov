<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_accordions_post_meta{
	
	public function __construct(){

		//meta box action for "accordions"
		add_action('add_meta_boxes', array($this, '_post_meta_accordions'));
		add_action('save_post', array($this, '_post_meta_accordions_save'));



		}


	public function _post_meta_accordions($post_type){

            add_meta_box('metabox-accordions',__('Accordions data', 'accordions'), array($this, 'meta_box_accordions_data'), 'accordions', 'normal', 'high');

		}






	public function meta_box_accordions_data($post) {
 
        // Add an nonce field so we can check for it later.
        wp_nonce_field('accordions_nonce_check', 'accordions_nonce_check_value');
 
        // Use get_post_meta to retrieve an existing value from the database.
       // $accordions_data = get_post_meta($post -> ID, 'accordions_data', true);

        $post_id = $post->ID;



        $settings_tabs_field = new settings_tabs_field();

        $accordion_settings_tab = array();
        $accordions_options = get_post_meta($post_id,'accordions_options', true);
        $current_tab = isset($accordions_options['current_tab']) ? $accordions_options['current_tab'] : 'content';
        $view_type = !empty($accordions_options['view_type']) ? $accordions_options['view_type'] : 'accordion';

        //var_dump($view_type);

        $accordion_settings_tab[] = array(
            'id' => 'shortcode',
            'title' => sprintf(__('%s Shortcode','accordions'),'<i class="fas fa-laptop-code"></i>'),
            'priority' => 1,
            'active' => ($current_tab == 'shortcode') ? true : false,
        );

        // $accordion_settings_tab[] = array(
        //     'id' => 'general',
        //     'title' => sprintf(__('%s General','accordions'),'<i class="fa fa-cogs"></i>'),
        //     'priority' => 2,
        //     'active' => ($current_tab == 'general') ? true : false,
        // );

        $accordion_settings_tab[] = array(
            'id' => 'accordion_options',
            'title' => sprintf(__('%s Accordion options','accordions'),'<i class="fas fa-bars"></i>'),
            'priority' => 2,
            'active' => ($current_tab == 'accordion_options') ? true : false,
            'hidden' => ($view_type == 'tabs')? true : false ,
            'data_visible' => 'accordion',
        );
        $accordion_settings_tab[] = array(
            'id' => 'style',
            'title' => sprintf(__('%s Style','accordions'),'<i class="fas fa-palette"></i>'),
            'priority' => 3,
            'active' => ($current_tab == 'style') ? true : false,
        );

        $accordion_settings_tab[] = array(
            'id' => 'tabs_options',
            'title' => sprintf(__('%s Tabs options','accordions'),'<i class="far fa-folder"></i>'),
            'priority' => 2,
            'active' => ($current_tab == 'tabs_options') ? true : false,
            'hidden' => ($view_type == 'accordion')? true : false ,
            'data_visible' => 'tabs',


        );


        $accordion_settings_tab[] = array(
            'id' => 'content',
            'title' => sprintf(__('%s Content','accordions'),'<i class="far fa-edit"></i>'),
            'priority' => 4,
            'active' => ($current_tab == 'content') ? true : false,
        );



        // $accordion_settings_tab[] = array(
        //     'id' => 'custom_scripts',
        //     'title' => sprintf(__('%s Custom scripts','accordions'),'<i class="far fa-file-code"></i>'),
        //     'priority' => 6,
        //     'active' => ($current_tab == 'buy_pro') ? true : false,
        // );

        $accordion_settings_tab[] = array(
            'id' => 'help_support',
            'title' => sprintf(__('%s Help support','accordions'),'<i class="fas fa-hands-helping"></i>'),
            'priority' => 80,
            'active' => ($current_tab == 'help_support') ? true : false,
        );


        // $accordion_settings_tab[] = array(
        //     'id' => 'buy_pro',
        //     'title' => sprintf(__('%s Buy pro','accordions'),'<i class="fas fa-store"></i>'),
        //     'priority' => 90,
        //     'active' => ($current_tab == 'buy_pro') ? true : false,
        // );


        $accordion_settings_tab = apply_filters('accordions_metabox_navs', $accordion_settings_tab);

        $tabs_sorted = array();

        if(!empty($accordion_settings_tab))
        foreach ($accordion_settings_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
        array_multisort($tabs_sorted, SORT_ASC, $accordion_settings_tab);


		?>

        <script>
            jQuery(document).ready(function($){
                $(document).on('click', '.settings-tabs input[name="accordions_options[view_type]"]', function(){
                    var val = $(this).val();

                    console.log( val );

                    $('.settings-tabs .tab-navs li').each(function( index ) {
                        data_visible = $( this ).attr('data_visible');

                        if(typeof data_visible != 'undefined'){
                            //console.log('undefined '+ data_visible );

                            n = data_visible.indexOf(val);
                            if(n<0){
                                $( this ).hide();
                            }else{
                                $( this ).show();
                            }
                        }else{
                            console.log('Not matched: '+ data_visible );


                        }
                    });


                })
            })


        </script>

        <div class="settings-tabs vertical">
            <input class="current_tab" type="hidden" name="accordions_options[current_tab]" value="<?php echo $current_tab; ?>">
            <div class="view-types">

                <?php

                $accordions_view_types = apply_filters('accordions_view_types', array('accordion'=>'Accordion', 'tabs'=>'Tabs'));

                $args = array(
                    'id'		=> 'view_type',
                    'parent'		=> 'accordions_options',
                    'title'		=> __('View type','accordions'),
                    'details'	=> '',
                    'type'		=> 'radio',
                    'value'		=> $view_type,
                    'default'		=> '',
                    'args'		=> $accordions_view_types,
                );

                $settings_tabs_field->generate_field($args);

                ?>
            </div>


            <ul class="tab-navs">
                <?php
                foreach ($accordion_settings_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                    $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                    ?>
                    <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>"><?php echo $title; ?></li>
                    <?php
                }
                ?>
            </ul>
            <?php
            foreach ($accordion_settings_tab as $tab){
                $id = $tab['id'];
                $title = $tab['title'];
                $active = $tab['active'];
                ?>

                <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                    <?php
                    do_action('accordions_metabox_content_'.$id, $post_id);
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="clear clearfix"></div>

        <?php
	}




	public function _post_meta_accordions_save($post_id){

        /*
         * We need to verify this came from the our screen and with
         * proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['accordions_nonce_check_value']))
            return $post_id;

        $nonce = $_POST['accordions_nonce_check_value'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'accordions_nonce_check'))
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id))
                return $post_id;

        } else {

            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        do_action('accordions_post_meta_save', $post_id);


					
		}
	
	}


new class_accordions_post_meta();