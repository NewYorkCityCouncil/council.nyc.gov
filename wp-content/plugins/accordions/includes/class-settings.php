<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 	


class accordions_class_settings{
	
	
    public function __construct(){

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

    }
	
	
	public function admin_menu() {

        $accordions_plugin_info = get_option('accordions_plugin_info');
        $accordions_upgrade = isset($accordions_plugin_info['accordions_upgrade']) ? $accordions_plugin_info['accordions_upgrade'] : '';


        add_submenu_page( 'edit.php?post_type=accordions', __( 'Settings', 'accordions' ), __( 'Settings', 'accordions' ), 'manage_options', 'settings', array( $this, 'settings' ) );

        if($accordions_upgrade != 'done'){
            //add_submenu_page( 'edit.php?post_type=accordions', __( 'Upgrade status', 'accordions' ), __( 'Upgrade status', 'accordions' ), 'manage_options', 'upgrade_status', array( $this, 'upgrade_status' ) );
        }
	}
	
	public function settings(){
        include( 'menu/settings.php' );
    }

    public function upgrade_status(){
        include( 'menu/upgrade-status.php' );
    }


}

new accordions_class_settings();

