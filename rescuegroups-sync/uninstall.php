<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Remove options.
delete_option( 'rescue_sync_api_key' );
// More cleanup to be added.
