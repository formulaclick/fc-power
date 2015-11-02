<?php
/**
 * Plugin Name: FC Power
 * Description: Plugin con funciones y seteos personalizados
 * Version: 1.0.5
 * Author: Formula Click
 * Author URI: http://www.formulaclick.com
 * License: GPL2
 */

// Inicializamos updater
if( ! class_exists( 'FC_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}
$updater = new FC_Updater( __FILE__ );
$updater->set_username( 'formulaclick' );
$updater->set_repository( 'fc-power' );
$updater->initialize();

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Mostrar logo personalizado
if(get_option('fc_power_custom_login')){
	require_once( plugin_dir_path( __FILE__ ) . 'custom-login.php' );
}

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	
	//hacemos que no se regeneren temas borrados
	define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );
	
	//No Mostrar admin bar
	if(!get_option('fc_power_show_adminmenu')){
		add_filter('show_admin_bar', '__return_false');
	}
	
	//permitir repair
	if(get_option('fc_power_allow_repair')){
		define('WP_ALLOW_REPAIR', true);
	}
	
	//No Mostrar links
	if(!get_option('fc_power_show_links')){
		//echo 'passo';exit;
		update_option( 'link_manager_enabled', 0 );
	}else{
		update_option( 'link_manager_enabled', 1 );
	}	
	
	require_once( plugin_dir_path( __FILE__ ) . 'plugin-list.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-fc-power.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-tgm-plugin-activation.php' );
	add_action( 'plugins_loaded', array( 'FCPower', 'get_instance' ) );
}
