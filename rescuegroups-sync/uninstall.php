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
// Delete all adoptable_pet posts.
$posts = get_posts(
    [
        'post_type'   => 'adoptable_pet',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields'      => 'ids',
    ]
);

foreach ( $posts as $post_id ) {
    wp_delete_post( $post_id, true );
}

// Unschedule cron events.
wp_clear_scheduled_hook( 'rescue_sync_cron' );
