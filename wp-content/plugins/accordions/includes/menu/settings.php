<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access


$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'general';

$accordions_settings_tab = array();

$accordions_settings_tab[] = array(
    'id' => 'general',
    'title' => sprintf(__('%s General','accordions'),'<i class="fas fa-list-ul"></i>'),
    'priority' => 1,
    'active' => ($current_tab == 'general') ? true : false,
);

// $accordions_settings_tab[] = array(
//     'id' => '3rd_party_import',
//     'title' => sprintf(__('%s 3rd party import','accordions'),'<i class="fas fa-download"></i>'),
//     'priority' => 2,
//     'active' => ($current_tab == '3rd_party_import') ? true : false,
// );

$accordions_settings_tab[] = array(
    'id' => 'help_support',
    'title' => sprintf(__('%s Help & support','accordions'),'<i class="fas fa-hands-helping"></i>'),
    'priority' => 3,
    'active' => ($current_tab == 'help_support') ? true : false,
);

// $accordions_settings_tab[] = array(
//     'id' => 'buy_pro',
//     'title' => sprintf(__('%s Buy Pro','accordions'),'<i class="fas fa-store"></i>'),
//     'priority' => 9,
//     'active' => ($current_tab == 'buy_pro') ? true : false,
// );

$accordions_settings_tab = apply_filters('accordions_settings_tabs', $accordions_settings_tab);

$tabs_sorted = array();

if(!empty($accordions_settings_tab))
foreach ($accordions_settings_tab as $page_key => $tab) $tabs_sorted[$page_key] = isset( $tab['priority'] ) ? $tab['priority'] : 0;
array_multisort($tabs_sorted, SORT_ASC, $accordions_settings_tab);



$accordions_settings = get_option('accordions_settings');

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s Settings', 'accordions'), accordions_plugin_name)?></h2>
		<form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	        <input type="hidden" name="accordions_hidden" value="Y">
            <input type="hidden" name="tab" value="<?php echo $current_tab; ?>">
            <?php
            if(!empty($_POST['accordions_hidden'])){
                $nonce = sanitize_text_field($_POST['_wpnonce']);
                if(wp_verify_nonce( $nonce, 'accordions_nonce' ) && $_POST['accordions_hidden'] == 'Y') {
                    do_action('accordions_settings_save');
                    ?>
                    <div class="updated notice  is-dismissible"><p><strong><?php _e('Changes Saved.', 'accordions' ); ?></strong></p></div>
                    <?php
                }
            }
            ?>
            <div class="settings-tabs-loading" style="">Loading...</div>
            <div class="settings-tabs vertical has-right-panel" style="display: none">
                <div class="settings-tabs-right-panel">
                    <?php
                    if(!empty($accordions_settings_tab))
                    foreach ($accordions_settings_tab as $tab) {
                        $id = $tab['id'];
                        $active = $tab['active'];
                        ?>
                        <div class="right-panel-content <?php if($active) echo 'active';?> right-panel-content-<?php echo $id; ?>">
                            <?php
                            do_action('accordions_settings_tabs_right_panel_'.$id);
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <ul class="tab-navs">
                    <?php
                    if(!empty($accordions_settings_tab))
                    foreach ($accordions_settings_tab as $tab){
                        $id = $tab['id'];
                        $title = $tab['title'];
                        $active = $tab['active'];
                        $data_visible = isset($tab['data_visible']) ? $tab['data_visible'] : '';
                        $hidden = isset($tab['hidden']) ? $tab['hidden'] : false;
                        $is_pro = isset($tab['is_pro']) ? $tab['is_pro'] : false;
                        $pro_text = isset($tab['pro_text']) ? $tab['pro_text'] : '';
                        ?>
                        <li <?php if(!empty($data_visible)):  ?> data_visible="<?php echo $data_visible; ?>" <?php endif; ?> class="tab-nav <?php if($hidden) echo 'hidden';?> <?php if($active) echo 'active';?>" data-id="<?php echo $id; ?>">
                            <?php echo $title; ?>
                            <?php
                            if($is_pro):
                                ?><span class="pro-feature"><?php echo $pro_text; ?></span> <?php
                            endif;
                            ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                if(!empty($accordions_settings_tab))
                foreach ($accordions_settings_tab as $tab){
                    $id = $tab['id'];
                    $title = $tab['title'];
                    $active = $tab['active'];
                    ?>
                    <div class="tab-content <?php if($active) echo 'active';?>" id="<?php echo $id; ?>">
                        <?php
                        do_action('accordions_settings_content_'.$id, $tab);
                        ?>
                    </div>
                    <?php
                }
                ?>
                <div class="clear clearfix"></div>
                <p class="submit">
                    <?php wp_nonce_field( 'accordions_nonce' ); ?>
                    <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes','accordions' ); ?>" />
                </p>
            </div>
		</form>
</div>