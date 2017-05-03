<?php
/*
*  fscf_parse_callback
*
*  @description: Parses the return callback once the user logged in to vCita
*  @since: 3.6
*  @created: 25/01/13
*/

class fscf_parse_callback {

	/*
	*  __construct
	*
	*  @description:
	*  @since 3.1.8
	*  @created: 23/06/12
	*/

	function __construct(){

        // Uses priority 20 to laod after plugin init
        add_action( 'admin_menu', array($this, 'add_parse_vcita_callback_page'), 20 );

    }

    /**
     * Adds a hidden page to allow reseting the plugin (mainly used for degbugging but not exclusive)
     * @since 0.1.0
     */
    function add_parse_vcita_callback_page(){
        add_submenu_page(
            null,
            //__('', 'livesite'),
           // __('', 'livesite'),
           '',
           '',
            'edit_posts',
            'live-site-parse-vcita-callback',
            array($this, 'ls_parse_vcita_callback')
        );
    }

    /**
     * Parses the return values from vcita connection
     * @since 0.1.0
     */
    function ls_parse_vcita_callback(){

    	$success = sanitize_text_field($_GET['success']);
    	$uid = sanitize_text_field($_GET['uid']);
    	$first_name = sanitize_text_field($_GET['first_name']);
    	$last_name = sanitize_text_field($_GET['last_name']);
    	$title = sanitize_text_field($_GET['title']);
    	$confirmation_token = sanitize_text_field($_GET['confirmation_token']);
    	$confirmed = sanitize_text_field($_GET['confirmed']);
    	$engage_delay = sanitize_text_field($_GET['engage_delay']);
    	$implementation_key = sanitize_text_field($_GET['implementation_key']);
    	$email = sanitize_email($_GET['email']);

		ls_set_settings( array(
            'vcita_connected' => true,
            'vcita_params' => array(
                'success'              => $success,
            	'uid'                  => $uid,
            	'first_name'           => $first_name,
            	'last_name'            => $last_name,
            	'title'                => $title,
            	'confirmation_token'   => $confirmation_token,
            	'confirmed'            => $confirmed,
            	'engage_delay'         => $engage_delay,
            	'implementation_key'   => $implementation_key,
            	'email'                => $email
            )
        ));

    	$redirect_url = $ls_helpers->get_plugin_path();

    ?>
    <script type="text/javascript">
        window.location = "<?php echo $redirect_url; ?>";
    </script>
    <?php }

}

new fscf_parse_callback();

