<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

global $wpdb;

// Delete all adoptable_pet posts and their meta.
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

// Remove custom taxonomy terms and relationships.
$taxonomies = [ 'pet_species', 'pet_breed' ];
foreach ( $taxonomies as $tax ) {
    $tt_ids = $wpdb->get_col( $wpdb->prepare( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s", $tax ) );
    if ( ! empty( $tt_ids ) ) {
        $in_tt_ids = implode( ',', array_map( 'intval', $tt_ids ) );
        $wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( $in_tt_ids )" );
        $term_ids = $wpdb->get_col( "SELECT term_id FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id IN ( $in_tt_ids )" );
        $wpdb->query( "DELETE FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id IN ( $in_tt_ids )" );
        if ( ! empty( $term_ids ) ) {
            $in_term_ids = implode( ',', array_map( 'intval', $term_ids ) );
            $wpdb->query( "DELETE FROM {$wpdb->terms} WHERE term_id IN ( $in_term_ids )" );
            if ( ! empty( $wpdb->termmeta ) ) {
                $wpdb->query( "DELETE FROM {$wpdb->termmeta} WHERE term_id IN ( $in_term_ids )" );
            }
        }
    }
}

// Delete any post meta starting with _rescue_sync_.
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $wpdb->esc_like( '_rescue_sync_' ) . '%' ) );

// Unschedule and clear all cron events.
while ( ( $timestamp = wp_next_scheduled( 'rescue_sync_cron' ) ) ) {
    wp_unschedule_event( $timestamp, 'rescue_sync_cron' );
}
wp_clear_scheduled_hook( 'rescue_sync_cron' );

// Remove plugin options.
$options = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( 'rescue_sync_' ) . '%' ) );
foreach ( $options as $option ) {
    delete_option( $option );
}
