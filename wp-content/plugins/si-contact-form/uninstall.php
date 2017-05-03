<?php

/*
 * Wordpress will run the code in this file when the user deletes the plugin
 * 
 */

// Be sure that Wordpress is deleting the plugin
if(defined('WP_UNINSTALL_PLUGIN') ){
	// XXX Prompt to delete options

    // settings get deleted when plugin is deleted from admin plugins page
    delete_option('fs_contact_global');

    // delete up to 100 forms (a unique configuration for each contact form)
    for ($i = 1; $i <= 100; $i++) {
       delete_option("fs_contact_form$i");
    }
}  
// end of file  