<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



add_shortcode('accordions_cron_upgrade_settings', 'accordions_cron_upgrade_settings');
add_action('accordions_cron_upgrade_settings', 'accordions_cron_upgrade_settings');

function accordions_cron_upgrade_settings(){

    $accordions_settings = get_option( 'accordions_settings', array() );

    $accordions_track_product_view = get_option( 'accordions_track_product_view' );
    $accordions_settings['track_product_view'] = $accordions_track_product_view;

    $accordions_license = get_option( 'accordions_license' );
    $license_key = isset($accordions_license['license_key']) ? $accordions_license['license_key'] : '';
    $accordions_settings['license_key'] = $license_key;

    $fontawesome_ver = get_option( 'accordions_fontawesome_ver' );

    if($fontawesome_ver== 'version-5'){
        $fontawesome_ver = 'v_5';
    }
    elseif($fontawesome_ver== 'version-4'){
        $fontawesome_ver = 'v_4';
    }else{
        $fontawesome_ver = 'none';
    }

    $accordions_settings['font_aw_version'] = $fontawesome_ver;


    update_option('accordions_settings', $accordions_settings);

    wp_clear_scheduled_hook('accordions_cron_upgrade_settings');
    wp_schedule_event(time(), '1minute', 'accordions_cron_upgrade_accordions');

    $accordions_plugin_info = get_option('accordions_plugin_info');
    $accordions_plugin_info['settings_upgrade'] = 'done';

    update_option('accordions_plugin_info', $accordions_plugin_info);

}





add_shortcode('accordions_cron_upgrade_accordions', 'accordions_cron_upgrade_accordions');
add_action('accordions_cron_upgrade_accordions', 'accordions_cron_upgrade_accordions');


