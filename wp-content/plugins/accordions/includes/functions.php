<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

function accordions_all_user_roles() {

    $wp_roles = new WP_Roles();

    //var_dump($wp_roles);
    $roles = $wp_roles->get_names();

    return  $roles;
    // Below code will print the all list of roles.
    //echo '<pre>'.var_export($wp_roles, true).'</pre>';

}


function accordions_old_content($post_id){

    $accordions_content_title = get_post_meta( $post_id, 'accordions_content_title', true );
    $accordions_content_title_toggled = get_post_meta( $post_id, 'accordions_content_title_toggled', true );
    $accordions_content_body = get_post_meta( $post_id, 'accordions_content_body', true );
    $accordions_hide = get_post_meta( $post_id, 'accordions_hide', true );
    $accordions_section_icon_plus = get_post_meta( $post_id, 'accordions_section_icon_plus', true );
    $accordions_section_icon_minus = get_post_meta( $post_id, 'accordions_section_icon_minus', true );
    $accordions_active_accordion = get_post_meta( $post_id, 'accordions_active_accordion', true );

    $accordions_data = array();

    $i = 0;

    if(!empty($accordions_content_title))
    foreach ($accordions_content_title as $index => $item){


        $accordions_data[$index]['header'] = $item;
        $accordions_data[$index]['body'] = isset($accordions_content_body[$index]) ? $accordions_content_body[$index] : '';

        $is_active = ($accordions_active_accordion == $i) ? array($i) : array();
        $accordions_data[$index]['is_active'] = $is_active;
        $accordions_data[$index]['toggled_text'] = isset($accordions_content_title_toggled[$index]) ? $accordions_content_title_toggled[$index] : '';

        $active_icon = !empty($accordions_section_icon_plus[$index]) ? '<i class="fa '.$accordions_section_icon_plus[$index].'"></i>' : '';
        $inactive_icon = !empty($accordions_section_icon_minus[$index]) ? '<i class="fa '.$accordions_section_icon_minus[$index].'"></i>' : '';



        $accordions_data[$index]['active_icon'] = $active_icon;
        $accordions_data[$index]['inactive_icon'] = $inactive_icon;
        $accordions_data[$index]['hide'] = !empty($accordions_hide[$index]) ? 'yes' : 'no';


        $i++;
    }

    return $accordions_data;

}



function accordions_old_options($accordions_id){


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


    return $accordions_options;

}






//add_filter('the_content','accordions_get_shortcode');
function accordions_get_shortcode($content){


    if(strpos($content, '[restabs')){
        $tabs = accordions_str_between_all($content, "[restabs", "[/restabs]");

        foreach ($tabs as $tab_content){

            $shortcode_content = accordions_nested_shortcode_content($tab_content, $child_tag='restab');
            echo '<pre>'.var_export('#####', true).'</pre>';
            echo '<pre>'.var_export($shortcode_content, true).'</pre>';
        }
    }

    return $content;
}




function accordions_str_between_all($string, $start, $end, $includeDelimiters = false,  &$offset = 0){
    $strings = [];
    $length = strlen($string);

    while ($offset < $length)
    {
        $found = accordions_str_between($string, $start, $end, $includeDelimiters, $offset);
        if ($found === null) break;

        $strings[] = $found;
        $offset += strlen($includeDelimiters ? $found : $start . $found . $end); // move offset to the end of the newfound string
    }

    return $strings;
}

function accordions_str_between($string, $start, $end, $includeDelimiters = false, &$offset = 0){
    if ($string === '' || $start === '' || $end === '') return null;

    $startLength = strlen($start);
    $endLength = strlen($end);

    $startPos = strpos($string, $start, $offset);
    if ($startPos === false) return null;

    $endPos = strpos($string, $end, $startPos + $startLength);
    if ($endPos === false) return null;

    $length = $endPos - $startPos + ($includeDelimiters ? $endLength : -$startLength);
    if (!$length) return '';

    $offset = $startPos + ($includeDelimiters ? 0 : $startLength);

    $result = substr($string, $offset, $length);

    return ($result !== false ? $result : null);
}









