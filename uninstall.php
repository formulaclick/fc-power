<?php
/**
 * @package		FCPower
 */

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

$option_names = array('_transient_fc_power_pluginlist');
 
foreach($option_names as $option){
	delete_option( $option );
	delete_site_option( $option ); // For site options in Multisite
}
