<?php
// Fired when the plugin is uninstalled

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

if (current_user_can('delete_plugins')) { // restrict to admins
        $basic_settings = 'queerify_basic';
        delete_option($basic_settings);
        // for site options in Multisite
        delete_site_option($basic_settings);   

} ?>
