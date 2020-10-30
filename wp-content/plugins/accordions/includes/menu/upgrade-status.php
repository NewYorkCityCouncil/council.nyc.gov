<?php	
if ( ! defined('ABSPATH')) exit;  // if direct access

$accordions_plugin_info = get_option('accordions_plugin_info');
$accordions_settings_upgrade = isset($accordions_plugin_info['settings_upgrade']) ? $accordions_plugin_info['settings_upgrade'] : '';
$accordions_upgrade = isset($accordions_plugin_info['accordions_upgrade']) ? $accordions_plugin_info['accordions_upgrade'] : '';

//echo '<pre>'.var_export($accordions_upgrade, true).'</pre>';
wp_enqueue_style('font-awesome-5');

$url = admin_url().'edit.php?post_type=accordions&page=upgrade_status';

?>
<?php

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php echo sprintf(__('%s Settings - Update', 'accordions'), accordions_plugin_name)?></h2>
    <p>accordions settings and accordions options data should automatic upgrade. please wait until all update completed. each loop will take 1 minute to completed, based on your accordions it will take take few minutes to completed.</p>
    <p>If you have any issue please <a href="https://www.pickplugins.com/forum/">create support ticket</a> on our forum</p>
    <p>Don't panic while updating, your old data still saved on database and you can downgrade plugin any time, please <a href="https://wordpress.org/plugins/woocommerce-products-slider/advanced/#plugin-download-history-stats">download from here</a> old version and reinstall.</p>


    <script>
        setTimeout(function(){
            window.location.href = '<?php echo $url; ?>';
        }, 1000*50);

    </script>

    <h3>Accordions settings upgrade status</h3>

    <?php

    if(!empty($accordions_settings_upgrade)){
        ?>
        <p>Completed</p>
        <?php
    }else{
        ?>
        <p>Pending</p>
        <?php
    }

    ?>




    <h3>Accordions post data upgrade status</h3>
    <?php

    $meta_query = array();

    $meta_query[] = array(
        'key' => 'accordions_upgrade_status',
        'value' => 'done',
        'compare' => '='
    );

    $args = array(
        'post_type'=>'accordions',
        'post_status'=>'any',
        'posts_per_page'=> -1,
        'meta_query'=> $meta_query,

    );

    $wp_query = new WP_Query($args);

    if ( $wp_query->have_posts() ) :
        ?>
        <ul>
        <?php
        while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $accordions_id = get_the_id();
            $accordions_title = get_the_title();
            ?>
            <li><?php echo $accordions_title; ?> - Done</li>
            <?php

        endwhile;
        ?>
        </ul>
        <?php

    else:
        ?>
        <p>Pending</p>
        <?php
    endif;


    if($accordions_upgrade == 'done'){
        wp_safe_redirect(admin_url().'edit.php?post_type=accordions');
    }


    ?>



    <p><a class="button" href="<?php echo admin_url().'edit.php?post_type=accordions&page=upgrade_status'; ?>">Refresh</a> to check Migration stats. <i class="fas fa-spin fa-spinner"></i></p>












</div>
