<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Remove options.
delete_option( 'rescue_sync_api_key' );
delete_option( 'rescue_sync_frequency' );
delete_option( 'rescue_sync_last_sync' );
delete_option( 'rescue_sync_last_status' );
// More cleanup to be added.