function accordions_cron_upgrade_accordions(){

    $meta_query = array();

        $meta_query[] = array(
        'key' => 'accordions_upgrade_status',
        'compare' => 'NOT EXISTS'
    );

    $args = array(
        'post_type'=>'accordions',
        'post_status'=>'any',
        'posts_per_page'=> 10,
        'meta_query'=> $meta_query,

    );


    $accordions_fontawesome_ver = get_option('accordions_fontawesome_ver');

    $wp_query = new WP_Query($args);


    if ( $wp_query->have_posts() ) :
        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $accordions_id = get_the_id();
            $accordions_title = get_the_title();
            $accordions_options = array();

            $accordions_options_is_saved = get_post_meta( $accordions_id, 'accordions_options', true );

            //echo $accordions_title.'<br/>';

            $accordions_lazy_load       = get_post_meta( $accordions_id, 'accordions_lazy_load', true );
            $accordions_options['lazy_load'] = $accordions_lazy_load;

            $accordions_lazy_load_src   = get_post_meta( $accordions_id, 'accordions_lazy_load_src', true );
            $accordions_options['lazy_load_src'] = $accordions_lazy_load_src;

            $accordions_hide_edit       = get_post_meta( $accordions_id, 'accordions_hide_edit', true );
            $accordions_options['hide_edit'] = $accordions_hide_edit;

            $accordions_collapsible     = get_post_meta( $accordions_id, 'accordions_collapsible', true );
            $accordions_options['accordion']['collapsible'] = $accordions_collapsible;

            $accordions_expaned_other   = get_post_meta( $accordions_id, 'accordions_expaned_other', true );
            $accordions_options['accordion']['expanded_other'] = $accordions_expaned_other;

            $accordions_heightStyle     = get_post_meta( $accordions_id, 'accordions_heightStyle', true );
            $accordions_options['accordion']['height_style'] = $accordions_heightStyle;

            $accordions_active_event    = get_post_meta( $accordions_id, 'accordions_active_event', true );
            $accordions_options['accordion']['active_event'] = $accordions_active_event;

            $enable_search              = get_post_meta( $accordions_id, 'enable_search', true );
            $accordions_options['accordion']['enable_search'] = $enable_search;

            $search_placeholder_text              = get_post_meta( $accordions_id, 'search_placeholder_text', true );
            $accordions_options['accordion']['search_placeholder_text'] = $search_placeholder_text;

            $accordions_click_scroll_top = get_post_meta( $accordions_id, 'accordions_click_scroll_top', true );
            $accordions_options['accordion']['click_scroll_top'] = $accordions_click_scroll_top;

            $accordions_click_scroll_top_offset = get_post_meta( $accordions_id, 'accordions_click_scroll_top_offset', true );
            $accordions_options['accordion']['click_scroll_top_offset'] = $accordions_click_scroll_top_offset;

            $accordions_header_toggle   = get_post_meta( $accordions_id, 'accordions_header_toggle', true );
            $accordions_options['accordion']['header_toggle'] = $accordions_header_toggle;

            $accordions_animate_style   = get_post_meta( $accordions_id, 'accordions_animate_style', true );
            $accordions_options['accordion']['animate_style'] = $accordions_animate_style;

            $accordions_animate_delay   = get_post_meta( $accordions_id, 'accordions_animate_delay', true );
            $accordions_options['accordion']['animate_delay'] = $accordions_animate_delay;

            $accordions_expand_collapse_display = get_post_meta( $accordions_id, 'accordions_expand_collapse_display', true );
            $accordions_options['accordion']['expand_collapse_display'] = $accordions_expand_collapse_display;

            $expand_collapse_bg_color = get_post_meta( $accordions_id, 'expand_collapse_bg_color', true );
            $accordions_options['accordion']['expand_collapse_bg_color'] = $expand_collapse_bg_color;

            $expand_collapse_text = get_post_meta( $accordions_id, 'expand_collapse_text', true );
            $accordions_options['accordion']['expand_collapse_text'] = $expand_collapse_text;

            $accordions_child           = get_post_meta( $accordions_id, 'accordions_child', true );
            $accordions_options['accordion']['is_child'] = $accordions_child;


            $accordions_click_track = get_post_meta($accordions_id,'accordions_click_track', true);
            $accordions_options['enable_stats'] = $accordions_click_track;


            $accordions_tabs_collapsible = get_post_meta( $accordions_id, 'accordions_tabs_collapsible', true );
            $accordions_options['tabs']['collapsible'] = $accordions_tabs_collapsible;

            $accordions_tabs_active_event = get_post_meta( $accordions_id, 'accordions_tabs_active_event', true );
            $accordions_options['tabs']['active_event'] = $accordions_tabs_active_event;

            $accordions_tabs_vertical   = get_post_meta( $accordions_id, 'accordions_tabs_vertical', true );
            $accordions_options['tabs']['tabs_vertical'] = $accordions_tabs_vertical;

            $accordions_tabs_vertical_width_ratio = get_post_meta( $accordions_id, 'accordions_tabs_vertical_width_ratio', true );
            $accordions_options['tabs']['navs_width_ratio'] = $accordions_tabs_vertical_width_ratio;

            $accordions_tabs_icon_toggle = get_post_meta( $accordions_id, 'accordions_tabs_icon_toggle', true );
            $accordions_options['tabs']['tabs_icon_toggle'] = $accordions_tabs_icon_toggle;

            $accordions_icons_plus = get_post_meta( $accordions_id, 'accordions_icons_plus', true );
            $accordions_icons_minus = get_post_meta( $accordions_id, 'accordions_icons_minus', true );

            $accordions_icons_plus = !empty($accordions_icons_plus) ? '<i class="fa '.$accordions_icons_plus.'"></i>' : '';
            $accordions_icons_minus = !empty($accordions_icons_minus) ? '<i class="fa '.$accordions_icons_minus.'"></i>' : '';

            $accordions_options['icon']['active'] = $accordions_icons_plus;
            $accordions_options['icon']['inactive'] = $accordions_icons_minus;

            $accordions_icons_position = get_post_meta( $accordions_id, 'accordions_icons_position', true );
            $accordions_options['icon']['position'] = $accordions_icons_position;

            $accordions_icons_color = get_post_meta( $accordions_id, 'accordions_icons_color', true );
            $accordions_options['icon']['color'] = $accordions_icons_color;

            $accordions_icons_color_hover = get_post_meta( $accordions_id, 'accordions_icons_color_hover', true );
            $accordions_options['icon']['color_hover'] = $accordions_icons_color_hover;

            $accordions_icons_font_size = get_post_meta( $accordions_id, 'accordions_icons_font_size', true );
            $accordions_options['icon']['font_size'] = $accordions_icons_font_size;

            $accordions_icons_bg_color = get_post_meta( $accordions_id, 'accordions_icons_bg_color', true );
            $accordions_options['icon']['background_color'] = $accordions_icons_bg_color;

            $accordions_icons_padding = get_post_meta( $accordions_id, 'accordions_icons_padding', true );
            $accordions_options['icon']['padding'] = $accordions_icons_padding;

            $accordions_themes = get_post_meta( $accordions_id, 'accordions_themes', true );
            $accordions_options['accordion']['theme'] = $accordions_themes;

            $header_class = 'border-none';

            if($accordions_themes == 'flat'){
                $header_class = 'border-none';
            }elseif($accordions_themes == 'rounded'){
                $header_class = 'border-round';
            }elseif($accordions_themes == 'semi-rounded'){
                $header_class = 'border-semi-round';
            }elseif($accordions_themes == 'rounded-top'){
                $header_class = 'border-top-round';
            }elseif($accordions_themes == 'shadow'){
                $header_class = 'shadow-bottom';
            }



            $accordions_options['header']['class'] = $header_class;

            $accordions_active_bg_color = get_post_meta( $accordions_id, 'accordions_active_bg_color', true );
            $accordions_options['header']['active_background_color'] = $accordions_active_bg_color;

            $accordions_default_bg_color = get_post_meta( $accordions_id, 'accordions_default_bg_color', true );
            $accordions_options['header']['background_color'] = $accordions_default_bg_color;

            $accordions_header_bg_opacity = get_post_meta( $accordions_id, 'accordions_header_bg_opacity', true );
            $accordions_options['header']['background_opacity'] = $accordions_header_bg_opacity;

            $accordions_items_title_color = get_post_meta( $accordions_id, 'accordions_items_title_color', true );
            $accordions_options['header']['color'] = $accordions_items_title_color;

            $accordions_items_title_color_hover = get_post_meta( $accordions_id, 'accordions_items_title_color_hover', true );
            $accordions_options['header']['color_hover'] = $accordions_items_title_color_hover;

            $accordions_items_title_font_size = get_post_meta( $accordions_id, 'accordions_items_title_font_size', true );
            $accordions_options['header']['font_size'] = $accordions_items_title_font_size;

            $accordions_items_title_font_family = get_post_meta( $accordions_id, 'accordions_items_title_font_family', true );
            $accordions_options['header']['font_family'] = $accordions_items_title_font_family;

            $accordions_items_title_padding = get_post_meta( $accordions_id, 'accordions_items_title_padding', true );
            $accordions_options['header']['padding'] = $accordions_items_title_padding;

            $accordions_items_title_margin = get_post_meta( $accordions_id, 'accordions_items_title_margin', true );
            $accordions_options['header']['margin'] = $accordions_items_title_margin;

            $body_class = '';
            $accordions_options['body']['class'] = $body_class;

            $accordions_active_bg_color = get_post_meta( $accordions_id, 'accordions_active_bg_color', true );
            $accordions_options['body']['active_background_color'] = $accordions_active_bg_color;

            $accordions_items_content_bg_color = get_post_meta( $accordions_id, 'accordions_items_content_bg_color', true );
            $accordions_options['body']['background_color'] = $accordions_items_content_bg_color;

            $accordions_items_content_bg_opacity = get_post_meta( $accordions_id, 'accordions_items_content_bg_opacity', true );
            $accordions_options['body']['background_opacity'] = $accordions_items_content_bg_opacity;

            $accordions_items_content_color = get_post_meta( $accordions_id, 'accordions_items_content_color', true );
            $accordions_options['body']['color'] = $accordions_items_content_color;



            $accordions_items_content_font_size = get_post_meta( $accordions_id, 'accordions_items_content_font_size', true );
            $accordions_options['body']['font_size'] = $accordions_items_content_font_size;

            $accordions_items_content_font_family = get_post_meta( $accordions_id, 'accordions_items_content_font_family', true );
            $accordions_options['body']['font_family'] = $accordions_items_content_font_family;


            $accordions_items_content_padding = get_post_meta( $accordions_id, 'accordions_items_content_padding', true );
            $accordions_options['body']['padding'] = $accordions_items_content_padding;

            $accordions_items_content_margin = get_post_meta( $accordions_id, 'accordions_items_content_margin', true );
            $accordions_options['body']['margin'] = $accordions_items_content_margin;


            //Container options
            $accordions_container_padding = get_post_meta( $accordions_id, 'accordions_container_padding', true );
            $accordions_options['container']['padding'] = $accordions_container_padding;

            $accordions_container_bg_color = get_post_meta( $accordions_id, 'accordions_container_bg_color', true );
            $accordions_options['container']['background_color'] = $accordions_container_bg_color;

            $accordions_items_content_bg_opacity = get_post_meta( $accordions_id, 'accordions_items_content_bg_opacity', true );
            $accordions_options['container']['background_opacity'] = $accordions_items_content_bg_opacity;

            $accordions_bg_img = get_post_meta( $accordions_id, 'accordions_bg_img', true );
            $accordions_options['container']['background_img'] = $accordions_bg_img;

            $accordions_container_text_align = get_post_meta( $accordions_id, 'accordions_container_text_align', true );
            $accordions_options['container']['text_align'] = $accordions_container_text_align;

            $accordions_width           = get_post_meta( $accordions_id, 'accordions_width', true );
            $accordions_width_large     = !empty($accordions_width['large']) ? $accordions_width['large'] : '100%';
            $accordions_width_medium    = !empty($accordions_width['medium']) ? $accordions_width['medium'] : '100%';
            $accordions_width_small     = !empty($accordions_width['small']) ? $accordions_width['small'] : '100%';

            $accordions_options['container']['width_large'] = $accordions_width_large;
            $accordions_options['container']['width_medium'] = $accordions_width_medium;
            $accordions_options['container']['width_small'] = $accordions_width_small;


            // Custom Scripts
            $accordions_custom_css = get_post_meta($accordions_id,'accordions_custom_css', true);
            $accordions_options['custom_scripts']['custom_css'] = $accordions_custom_css;

            $accordions_custom_js = get_post_meta($accordions_id,'accordions_custom_js', true);
            $accordions_options['custom_scripts']['custom_js'] = $accordions_custom_js;


            $track_header = get_post_meta($accordions_id, 'track_header', true);
            $accordions_options['track_header'] = $track_header;

            $accordions_content_title = get_post_meta($accordions_id,'accordions_content_title', true);
            $accordions_content_body = get_post_meta($accordions_id,'accordions_content_body', true);
            $accordions_content_title_toggled = get_post_meta($accordions_id,'accordions_content_title_toggled', true);
            $accordions_section_icon_plus = get_post_meta($accordions_id,'accordions_section_icon_plus', true);
            $accordions_section_icon_minus = get_post_meta($accordions_id,'accordions_section_icon_minus', true);
            $accordions_hide = get_post_meta($accordions_id,'accordions_hide', true);
            $accordions_bg_color = get_post_meta($accordions_id,'accordions_bg_color', true);
            $accordions_header_bg_img = get_post_meta($accordions_id,'accordions_header_bg_img', true);

            $accordions_active_accordion = get_post_meta($accordions_id,'accordions_active_accordion', true);

            $i = 0;

            if(!empty($accordions_content_title))
            foreach ($accordions_content_title as $index => $title){

                $accordions_options['content'][$index]['header'] = $title;
                $accordions_options['content'][$index]['body'] = isset($accordions_content_body[$index]) ? $accordions_content_body[$index] : '';
                $accordions_options['content'][$index]['hide'] = isset($accordions_hide[$index]) ? $accordions_hide[$index] : '';
                $accordions_options['content'][$index]['toggled_text'] = isset($accordions_content_title_toggled[$index]) ? $accordions_content_title_toggled[$index] : '';

                $accordions_options['content'][$index]['is_active'] = ($accordions_active_accordion == $i) ? 'yes' : 'no';


                $active_icon = !empty($accordions_section_icon_plus[$index]) ? '<i class="fa '.$accordions_section_icon_plus[$index].'"></i>' : '';
                $inactive_icon = !empty($accordions_section_icon_minus[$index]) ? '<i class="fa '.$accordions_section_icon_minus[$index].'"></i>' : '';

                $accordions_options['content'][$index]['active_icon'] = $active_icon;
                $accordions_options['content'][$index]['inactive_icon'] = $inactive_icon;

                $accordions_options['content'][$index]['background_color'] = isset($accordions_bg_color[$index]) ? $accordions_bg_color[$index] : '';
                $accordions_options['content'][$index]['background_img'] = isset($accordions_header_bg_img[$index]) ? $accordions_header_bg_img[$index] : '';

                $i++;
            }




            if(empty($accordions_options_is_saved)){
                update_post_meta($accordions_id, 'accordions_options', $accordions_options);
            }


            update_post_meta($accordions_id, 'accordions_upgrade_status', 'done');



            wp_reset_query();
            wp_reset_postdata();
        endwhile;
    else:

        $accordions_plugin_info = get_option('accordions_plugin_info');
        $accordions_plugin_info['accordions_upgrade'] = 'done';
        update_option('accordions_plugin_info', $accordions_plugin_info);

        wp_clear_scheduled_hook('accordions_cron_upgrade_accordions');


    endif;


}

