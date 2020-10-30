<?php
if ( ! defined('ABSPATH')) exit;  // if direct access






add_shortcode('accordions_import_cron_ultimate_faqs', 'accordions_import_cron_ultimate_faqs');
add_action('accordions_import_cron_ultimate_faqs', 'accordions_import_cron_ultimate_faqs');


function accordions_import_cron_ultimate_faqs(){
    $accordions_plugin_info = get_option('accordions_plugin_info');

    $meta_query = array();

    $meta_query[] = array(
        'key' => 'import_done',
        'compare' => 'NOT EXISTS'
    );

    $args = array(
        'post_type'=>'ufaq',
        'post_status'=>'publish',
        'posts_per_page'=> -1,
        'meta_query'=> $meta_query,
    );


    $wp_query = new WP_Query($args);


    if ( $wp_query->have_posts() ) :

        $accordions_options = array();


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





        $accordions_options['lazy_load'] = !empty($eap_preloader) ? 'yes' : 'no';
        $accordions_options['lazy_load_src'] = '';
        $accordions_options['hide_edit'] = '';
        $accordions_options['accordion']['collapsible'] =  'true';
        $accordions_options['accordion']['expanded_other'] = !empty($eap_mutliple_collapse) ? 'yes' : 'no';
        $accordions_options['accordion']['height_style'] = !empty($eap_accordion_fillspace) ? 'content' : '';


        $accordions_options['accordion']['active_event'] = '';
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








        $index = 0;
        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $post_id = get_the_id();
            $post_title = get_the_title();
            $post_content = get_the_content();


            $accordions_options['content'][$index]['header'] = $post_title;
            $accordions_options['content'][$index]['body'] = $post_content;
            $accordions_options['content'][$index]['hide'] = 'no';
            $accordions_options['content'][$index]['toggled_text'] = '';


            $accordions_options['content'][$index]['is_active'] = '';


            $accordions_options['content'][$index]['active_icon'] = '';
            $accordions_options['content'][$index]['inactive_icon'] = '';

            $accordions_options['content'][$index]['background_color'] =  '';
            $accordions_options['content'][$index]['background_img'] =  '';

            update_post_meta($post_id, 'import_done', 'done');

            $index++;
        endwhile;

        //echo '<pre>'.var_export($accordions_options, true).'</pre>';


        $post_data = array(
            'post_title'    => 'Ultimate FAQ',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'   	=> 'accordions',
            'post_author'   => 1,
        );

        $accordions_id = wp_insert_post($post_data);


        update_post_meta($accordions_id, 'accordions_options', $accordions_options);


//        echo '##################';
//        echo '<br/>';
//        echo 'import done: '.$post_title;
//        echo '<br/>';


        wp_reset_query();
        wp_reset_postdata();

    else:

        $accordions_plugin_info['3rd_party_import'] = 'done';
        update_option('accordions_plugin_info', $accordions_plugin_info);

        wp_clear_scheduled_hook('accordions_import_cron_ultimate_faqs');


    endif;


}


		
		