<?php

/*
* @Author 		PickPlugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access




add_action('accordions_metabox_content_shortcode', 'accordions_metabox_content_shortcode',10, 2);

function accordions_metabox_content_shortcode($post_id){

    $settings_tabs_field = new settings_tabs_field();


    ?>
    <div class="section">
        <div class="section-title"><?php echo __('Shortcodes','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Simply copy these shortcode and user under content','accordions');?></p>


        <?php


        ob_start();

        ?>

        <div class="copy-to-clipboard">
            <input type="text" value="[accordions id='<?php echo $post_id;  ?>']"> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('You can use this shortcode under post content','accordions'); ?></p>
        </div>

        <div class="copy-to-clipboard">
            <input type="text" value="[accordions_pplugins id='<?php echo $post_id;  ?>']"> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('To avoid conflict with 3rd party shortcode also used same <code>[accordions]</code>You can use this shortcode under post content.','accordions'); ?></p>
        </div>

        <div class="copy-to-clipboard">
            <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[accordions id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('PHP Code, you can use under theme .php files.','accordions'); ?></p>
        </div>

        <div class="copy-to-clipboard">
            <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[accordions_pplugins id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('To avoid conflict, PHP code you can use under theme .php files.','accordions'); ?></p>
        </div>



        <?php

        $html = ob_get_clean();

        $args = array(
            'id'		=> 'accordions_shortcodes',
            'title'		=> __('Accordion shortcode','accordions'),
            'details'	=> '',
            'type'		=> 'custom_html',
            'html'		=> $html,


        );

        $settings_tabs_field->generate_field($args);
        ?>

        <?php


        ob_start();

        ?>

        <div class="copy-to-clipboard">
            <input type="text" value="[accordions_tabs id='<?php echo $post_id;  ?>']"> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('You can use this shortcode under post content','accordions'); ?></p>
        </div>

        <div class="copy-to-clipboard">
            <input type="text" value="[accordions_tabs_pplugins id='<?php echo $post_id;  ?>']"> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('To avoid conflict with 3rd party shortcode also used same <code>[accordions_tabs]</code>You can use this shortcode under post content','accordions'); ?></p>
        </div>

        <div class="copy-to-clipboard">
            <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[accordions_tabs id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('PHP Code, you can use under theme .php files.','accordions'); ?></p>
        </div>

        <div class="copy-to-clipboard">
            <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[accordions_tabs_pplugins id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea> <span class="copied"><?php echo __('Copied','accordions'); ?></span>
            <p class="description"><?php echo __('To avoid conflict, PHP code you can use under theme .php files.','accordions'); ?></p>
        </div>



        <style type="text/css">
            .copy-to-clipboard{}
            .copy-to-clipboard .copied{
                display: none;
                background: #e5e5e5;
                padding: 4px 10px;
                line-height: normal;
            }
        </style>

        <script>
            jQuery(document).ready(function($){


                $(document).on('click', '.copy-to-clipboard input, .copy-to-clipboard textarea', function () {

                    $(this).focus();
                    $(this).select();
                    document.execCommand('copy');

                    $(this).parent().children('.copied').fadeIn().fadeOut(2000);
                })

            })


        </script>




        <?php

        $html = ob_get_clean();

        $args = array(
            'id'		=> 'accordions_shortcodes',
            'title'		=> __('Tabs shortcodes','accordions'),
            'details'	=> '',
            'type'		=> 'custom_html',
            'html'		=> $html,


        );

        $settings_tabs_field->generate_field($args);
        ?>










    </div>
    <?php
}


add_action('accordions_metabox_content_general', 'accordions_metabox_content_general', 10);

function accordions_metabox_content_general($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id, 'accordions_options', true);
    $accordions_options = !empty($accordions_options) ? $accordions_options : accordions_old_options($post_id);


    $lazy_load = isset($accordions_options['lazy_load']) ? $accordions_options['lazy_load'] : 'yes';
    $lazy_load_src = isset($accordions_options['lazy_load_src']) ? $accordions_options['lazy_load_src'] : '';
    $hide_edit = isset($accordions_options['hide_edit']) ? $accordions_options['hide_edit'] : '';
    $enable_autoembed = isset($accordions_options['enable_autoembed']) ? $accordions_options['enable_autoembed'] : '';
    $enable_shortcode = isset($accordions_options['enable_shortcode']) ? $accordions_options['enable_shortcode'] : '';
    $enable_wpautop = isset($accordions_options['enable_wpautop']) ? $accordions_options['enable_wpautop'] : '';
    $enable_schema = isset($accordions_options['enable_schema']) ? $accordions_options['enable_schema'] : '';
    $edit_link_access_role = isset($accordions_options['edit_link_access_role']) ? $accordions_options['edit_link_access_role'] : array();

    //var_dump($lazy_load);

    ?>

    <div class="section">
        <div class="section-title"><?php echo __('General options','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Some general options','accordions'); ?></p>

        <?php
        $args = array(
            'id'		=> 'lazy_load',
            'parent'		=> 'accordions_options',
            'title'		=> __('Enable lazy load','accordions'),
            'details'	=> __('Accordion content will be hidden until page load completed.','accordions'),
            'type'		=> 'select',
            'value'		=> $lazy_load,
            'default'		=> 'yes',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'lazy_load_src',
            'parent'		=> 'accordions_options',
            'title'		=> __('Lazy load image','accordions'),
            'details'	=> __('Set custom image source for lazy load icon.','accordions'),
            'type'		=> 'media_url',
            'value'		=> $lazy_load_src,
            'default'		=> '',
            'placeholder' => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'hide_edit',
            'parent'		=> 'accordions_options',
            'title'		=> __('Hide edit link','accordions'),
            'details'	=> __('You can display/hide accordion edit link on front-end','accordions'),
            'type'		=> 'select',
            'value'		=> $hide_edit,
            'default'		=> 'yes',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'edit_link_access_role',
            'parent'		=> 'accordions_options',
            'title'		=> __('Who can see edit link','accordions'),
            'details'	=> __('Select which user role can access to edit link.','accordions'),
            'type'		=> 'select',
            'multiple'		=> true,
            'value'		=> $edit_link_access_role,
            'default'		=> array('administrator'),
            'args'		=> accordions_all_user_roles(),
        );

        $settings_tabs_field->generate_field($args);




        $args = array(
            'id'		=> 'enable_autoembed',
            'parent'		=> 'accordions_options',
            'title'		=> __('Enable autoembed','accordions'),
            'details'	=> __('Enable autoembed for content.','accordions'),
            'type'		=> 'select',
            'value'		=> $enable_autoembed,
            'default'		=> 'yes',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'enable_shortcode',
            'parent'		=> 'accordions_options',
            'title'		=> __('Enable shortcode','accordions'),
            'details'	=> __('Enable shortcode for content.','accordions'),
            'type'		=> 'select',
            'value'		=> $enable_shortcode,
            'default'		=> 'yes',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'enable_wpautop',
            'parent'		=> 'accordions_options',
            'title'		=> __('Enable wpautop','accordions'),
            'details'	=> __('Enable wpautop for content.','accordions'),
            'type'		=> 'select',
            'value'		=> $enable_wpautop,
            'default'		=> 'yes',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'enable_schema',
            'parent'		=> 'accordions_options',
            'title'		=> __('Enable schema','accordions'),
            'details'	=> __('Enable schema for accordion or tabs.','accordions'),
            'type'		=> 'select',
            'value'		=> $enable_schema,
            'default'		=> 'no',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);



        ?>

    </div>
        <?php

}

add_action('accordions_metabox_content_accordion_options', 'accordions_metabox_content_accordion_options', 10);

function accordions_metabox_content_accordion_options($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordions_options = !empty($accordions_options) ? $accordions_options : accordions_old_options($post_id);


    $accordion = isset($accordions_options['accordion']) ? $accordions_options['accordion'] : array();
    $collapsible = isset($accordion['collapsible']) ? $accordion['collapsible'] : 'true';
    $expanded_other = isset($accordion['expanded_other']) ? $accordion['expanded_other'] : 'no';
    $height_style = isset($accordion['height_style']) ? $accordion['height_style'] : 'content';
    $active_event = isset($accordion['active_event']) ? $accordion['active_event'] : 'click';


    ?>

    <div class="section">
        <div class="section-title"><?php echo __('Accordion options','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Some general setting for accordion','accordions'); ?></p>

        <?php
        $args = array(
            'id'		=> 'collapsible',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Collapsible','accordions'),
            'details'	=> __('Make accordion collapsible.','accordions'),
            'type'		=> 'select',
            'value'		=> $collapsible,
            'default'		=> 'true',
            'args'		=> array(
                'true'	=> __('True','accordions'),
                'false'	=> __('False','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'expanded_other',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Keep expanded others','accordions'),
            'details'	=> __('This is useful when use collapsible.','accordions'),
            'type'		=> 'select',
            'value'		=> $expanded_other,
            'default'		=> 'no',
            'args'		=> array(
                'no'	=> __('No','accordions'),
                'yes'	=> __('Yes','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'height_style',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Content height style','accordions'),
            'details'	=> __('accordion content style.','accordions'),
            'type'		=> 'select',
            'value'		=> $height_style,
            'default'		=> 'content',
            'args'		=> array(

                'content'	=> __('Content','accordions'),
                'fill'	=> __('Fill','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'active_event',
            'parent'		=> 'accordions_options[accordion]',
            'title'		=> __('Activate event','accordions'),
            'details'	=> __('Activate event type for header.','accordions'),
            'type'		=> 'select',
            'value'		=> $active_event,
            'default'		=> 'click',
            'args'		=> array(
                'click'	=> __('Click','accordions'),
                'mouseover'	=> __('Mouseover','accordions'),
                'focus'	=> __('Focus','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);
        ?>

    </div>
    <?php


}


add_action('accordions_metabox_content_tabs_options', 'accordions_metabox_content_tabs_options', 10);

function accordions_metabox_content_tabs_options($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordions_options = !empty($accordions_options) ? $accordions_options : accordions_old_options($post_id);


    $tabs = isset($accordions_options['tabs']) ? $accordions_options['tabs'] : array();
    $collapsible = isset($tabs['collapsible']) ? $tabs['collapsible'] : 'true';
    $active_event = isset($tabs['active_event']) ? $tabs['active_event'] : 'click';
    $navs_alignment = isset($tabs['navs_alignment']) ? $tabs['navs_alignment'] : 'left';


    ?>

    <div class="section">
        <div class="section-title"><?php echo __('Tabs options','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Settings for tabs','accordions'); ?></p>


        <?php
        $args = array(
            'id'		=> 'collapsible',
            'parent'		=> 'accordions_options[tabs]',
            'title'		=> __('Collapsible','accordions'),
            'details'	=> __('Make tabs collapsible.','accordions'),
            'type'		=> 'select',
            'value'		=> $collapsible,
            'default'		=> 'true',
            'args'		=> array(
                'true'	=> __('True','accordions'),
                'false'	=> __('False','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'active_event',
            'parent'		=> 'accordions_options[tabs]',
            'title'		=> __('Activate event','accordions'),
            'details'	=> __('Event for activate tabs','accordions'),
            'type'		=> 'select',
            'value'		=> $active_event,
            'default'		=> 'click',
            'args'		=> array(
                'click'	=> __('Click','accordions'),
                'mouseover'	=> __('Mouseover','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'navs_alignment',
            'parent'		=> 'accordions_options[tabs]',
            'title'		=> __('Navs alignment','accordions'),
            'details'	=> __('Set navs alignment','accordions'),
            'type'		=> 'select',
            'value'		=> $navs_alignment,
            'default'		=> 'left',
            'args'		=> array(
                'left'	=> __('Left','accordions'),
                'right'	=> __('Right','accordions'),
            ),
        );

        $settings_tabs_field->generate_field($args);


        ?>

    </div>
    <?php


}




add_action('accordions_metabox_content_style', 'accordions_metabox_content_style', 10);

function accordions_metabox_content_style($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordions_options = !empty($accordions_options) ? $accordions_options : accordions_old_options($post_id);

    $icon = isset($accordions_options['icon']) ? $accordions_options['icon'] : array();
    $icon_active = isset($icon['active']) ? $icon['active'] : '';
    $icon_inactive = isset($icon['inactive']) ? $icon['inactive'] : '';
    $icon_color = isset($icon['color']) ? $icon['color'] : '';
    $icon_color_hover = isset($icon['color_hover']) ? $icon['color_hover'] : '';
    $icon_font_size = isset($icon['font_size']) ? $icon['font_size'] : '';
    $icon_background_color = isset($icon['background_color']) ? $icon['background_color'] : '';
    $icon_padding = isset($icon['padding']) ? $icon['padding'] : '';
    $icon_margin = isset($icon['margin']) ? $icon['margin'] : '';
    $icon_position = isset($icon['position']) ? $icon['position'] : '';


    $header = isset($accordions_options['header']) ? $accordions_options['header'] : array();
    $header_class = isset($header['class']) ? $header['class'] : '';
    $header_background_color = isset($header['background_color']) ? $header['background_color'] : '';
    $header_active_background_color = isset($header['active_background_color']) ? $header['active_background_color'] : '';
    $header_color = isset($header['color']) ? $header['color'] : '';
    $header_color_hover = isset($header['color_hover']) ? $header['color_hover'] : '';
    $header_font_size = isset($header['font_size']) ? $header['font_size'] : '';
    $header_font_family = isset($header['font_family']) ? $header['font_family'] : '';

    $header_padding = isset($header['padding']) ? $header['padding'] : '';
    $header_margin = isset($header['margin']) ? $header['margin'] : '';

    $body = isset($accordions_options['body']) ? $accordions_options['body'] : array();
    $body_class = isset($body['class']) ? $body['class'] : '';
    $body_background_color = isset($body['background_color']) ? $body['background_color'] : '';
    $body_active_background_color = isset($body['active_background_color']) ? $body['active_background_color'] : '';
    $body_color = isset($body['color']) ? $body['color'] : '';
    $body_color_hover = isset($body['color_hover']) ? $body['color_hover'] : '';
    $body_font_size = isset($body['font_size']) ? $body['font_size'] : '';
    $body_font_family = isset($body['font_family']) ? $body['font_family'] : '';

    $body_padding = isset($body['padding']) ? $body['padding'] : '';
    $body_margin = isset($body['margin']) ? $body['margin'] : '';

    ?>
    <div class="section">
        <div class="section-title"><?php echo __('Accordion icons','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Customize accordion icons.','accordions'); ?></p>

        <?php

        $args = array(
            'id'		=> 'active',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Active icon','accordions'),
            'details'	=> __('Icon for idle, you can use <a target="_blank" href="https://fontawesome.com/icons">Font Awesome</a> icon html <code>&lt;i class="fas fa-chevron-right">&lt;/i></code>','accordions'),
            'type'		=> 'text_icon',
            'value'		=> $icon_active,
            'default'		=> '',
            'placeholder' => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'inactive',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Inactive icon','accordions'),
            'details'	=> __('Icon for activate, you can use <a target="_blank" href="https://fontawesome.com/icons">Font Awesome</a> icon html <code>&lt;i class="fas fa-chevron-down">&lt;/i></code>','accordions'),
            'type'		=> 'text_icon',
            'value'		=> $icon_inactive,
            'default'		=> '',
            'placeholder' => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'color',
            'css_id'		=> 'icon_color',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Color','accordions'),
            'details'	=> __('Color for icons','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $icon_color,
            'default'		=> '',
            'placeholder' => '#999999',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'color_hover',
            'css_id'		=> 'icon_color_hover',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Hover color','accordions'),
            'details'	=> __('Color for icons on mousehover','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $icon_color_hover,
            'default'		=> '',
            'placeholder' => '#777777',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'background_color',
            'css_id'		=> 'icon_background_color',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Background color','accordions'),
            'details'	=> __('Background color for icons','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $icon_background_color,
            'default'		=> '',
            'placeholder' => '#777777',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'font_size',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Font size','accordions'),
            'details'	=> __('You can set custom font size.','accordions'),
            'type'		=> 'text',
            'value'		=> $icon_font_size,
            'default'		=> '',
            'placeholder' => '14px',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'padding',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Padding','accordions'),
            'details'	=> __('Choose icon area padding','accordions'),
            'type'		=> 'text',
            'value'		=> $icon_padding,
            'default'		=> '',
            'placeholder' => '10px',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'margin',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Margin','accordions'),
            'details'	=> __('Choose header area margin','accordions'),
            'type'		=> 'text',
            'value'		=> $icon_margin,
            'default'		=> '',
            'placeholder' => '5px',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'position',
            'parent'		=> 'accordions_options[icon]',
            'title'		=> __('Position','accordions'),
            'details'	=> __('Set icon position','accordions'),
            'type'		=> 'select',
            'value'		=> $icon_position,
            'default'		=> 'left',
            'args'		=> array(
                'none'	=> __('None','accordions'),
                'left'	=> __('Left','accordions'),
                'right'	=> __('Right','accordions'),
            ),

        );

        $settings_tabs_field->generate_field($args);

        ?>
    </div>


    <div class="section">
        <div class="section-title"><?php echo __('Accordion header style','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Customize accordion header.','accordions'); ?></p>
        <?php

        $args = array(
            'id'		=> 'class',
            'css_id'		=> 'header_background_color',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Add class','accordions'),
            'details'	=> __('Header style class, ex: <code>border-flat, border-semi-round, border-round, border-1px border-2px border-3px, shadow-bottom, shadow-top shadow-bottom-right, shadow-bottom-left</code>','accordions'),
            'type'		=> 'text',
            'value'		=> $header_class,
            'default'		=> '',
            'placeholder' => '',
        );

        $settings_tabs_field->generate_field($args);





        $args = array(
            'id'		=> 'background_color',
            'css_id'		=> 'header_background_color',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Background color','accordions'),
            'details'	=> __('Background color of header on idle','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $header_background_color,
            'default'		=> '',
            'placeholder' => '#eeeeee',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'active_background_color',
            'css_id'		=> 'header_active_background_color',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Active background color','accordions'),
            'details'	=> __('Background color of header on active stats','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $header_active_background_color,
            'default'		=> '',
            'placeholder' => '#dddddd',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'color',
            'css_id'		=> 'header_color',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Color','accordions'),
            'details'	=> __('Font color for accordion headers','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $header_color,
            'default'		=> '',
            'placeholder' => '#999999',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'color_hover',
            'css_id'		=> 'header_color_hover',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Color on hover','accordions'),
            'details'	=> __('Font color for accordion headers','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $header_color_hover,
            'default'		=> '',
            'placeholder' => '#7777777',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'font_size',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Font size','accordions'),
            'details'	=> __('Choose font size for header text','accordions'),
            'type'		=> 'text',
            'value'		=> $header_font_size,
            'default'		=> '',
            'placeholder' => '14px',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'font_family',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Font family','accordions'),
            'details'	=> __('Choose font family for header text','accordions'),
            'type'		=> 'text',
            'value'		=> $header_font_family,
            'placeholder' => 'Open Sans',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'padding',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Padding','accordions'),
            'details'	=> __('Choose header area padding','accordions'),
            'type'		=> 'text',
            'value'		=> $header_padding,
            'default'		=> '',
            'placeholder' => '10px',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'margin',
            'parent'		=> 'accordions_options[header]',
            'title'		=> __('Margin','accordions'),
            'details'	=> __('Choose header area margin','accordions'),
            'type'		=> 'text',
            'value'		=> $header_margin,
            'default'		=> '',
            'placeholder' => '5px',
        );

        $settings_tabs_field->generate_field($args);
        ?>

    </div>

    <div class="section">
        <div class="section-title"><?php echo __('Accordions content style','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Customize accordion content.','accordions'); ?></p>

        <?php

        $args = array(
            'id'		=> 'class',
            'css_id'		=> 'header_class',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Add class','accordions'),
            'details'	=> __('Body style class, ex: <code>border-flat, border-semi-round, border-round, border-1px border-2px border-3px, shadow-bottom, shadow-top shadow-bottom-right, shadow-bottom-left</code>','accordions'),
            'type'		=> 'text',
            'value'		=> $body_class,
            'default'		=> '',
            'placeholder' => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'color',
            'css_id'		=> 'body_color_hover',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Color','accordions'),
            'details'	=> __('You can choose custom color for accordion content','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $body_color,
            'default'		=> '',
            'placeholder' => '#999999',
        );

        $settings_tabs_field->generate_field($args);
        ?>


        <?php
        $args = array(
            'id'		=> 'font_size',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Font size','accordions'),
            'details'	=> __('You can set custom font size for accordion content','accordions'),
            'type'		=> 'text',
            'value'		=> $body_font_size,
            'default'		=> '',
            'placeholder' => '10px',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'font_family',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Font family','accordions'),
            'details'	=> __('Choose font family for accordion content text','accordions'),
            'type'		=> 'text',
            'value'		=> $body_font_family,
            'placeholder' => 'Open Sans',
        );

        $settings_tabs_field->generate_field($args);

        ?>

        <?php
        $args = array(
            'id'		=> 'background_color',
            'css_id'		=> 'body_background_color',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Background color','accordions'),
            'details'	=> __('You can choose custom background color for accordion content area','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $body_background_color,
            'default'		=> '#ffffff',
            'placeholder' => '#ffffff',
        );

        $settings_tabs_field->generate_field($args);
        ?>


        <?php
        $args = array(
            'id'		=> 'padding',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Padding','accordions'),
            'details'	=> __('You can set custom padding for accordion content','accordions'),
            'type'		=> 'text',
            'value'		=> $body_padding,
            'default'		=> '',
            'placeholder' => '10px',
        );

        $settings_tabs_field->generate_field($args);
        ?>


        <?php
        $args = array(
            'id'		=> 'margin',
            'parent'		=> 'accordions_options[body]',
            'title'		=> __('Margin','accordions'),
            'details'	=> __('You can set custom margin for accordion content','accordions'),
            'type'		=> 'text',
            'value'		=> $body_margin,
            'default'		=> '',
            'placeholder' => '10px',
        );

        $settings_tabs_field->generate_field($args);

        ?>
    </div>

    <div class="section">
        <div class="section-title"><?php echo __('Container style','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Customize container style optons.','accordions'); ?></p>

        <?php

        $container = isset($accordions_options['container']) ? $accordions_options['container'] : array();
        $container_padding = isset($container['padding']) ? $container['padding'] : '';
        $container_background_color = isset($container['background_color']) ? $container['background_color'] : '';
        $container_text_align = isset($container['text_align']) ? $container['text_align'] : '';
        $container_background_img = isset($container['background_img']) ? $container['background_img'] : '';

        $width_large = isset($container['width_large']) ? $container['width_large'] : '';
        $width_medium = isset($container['width_medium']) ? $container['width_medium'] : '';
        $width_small = isset($container['width_small']) ? $container['width_small'] : '';


        $args = array(
            'id'		=> 'padding',
            'parent'		=> 'accordions_options[container]',
            'title'		=> __('Padding','accordions'),
            'details'	=> __('Set container padding','accordions'),
            'type'		=> 'text',
            'value'		=> $container_padding,
            'default'		=> '',
            'placeholder' => '10px',

        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'background_color',
            'parent'		=> 'accordions_options[container]',
            'title'		=> __('Background color','accordions'),
            'details'	=> __('Set container background color','accordions'),
            'type'		=> 'colorpicker',
            'value'		=> $container_background_color,
            'default'		=> '#ffffff',
            'placeholder' => '',

        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'text_align',
            'parent'		=> 'accordions_options[container]',
            'title'		=> __('Text align','accordions'),
            'details'	=> __('Set container text align','accordions'),
            'type'		=> 'select',
            'value'		=> $container_text_align,
            'default'		=> 'left',
            'args'		=> array(
                'left'	=> __('Left','accordions'),
                'right'	=> __('Right','accordions'),
                'center'	=> __('Center','accordions'),
                'justify'	=> __('Justify','accordions'),

            ),

        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'background_img',
            'parent'		=> 'accordions_options[container]',
            'title'		=> __('Background image','accordions'),
            'details'	=> __('Set container background image','accordions'),
            'type'		=> 'media_url',
            'value'		=> $container_background_img,
            'default'		=> '',
            'placeholder' => '',

        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'		=> 'width',
            'title'		=> __('Container width','accordions'),
            'details'	=> __('Set accordion width.','accordions'),
            'type'		=> 'option_group',
            'options'		=> array(
                array(
                    'id'		=> 'width_large',
                    'parent'		=> 'accordions_options[container]',
                    'title'		=> __('In desktop','accordions'),
                    'details'	=> __('min-width: 1200px, ex: 300px','accordions'),
                    'type'		=> 'text',
                    'value'		=> $width_large,
                    'default'		=> '',
                    'placeholder'   => '',
                ),
                array(
                    'id'		=> 'width_medium',
                    'parent'		=> 'accordions_options[container]',
                    'title'		=> __('In tablet & small desktop','accordions'),
                    'details'	=> __('min-width: 992px, ex: 90%','accordions'),
                    'type'		=> 'text',
                    'value'		=> $width_medium,
                    'default'		=> '',
                    'placeholder'   => '',
                ),
                array(
                    'id'		=> 'width_small',
                    'parent'		=> 'accordions_options[container]',
                    'title'		=> __('In mobile','accordions'),
                    'details'	=> __('min-width: 576px, ex: 90%','accordions'),
                    'type'		=> 'text',
                    'value'		=> $width_small,
                    'default'		=> '',
                    'placeholder'   => '',
                ),
            ),

        );

        $settings_tabs_field->generate_field($args);



        ?>

    </div>

    <?php

}



add_action('accordions_metabox_content_content', 'accordions_metabox_content_content', 10);

function accordions_metabox_content_content($post_id){

    $settings_tabs_field = new settings_tabs_field();
    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordions_options = !empty($accordions_options) ? $accordions_options : accordions_old_options($post_id);

    $accordions_content = isset($accordions_options['content']) ? $accordions_options['content'] : array();


    ?>

    <div class="section">
        <div class="section-title"><?php echo __('Accordions content','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Add you accordion content here.','accordions'); ?></p>

        <?php


        //echo '<pre>'.var_export($accordions_active_accordion, true).'</pre>';
        //echo '<pre>'.var_export($accordions_content_body, true).'</pre>';


        $meta_fields = array(
            array(
                'id'		=> 'header',
                'css_id'		=> 'header_TIMEINDEX',
                'title'		=> __('Header','accordions'),
                'details'	=> __('Accordion header.','accordions'),
                'type'		=> 'text',
                'value'		=> '',
                'default'		=> '',
                'placeholder'		=> 'Address',
            ),
            array(
                'id'		=> 'body',
                'css_id'		=> 'body_TIMEINDEX',
                'title'		=> __('Accordion body','accordions'),
                'details'	=> __('Accordion body content.','accordions'),
                'type'		=> 'textarea_editor',
                'value'		=> '',
                'default'		=> '',
                'placeholder'		=> '',
            ),
            array(
                'id'		=> 'hide',
                'css_id'		=> 'hide_TIMEINDEX',
                'title'		=> __('Hide','accordions'),
                'details'	=> __('Hide this.','accordions'),
                'type'		=> 'select',
                'value'		=> '',
                'default'		=> 'false',
                'args'		=> array(
                    'true'	=> __('True','accordions'),
                    'false'	=> __('False','accordions'),
                ),
            ),

            array(
                'id'		=> 'hide_schema',
                'css_id'		=> 'hide_TIMEINDEX',
                'title'		=> __('Hide schema','accordions'),
                'details'	=> __('Hide schema for this.','accordions'),
                'type'		=> 'select',
                'value'		=> '',
                'default'		=> 'false',
                'args'		=> array(
                    'true'	=> __('True','accordions'),
                    'false'	=> __('False','accordions'),
                ),
            ),

        );

        $meta_fields = apply_filters('accordions_content_fields', $meta_fields);

        $args = array(
            'id'		=> 'content',
            'parent'		=> 'accordions_options',
            'title'		=> __('Accordion content','text-domain'),
            'details'	=> __('Set accordion content & title here.','text-domain'),
            'collapsible'=> true,
            'type'		=> 'repeatable',
            'limit'		=> 10,
            'title_field'		=> 'header',
            'value'		=> $accordions_content,
            'fields'    => $meta_fields,
        );

        $settings_tabs_field->generate_field($args);



        ob_start();

        ?>




    </div>
    <?php
}


add_action('accordions_metabox_content_custom_scripts', 'accordions_metabox_content_custom_scripts', 10);

function accordions_metabox_content_custom_scripts($post_id){


    $settings_tabs_field = new settings_tabs_field();

    $accordions_options = get_post_meta($post_id,'accordions_options', true);
    $accordions_options = !empty($accordions_options) ? $accordions_options : accordions_old_options($post_id);

    $custom_scripts = isset($accordions_options['custom_scripts']) ? $accordions_options['custom_scripts'] : array();
    $custom_js = isset($custom_scripts['custom_js']) ? $custom_scripts['custom_js'] : '';
    $custom_css = isset($custom_scripts['custom_css']) ? $custom_scripts['custom_css'] : '';


    ?>
    <div class="section">
        <div class="section-title"><?php echo __('Accordions Scripts','accordions'); ?></div>
        <p class="description section-description"><?php echo __('Add your own CSS & Scripts.','accordions'); ?></p>

        <?php
        $args = array(
            'id'		=> 'custom_js',
            'parent'		=> 'accordions_options[custom_scripts]',
            'title'		=> __('Custom Js','accordions'),
            'details'	=> __('You can add custom scripts here, do not use <code>&lt;script&gt; &lt;/script&gt;</code> tag','accordions'),
            'type'		=> 'scripts_js',
            'value'		=> $custom_js,
            'default'		=> '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'custom_css',
            'parent'		=> 'accordions_options[custom_scripts]',
            'title'		=> __('Custom CSS','accordions'),
            'details'	=> __('You can add custom css here, do not use <code>  &lt;style&gt; &lt;/style&gt;</code> tag','accordions'),
            'type'		=> 'scripts_css',
            'value'		=> $custom_css,
            'default'		=> '',
        );

        $settings_tabs_field->generate_field($args);
        ?>

    </div>
    <?php


}



add_action('accordions_metabox_content_help_support', 'accordions_metabox_content_help_support');

if(!function_exists('accordions_metabox_content_help_support')) {
    function accordions_metabox_content_help_support($tab){

        $settings_tabs_field = new settings_tabs_field();

        ?>
        <div class="section">

            <div class="section-title"><?php echo __('Get support', 'accordions'); ?></div>
            <p class="description section-description"><?php echo __('Use following to get help and support from our expert team.', 'accordions'); ?></p>

            <?php


            ob_start();
            ?>

            <p><?php echo __('Ask question for free on our forum and get quick reply from our expert team members.', 'accordions'); ?></p>
            <a class="button" href="https://www.pickplugins.com/create-support-ticket/"><?php echo __('Create support ticket', 'accordions'); ?></a>

            <p><?php echo __('Read our documentation before asking your question.', 'accordions'); ?></p>
            <a class="button" href="https://www.pickplugins.com/documentation/accordions/"><?php echo __('Documentation', 'accordions'); ?></a>

            <p><?php echo __('Watch video tutorials.', 'accordions'); ?></p>
            <a class="button" href="https://www.youtube.com/playlist?list=PL0QP7T2SN94ZPeQ83jOnteDDrOeDLBuFD"><i class="fab fa-youtube"></i> <?php echo __('All tutorials', 'accordions'); ?></a>

            <ul>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=4ZGMA6hOoxs">Accordions - data migration</a></li>


            </ul>



            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'get_support',
                //'parent'		=> '',
                'title'		=> __('Ask question','accordions'),
                'details'	=> '',
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>

            <p class="">We wish your 2 minutes to write your feedback about the <b>Accordions</b> plugin. give us <span style="color: #ffae19"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span></p>

            <a target="_blank" href="https://wordpress.org/plugins/accordions/#reviews" class="button"><i class="fab fa-wordpress"></i> Write a review</a>


            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'reviews',
                //'parent'		=> '',
                'title'		=> __('Submit reviews','accordions'),
                'details'	=> '',
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);




            ?>



        </div>
        <?php


    }
}



add_action('accordions_metabox_content_buy_pro', 'accordions_settings_content_buy_pro');




add_action('accordions_post_meta_save','accordions_post_meta_save');

function accordions_post_meta_save($job_id){

    $accordions_options = isset($_POST['accordions_options']) ? stripslashes_deep($_POST['accordions_options']) : '';
    update_post_meta($job_id, 'accordions_options', $accordions_options);


}
