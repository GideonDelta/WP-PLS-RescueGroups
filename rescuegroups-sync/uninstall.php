<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Delete adoptable_pet posts and their meta.
$posts = get_posts([
    'post_type'   => 'adoptable_pet',
    'post_status' => 'any',
    'numberposts' => -1,
    'fields'      => 'ids',
]);

foreach ( $posts as $post_id ) {
    wp_delete_post( $post_id, true );
}

// Unschedule the cron event if it exists.
$timestamp = wp_next_scheduled( 'rescue_sync_cron' );
if ( $timestamp ) {
    wp_unschedule_event( $timestamp, 'rescue_sync_cron' );
}

// Remove options.
delete_option( 'rescue_sync_api_key' );
