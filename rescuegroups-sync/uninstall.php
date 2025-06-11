<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Remove custom posts and related meta.
$posts = get_posts([
    'post_type'      => 'adoptable_pet',
    'post_status'    => 'any',
    'numberposts'    => -1,
    'fields'         => 'ids',
]);

foreach ( $posts as $post_id ) {
    wp_delete_post( $post_id, true );
}

// Remove all plugin options, including those added by extended settings.
global $wpdb;
$options = $wpdb->get_col(
    $wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( 'rescue_sync_' ) . '%'
    )
);

foreach ( $options as $option ) {
    delete_option( $option );
}
