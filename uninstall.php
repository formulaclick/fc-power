<?php
/**
 * @package		FCPower
 */

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}


delete_option( 'fc_power_plugin_list' );
delete_option( '_transient_fc_power_pluginlist' );

// For site options in Multisite
delete_site_option( 'fc_power_plugin_list' );
delete_site_option( '_transient_fc_power_pluginlist' );