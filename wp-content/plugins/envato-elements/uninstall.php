<?php
// If uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Clean up options.
delete_option( 'envato_elements_tracker_notice' );
delete_option( '_envato_elements_installed_time' );
delete_option( 'envato_elements_license_code' );
// todo: move everything into the single options array below.
delete_option( 'envato_elements_options' );

// Remove the scheduled task.
wp_clear_scheduled_hook( 'envato_elements_cron' );