function accordions_nested_shortcode_content($string, $child_tag='restab'){

    $accordion_content = array();

    //echo '<pre>'.var_export($tabs, true).'</pre>';


    $tabs = explode('['.$child_tag, $string);
    unset($tabs[0]);

    $i = 0;
    foreach ($tabs as $tab){
        $tab = str_replace('[/'.$child_tag.']','', $tab);
        $tab = str_replace(' active="active"','', $tab);

        $title_content = explode(']', $tab);
        $title = isset($title_content[0]) ? $title_content[0] : '';

        preg_match('/title="(.*?)"/', $title, $output_array);

        $title = $output_array[1];

        //$title = str_replace('title="','', $title);
        //$title = str_replace('"','', $title);
        $acc_title = ltrim($title);

        $acc_content = isset($title_content[1]) ? $title_content[1] : '';

        $accordion_content[$i]['title'] = $acc_title;
        $accordion_content[$i]['content'] = $acc_content;

        $i++;
    }

    //echo '<pre>'.var_export($accordion_content, true).'</pre>';




    return $accordion_content;
}


add_filter('the_content','accordions_preview_content');
function accordions_preview_content($content){
    if(is_singular('accordions')){
        $post_id = get_the_id();
        $content .= do_shortcode('[accordions id="'.$post_id.'"]');
    }

    return $content;
}


function accordions_ajax_import_json(){

	$response = array();


    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if(wp_verify_nonce( $nonce, 'accordions_nonce' )) {

        if(current_user_can( 'manage_options' )){

            $json_file = isset($_POST['json_file']) ? $_POST['json_file'] : '';
            $string = file_get_contents($json_file);
            $json_a = json_decode($string,true);


            foreach ($json_a as $post_id=>$post_data){

                $meta_fields = $post_data['meta_fields'];
                $title = $post_data['title'];

                // Create post object
                $my_post = array(
                    'post_title'    => $title,
                    'post_type' => 'accordions',
                    'post_status'   => 'publish',

                );

                $post_inserted_id = wp_insert_post( $my_post );

                foreach ($meta_fields as $meta_key=>$meta_value){
                    update_post_meta( $post_inserted_id, $meta_key, $meta_value );
                }
            }


            //$response['json_a'] = $json_a;
            $response['message'] = __('Impor done','');
        }

    }else{
        $response['message'] = __('You do not have permission','');
    }




	echo json_encode( $response );



	die();
}
add_action('wp_ajax_accordions_ajax_import_json', 'accordions_ajax_import_json');
//add_action('wp_ajax_nopriv_accordions_ajax_import_json', 'accordions_ajax_import_json');







add_shortcode('accordions_youtube', 'accordions_youtube');


function accordions_youtube($atts, $content = null ){

		$atts = shortcode_atts(
			array(
				'video_id' => "",
				'width' => "560",	
				'height' => "315",										

				), $atts);
		
		$video_id = $atts['video_id'];
		$width = $atts['width'];			
		$height = $atts['height'];			
		
		$html = '';
		$html.= '<iframe width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';

		return $html;	
	}











function accordions_add_shortcode_column( $columns ) {
    return array_merge( $columns, 
        array( 'shortcode' => __( 'Shortcode', 'accordions' ) ) );
}
add_filter( 'manage_accordions_posts_columns' , 'accordions_add_shortcode_column' );


function accordions_posts_shortcode_display( $column, $post_id ) {
    if ($column == 'shortcode'){
		?>
        <input style="background:#bfefff" type="text" onClick="this.select();" value="[accordions <?php echo 'id=&quot;'.$post_id.'&quot;';?>]" /><br />
      <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[accordions id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea>
        <?php		
		
    }
}

add_action( 'manage_accordions_posts_custom_column' , 'accordions_posts_shortcode_display', 10, 2 );






function accordions_paratheme_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = $r.','.$g.','.$b;
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}









