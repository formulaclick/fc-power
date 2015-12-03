<?php
/**
 * Plugin Name: FC Power
 * Description: Plugin con funciones y seteos personalizados
 * Version: 1.0.10
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
	
	if (is_admin()){
		
		//permitir repair
		if(get_option('fc_power_allow_repair')){
			define('WP_ALLOW_REPAIR', true);
		}		
		
		//hacemos que no se regeneren temas borrados
		define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );

		//No Mostrar links
		if(!get_option('fc_power_show_links')){
			update_option( 'link_manager_enabled', 0 );
		}else{
			update_option( 'link_manager_enabled', 1 );
		}	
		
		require_once( plugin_dir_path( __FILE__ ) . 'plugin-list.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/class-fc-power.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/class-tgm-plugin-activation.php' );
		add_action( 'plugins_loaded', array( 'FCPower', 'get_instance' ) );
			
	}else{
		
		//Mostrar logo personalizado
		if(get_option('fc_power_custom_login')){
			require_once( plugin_dir_path( __FILE__ ) . 'custom-login.php' );
		}
		
		//No Mostrar admin bar
		if(!get_option('fc_power_show_adminmenu')){
			add_filter('show_admin_bar', '__return_false');
		}	

		function fc_legal_replace_content($content){
			
			$fc_power_aviso_legal_pagina = get_option('fc_power_aviso_legal_pagina');	
			if(get_the_ID() == $fc_power_aviso_legal_pagina){
				$fc_legal_vars = array();
				$fc_legal_vars['%%CABECERA%%'] = get_option('fc_power_aviso_legal_tag_titulos');
				$fc_legal_vars['%%RRRRR%%'] = get_option('fc_power_aviso_legal_RRRRR');
				$fc_legal_vars['%%NNNNN%%'] = get_option('fc_power_aviso_legal_NNNNN');
				$fc_legal_vars['%%WWWWW%%'] = get_option('fc_power_aviso_legal_WWWWW');
				$fc_legal_vars['%%QQQQQ%%'] = get_option('fc_power_aviso_legal_QQQQQ');
				$fc_legal_vars['%%EEEEE%%'] = get_option('fc_power_aviso_legal_EEEEE');
				$fc_legal_vars['%%CCCC%%'] = get_option('fc_power_aviso_legal_CCCC');
				$fc_legal_vars['%%DDDD%%'] = get_option('fc_power_aviso_legal_DDDD');
				$fc_legal_vars['%%MMMM%%'] = get_option('fc_power_aviso_legal_MMMM');
				include 'template-aviso-legal.php';
				
				if($fc_legal_vars['%%MMMM%%'] == ''){
					$template = str_replace(' inscrita en el %%MMMM%%','', $template);
				}
				
				$content = str_replace(array_keys($fc_legal_vars), array_values($fc_legal_vars), $template);
			}
			
			return $content;
		}
		add_filter('the_content', 'fc_legal_replace_content');

	}
	
}
