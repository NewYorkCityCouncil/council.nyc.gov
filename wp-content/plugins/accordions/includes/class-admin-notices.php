<?php
if ( ! defined('ABSPATH')) exit; // if direct access 

class class_accordions_notices{

    public function __construct(){
        //add_action('admin_notices', array( $this, 'data_upgrade' ));
    }

    public function data_upgrade(){



        $accordions_plugin_info = get_option('accordions_plugin_info');
        $accordions_upgrade = isset($accordions_plugin_info['accordions_upgrade']) ? $accordions_plugin_info['accordions_upgrade'] : '';


        $actionurl = admin_url().'edit.php?post_type=accordions&page=upgrade_status';
        $actionurl = wp_nonce_url( $actionurl,  'accordions_upgrade' );

        $nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';

        if ( wp_verify_nonce( $nonce, 'accordions_upgrade' )  ){
            $accordions_plugin_info['accordions_upgrade'] = 'processing';
            update_option('accordions_plugin_info', $accordions_plugin_info);
            wp_schedule_event(time(), '1minute', 'accordions_cron_upgrade_settings');

            return;
        }


        if(empty($accordions_upgrade)){

            ?>
            <div class="update-nag">
                <?php
                echo sprintf(__('Data migration required for <b>Accordions by PickPlugins</b> plugin, please <a class="button button-primary" href="%s">click to start</a> migration. Watch this <a target="_blank" href="https://www.youtube.com/watch?v=4ZGMA6hOoxs">video</a>  first', 'accordions'), $actionurl);
                ?>
            </div>
            <?php


        }

    }




}

new class_accordions_notices();