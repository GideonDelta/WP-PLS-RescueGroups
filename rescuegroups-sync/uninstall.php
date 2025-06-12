<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Delete all adoptable_pet posts and their meta.
$posts = get_posts( [
    'post_type'   => 'adoptable_pet',
    'post_status' => 'any',
    'numberposts' => -1,
    'fields'      => 'ids',
] );

foreach ( $posts as $post_id ) {
    wp_delete_post( $post_id, true );
}

// Unschedule and clear all cron events.
$timestamp = wp_next_scheduled( 'rescue_sync_cron' );
if ( $timestamp ) {
    wp_unschedule_event( $timestamp, 'rescue_sync_cron' );
}
wp_clear_scheduled_hook( 'rescue_sync_cron' );

// Remove plugin options.
delete_option( 'rescue_sync_api_key' );
delete_option( 'rescue_sync_frequency' );
delete_option( 'rescue_sync_last_sync' );
delete_option( 'rescue_sync_last_status' );
delete_option( 'rescue_sync_archive_slug' );
delete_option( 'rescue_sync_default_number' );
delete_option( 'rescue_sync_default_featured' );

// TODO: More extensive cleanup (e.g., remove metadata) can be added here.
