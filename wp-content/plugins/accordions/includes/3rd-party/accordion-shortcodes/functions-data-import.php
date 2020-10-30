<?php
if ( ! defined('ABSPATH')) exit;  // if direct access






add_shortcode('accordions_import_cron_accordion_shortcodes', 'accordions_import_cron_accordion_shortcodes');
add_action('accordions_import_cron_accordion_shortcodes', 'accordions_import_cron_accordion_shortcodes');


function accordions_import_cron_accordion_shortcodes(){

    $accordions_plugin_info = get_option('accordions_plugin_info');
    $meta_query = array();

    $meta_query[] = array(
        'key' => 'import_done',
        'compare' => 'NOT EXISTS'
    );

    $args = array(
        'post_type'=> array( 'page',   ),
        'post_status'=>'publish',
        'posts_per_page'=> 10,
        'meta_query'=> $meta_query,
    );



    $wp_query = new WP_Query($args);


    if ( $wp_query->have_posts() ) :
        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $post_id = get_the_id();
            $post_title = get_the_title();
            $post_content = get_the_content();

            $accordions_options = array();

            echo '<pre>'.var_export($post_title, true).'</pre>';

            $accordions_icons_plus = 'plus';
            $accordions_icons_minus = 'minus';


            $accordions_icons_plus = !empty($accordions_icons_plus) ? '<i class="fa fa-'.$accordions_icons_plus.'"></i>' : '<i class="fa fa-plus"></i>';
            $accordions_icons_minus = !empty($accordions_icons_minus) ? '<i class="fa fa-'.$accordions_icons_minus.'"></i>' : '<i class="fa fa-minus"></i>';

            $accordions_options['icon']['active'] = $accordions_icons_plus;
            $accordions_options['icon']['inactive'] = $accordions_icons_minus;
            $accordions_options['icon']['position'] = '';
            $accordions_options['icon']['color'] = '';
            $accordions_options['icon']['color_hover'] = '';
            $accordions_options['icon']['font_size'] = '';
            $accordions_options['icon']['background_color'] = '';
            $accordions_options['icon']['padding'] = '';




            $accordions_options['header']['class'] = '';
            $accordions_options['header']['active_background_color'] = '';
            $accordions_options['header']['background_color'] = '';
            $accordions_options['header']['background_opacity'] = '';
            $accordions_options['header']['color'] = '';
            $accordions_options['header']['color_hover'] = '';
            $accordions_options['header']['font_size'] = '';
            $accordions_options['header']['font_family'] = '';
            $accordions_options['header']['padding'] = '';
            $accordions_options['header']['margin'] = '';


            $accordions_options['body']['class'] = '';

            $accordions_options['body']['active_background_color'] = '';
            $accordions_options['body']['background_color'] = '';
            $accordions_options['body']['background_opacity'] = '';
            $accordions_options['body']['color'] = '';
            $accordions_options['body']['font_size'] = '';
            $accordions_options['body']['font_family'] = '';
            $accordions_options['body']['padding'] = '';
            $accordions_options['body']['margin'] = '';


            $accordions_options['lazy_load'] = '';
            $accordions_options['lazy_load_src'] = '';
            $accordions_options['view_type'] = 'accordion';

            $accordions_options['hide_edit'] = '';
            $accordions_options['accordion']['collapsible'] =  'true';
            $accordions_options['accordion']['expanded_other'] = '';
            $accordions_options['accordion']['height_style'] = 'content';
            $accordions_options['accordion']['active_event'] = 'click';
            $accordions_options['accordion']['enable_search'] = '';
            $accordions_options['accordion']['search_placeholder_text'] = '';
            $accordions_options['accordion']['click_scroll_top'] = '';
            $accordions_options['accordion']['click_scroll_top_offset'] = '';
            $accordions_options['accordion']['header_toggle'] = '';
            $accordions_options['accordion']['animate_style'] = '';
            $accordions_options['accordion']['animate_delay'] = '';
            $accordions_options['accordion']['expand_collapse_display'] = '';
            $accordions_options['accordion']['expand_collapse_bg_color'] = '';
            $accordions_options['accordion']['expand_collapse_text'] = '';
            $accordions_options['accordion']['is_child'] = '';


            //echo '<pre>'.var_export($post_content, true).'</pre>';

            if( strpos($post_content, '[accordion') !== false){
                $tabs = accordions_str_between_all($post_content, "[accordion", "[/accordion]");

                if(!empty($tabs))
                foreach ($tabs as $tab_content){

                    $shortcode_content = accordions_nested_shortcode_content($tab_content, $child_tag='accordion-item');

                    $i = 0;

                    if(!empty($shortcode_content))
                        foreach ($shortcode_content as $index => $accordion_single_data){

                            $acc_title = isset($accordion_single_data['title']) ? $accordion_single_data['title'] : '';
                            $acc_content = isset($accordion_single_data['content']) ? $accordion_single_data['content'] : '';

                            $accordions_options['content'][$index]['header'] = $acc_title;
                            $accordions_options['content'][$index]['body'] = $acc_content;
                            $accordions_options['content'][$index]['hide'] = 'no';
                            $accordions_options['content'][$index]['toggled_text'] = '';
                            $accordions_options['content'][$index]['is_active'] = '';

                            $active_icon =  '';
                            $inactive_icon =  '';
                            $accordions_options['content'][$index]['active_icon'] = $active_icon;
                            $accordions_options['content'][$index]['inactive_icon'] = $inactive_icon;
                            $accordions_options['content'][$index]['background_color'] =  '';
                            $accordions_options['content'][$index]['background_img'] =  '';

                            $i++;
                        }

                    $accordions_id = wp_insert_post(
                        array(
                            'post_title'    => 'Accordion Shortcodes',
                            'post_content'  => '',
                            'post_status'   => 'publish',
                            'post_type'   	=> 'accordions',
                            'post_author'   => 1,
                        )
                    );

                    update_post_meta($accordions_id, 'accordions_options', $accordions_options);


                }
            }



            update_post_meta($post_id, 'import_done', 'done');


            wp_reset_query();
            wp_reset_postdata();
        endwhile;
    else:

        $accordions_plugin_info['3rd_party_import'] = 'done';
        update_option('accordions_plugin_info', $accordions_plugin_info);

        wp_clear_scheduled_hook('accordions_import_cron_accordion_shortcodes');


    endif;


}


		
		