add_shortcode('accordions_cron_reset_migrate', 'accordions_cron_reset_migrate');

add_action('accordions_cron_reset_migrate','accordions_cron_reset_migrate');

function accordions_cron_reset_migrate(){

    $accordions_plugin_info = get_option('accordions_plugin_info');

    delete_option('accordions_settings');




    $accordions_meta_query[] = array(
        'key' => 'accordions_upgrade_status',
        'compare' => '='
    );

    $accordions_args = array(
        'post_type' => 'accordions',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'meta_query' => $accordions_meta_query,
    );

    $accordions_query = new WP_Query($accordions_args);

    if ($accordions_query->have_posts()) :
        while ($accordions_query->have_posts()) : $accordions_query->the_post();
            $post_id = get_the_id();
            delete_post_meta($post_id, 'accordions_upgrade_status');
            delete_post_meta($post_id, 'accordions_options');

        endwhile;
        wp_reset_postdata();
        wp_reset_query();
    endif;




    $accordions_plugin_info['settings_upgrade'] = '';
    $accordions_plugin_info['accordions_upgrade'] = '';
    $accordions_plugin_info['migration_reset'] = 'done';
    update_option('accordions_plugin_info', $accordions_plugin_info);

    wp_clear_scheduled_hook('accordions_cron_reset_migrate');

}
		
		
		

		
		