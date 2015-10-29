<?php
/**
 * Plugin Name: FC Power
 * Description: Plugin con funciones y seteos personalizados
 * Version: 1.0.2
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

/**
 * Build settings
 */
 
require_once( plugin_dir_path( __FILE__ ) . 'custom-login.php' );
 
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'plugin-list.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-fc-power.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-tgm-plugin-activation.php' );
	add_action( 'plugins_loaded', array( 'FCPower', 'get_instance' ) );
}

/**
 * Eliminamos la barra admin del usuario
 */
add_filter('show_admin_bar', '__return_false');

/**
 * Eliminamos el gestor de links
 */
update_option( 'link_manager_enabled', 0 );

