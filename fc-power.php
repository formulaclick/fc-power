<?php
/**
 * Plugin Name: FC Power
 * Description: Plugin con funciones y seteos personalizados
 * Version: 1.1.3
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

require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

if  ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
	require_once( plugin_dir_path( __FILE__ ) . 'plugin-list.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-fc-power.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-tgm-plugin-activation.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/class-fc-module.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'modules/opciones-generales/class-fc-opciones-generales.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'modules/envios-smtp/class-fc-envios-smtp.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'modules/aviso-legal/class-fc-aviso-legal.php' );
	add_action( 'plugins_loaded', array( 'FCPower', 'get_instance' ) );
}
