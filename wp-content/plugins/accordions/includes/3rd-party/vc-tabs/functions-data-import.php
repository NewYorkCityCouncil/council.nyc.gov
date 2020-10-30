<?php
if ( ! defined('ABSPATH')) exit;  // if direct access






add_shortcode('accordions_import_cron_vc_tabs', 'accordions_import_cron_vc_tabs');
add_action('accordions_import_cron_vc_tabs', 'accordions_import_cron_vc_tabs');


function accordions_import_cron_vc_tabs(){
    $accordions_plugin_info = get_option('accordions_plugin_info');

    $meta_query = array();

    $meta_query[] = array(
        'key' => 'import_done',
        'compare' => 'NOT EXISTS'

    );

    $args = array(
        'post_type'=>'responsive_accordion',
        'post_status'=>'publish',
        'posts_per_page'=> 1,
        'meta_query'=> $meta_query,

    );


    $accordions_fontawesome_ver = get_option('accordions_fontawesome_ver');

    $wp_query = new WP_Query($args);


    if ( $wp_query->have_posts() ) :
        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $post_id = get_the_id();
            $post_title = get_the_title();
            $accordions_options = array();

            //echo $accordions_title.'<br/>';
            $wpsm_accordion_data       = get_post_meta( $post_id, 'wpsm_accordion_data', true );
            $wpsm_accordion_data       = unserialize( $wpsm_accordion_data );

            $Accordion_Settings = get_post_meta( $post_id, 'Accordion_Settings', true);
            $Accordion_Settings       = unserialize( $Accordion_Settings );

            $acc_sec_title = isset($Accordion_Settings['acc_sec_title']) ? $Accordion_Settings['acc_sec_title'] : 'yes';
            $op_cl_icon = isset($Accordion_Settings['op_cl_icon']) ? $Accordion_Settings['op_cl_icon'] : 'yes';
            $acc_title_icon = isset($Accordion_Settings['acc_title_icon']) ? $Accordion_Settings['acc_title_icon'] : 'yes';
            $acc_radius = isset($Accordion_Settings['acc_radius']) ? $Accordion_Settings['acc_radius'] : 'yes';
            $acc_margin = isset($Accordion_Settings['acc_margin']) ? $Accordion_Settings['acc_margin'] : 'yes';
            $enable_toggle = isset($Accordion_Settings['enable_toggle']) ? $Accordion_Settings['enable_toggle'] : 'yes';
            $enable_ac_border = isset($Accordion_Settings['enable_ac_border']) ? $Accordion_Settings['enable_ac_border'] : 'yes';
            $acc_op_cl_align = isset($Accordion_Settings['acc_op_cl_align']) ? $Accordion_Settings['acc_op_cl_align'] : 'right';
            $acc_title_bg_clr = isset($Accordion_Settings['acc_title_bg_clr']) ? $Accordion_Settings['acc_title_bg_clr'] : '#e8e8e8';
            $acc_title_icon_clr = isset($Accordion_Settings['acc_title_icon_clr']) ? $Accordion_Settings['acc_title_icon_clr'] : '#000000';
            $acc_desc_bg_clr = isset($Accordion_Settings['acc_desc_bg_clr']) ? $Accordion_Settings['acc_desc_bg_clr'] : '#ffffff';
            $acc_desc_font_clr = isset($Accordion_Settings['acc_desc_font_clr']) ? $Accordion_Settings['acc_desc_font_clr'] : '#000000';
            $title_size = isset($Accordion_Settings['title_size']) ? $Accordion_Settings['title_size'] : '18';
            $des_size = isset($Accordion_Settings['des_size']) ? $Accordion_Settings['des_size'] : '16';
            $font_family = isset($Accordion_Settings['font_family']) ? $Accordion_Settings['font_family'] : 'Open Sans';
            $expand_option = isset($Accordion_Settings['expand_option']) ? $Accordion_Settings['expand_option'] : 1;
            $ac_styles = isset($Accordion_Settings['ac_styles']) ? $Accordion_Settings['ac_styles'] : 1;




            echo '<pre>'.var_export($acc_sec_title, true).'</pre>';

            $accordions_icons_plus = 'plus';
            $accordions_icons_minus = 'minus';


            $accordions_icons_plus = !empty($accordions_icons_plus) ? '<i class="fa fa-'.$accordions_icons_plus.'"></i>' : '<i class="fa fa-plus"></i>';
            $accordions_icons_minus = !empty($accordions_icons_minus) ? '<i class="fa fa-'.$accordions_icons_minus.'"></i>' : '<i class="fa fa-minus"></i>';

            $accordions_options['icon']['active'] = $accordions_icons_plus;
            $accordions_options['icon']['inactive'] = $accordions_icons_minus;
            $accordions_options['icon']['position'] = $acc_op_cl_align;
            $accordions_options['icon']['color'] = $acc_title_icon_clr;
            $accordions_options['icon']['color_hover'] = '';
            $accordions_options['icon']['font_size'] = $title_size.'px';
            $accordions_options['icon']['background_color'] = '';
            $accordions_options['icon']['padding'] = '';




            $accordions_options['header']['class'] = ($acc_radius == 'yes') ? 'border-semi-round' :'';
            $accordions_options['header']['active_background_color'] = '';
            $accordions_options['header']['background_color'] = $acc_title_bg_clr;
            $accordions_options['header']['background_opacity'] = '';
            $accordions_options['header']['color'] = '';
            $accordions_options['header']['color_hover'] = '';
            $accordions_options['header']['font_size'] = $title_size.'px';
            $accordions_options['header']['font_family'] = $font_family;
            $accordions_options['header']['padding'] = '';
            $accordions_options['header']['margin'] = ($acc_margin == 'yes') ? '5px' :'';


            $accordions_options['body']['class'] = ($enable_ac_border == 'yes') ? 'border-2px' :'';

            $accordions_options['body']['active_background_color'] = '';
            $accordions_options['body']['background_color'] = $acc_desc_bg_clr;
            $accordions_options['body']['background_opacity'] = '';
            $accordions_options['body']['color'] = $acc_desc_font_clr;
            $accordions_options['body']['font_size'] = '';
            $accordions_options['body']['font_family'] = $font_family;
            $accordions_options['body']['padding'] = '';
            $accordions_options['body']['margin'] = '';





            $accordions_options['lazy_load'] = '';
            $accordions_options['lazy_load_src'] = '';
            $accordions_options['hide_edit'] = '';
            $accordions_options['accordion']['collapsible'] =  ($enable_toggle == 'yes') ? 'true' :'false';
            $accordions_options['accordion']['expanded_other'] = ($enable_toggle == 'yes') ? 'yes' :'no';
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











            $i = 0;

            if(!empty($wpsm_accordion_data))
                foreach ($wpsm_accordion_data as $index => $accordion_single_data){

                    $accordion_title = $accordion_single_data['accordion_title'];
                    $accordion_title_icon = $accordion_single_data['accordion_title_icon'];
                    $enable_single_icon = $accordion_single_data['enable_single_icon'];
                    $accordion_desc = $accordion_single_data['accordion_desc'];




                    $accordions_options['content'][$index]['header'] = ($acc_title_icon =='yes') ? (($enable_single_icon == 'yes') ? '<i class="fa '.$accordion_title_icon.'"></i> '.$accordion_title : $accordion_title) : $accordion_title;

                    $accordions_options['content'][$index]['body'] = $accordion_desc;
                    $accordions_options['content'][$index]['hide'] = 'no';
                    $accordions_options['content'][$index]['toggled_text'] = '';


                    $accordions_options['content'][$index]['is_active'] = ($expand_option == '1' && $i==0) ? 'yes' :'';


                    $active_icon = !empty($accordions_section_icon_plus[$index]) ? '<i class="fa '.$enable_single_icon.'"></i>' : '';
                    $inactive_icon = !empty($accordions_section_icon_minus[$index]) ? '<i class="fa '.$accordions_section_icon_minus[$index].'"></i>' : '';

                    $accordions_options['content'][$index]['active_icon'] = $active_icon;
                    $accordions_options['content'][$index]['inactive_icon'] = $inactive_icon;

                    $accordions_options['content'][$index]['background_color'] =  '';
                    $accordions_options['content'][$index]['background_img'] =  '';

                    $i++;
                }





            $accordions_id = wp_insert_post(
                array(
                    'post_title'    => $post_title,
                    'post_content'  => '',
                    'post_status'   => 'publish',
                    'post_type'   	=> 'accordions',
                    'post_author'   => 1,
                )
            );












            update_post_meta($accordions_id, 'accordions_options', $accordions_options);
            update_post_meta($post_id, 'import_done', 'done');

            echo '##################';
            echo '<br/>';
            echo 'import done: '.$post_title;
            echo '<br/>';

            wp_reset_query();
            wp_reset_postdata();
        endwhile;
    else:

        $accordions_plugin_info['3rd_party_import'] = 'done';
        update_option('accordions_plugin_info', $accordions_plugin_info);

        wp_clear_scheduled_hook('accordions_import_cron_vc_tabs');


    endif;


}


		
		