<?php
namespace RescueSync;

class Sync {

    /**
     * Constructor.
     *
     * Adds the cron hook for running the sync process.
     */
    public function __construct() {
        add_action( 'rescue_sync_cron', [ $this, 'run' ] );
    }

    /**
     * Schedule the cron event when the plugin is activated.
     */
    public static function activate() {
        if ( ! wp_next_scheduled( 'rescue_sync_cron' ) ) {
            wp_schedule_event( time(), 'hourly', 'rescue_sync_cron' );
        }
    }

    /**
     * Clear the cron event when the plugin is deactivated.
     */
    public static function deactivate() {
        $timestamp = wp_next_scheduled( 'rescue_sync_cron' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'rescue_sync_cron' );
        }
    }

    /**
     * Fetch animals from the API and create/update posts.
     */
    public function run() {
        $api_key = Utils::get_option( 'api_key' );
        if ( empty( $api_key ) ) {
            return;
        }

        $client  = new API_Client( $api_key );
        $results = $client->get_all_available_animals();

        if ( empty( $results['data'] ) || ! is_array( $results['data'] ) ) {
            return;
        }

        foreach ( $results['data'] as $animal ) {
            $animal_id = isset( $animal['id'] ) ? intval( $animal['id'] ) : 0;
            if ( ! $animal_id ) {
                continue;
            }

            $name        = isset( $animal['attributes']['name'] ) ? $animal['attributes']['name'] : '';
            $description = isset( $animal['attributes']['descriptionText'] ) ? $animal['attributes']['descriptionText'] : '';

            $query = new \WP_Query([
                'post_type'      => 'adoptable_pet',
                'post_status'    => 'any',
                'meta_key'       => 'rescuegroups_id',
                'meta_value'     => $animal_id,
                'fields'         => 'ids',
                'posts_per_page' => 1,
            ]);

            $post_args = [
                'post_title'   => sanitize_text_field( $name ),
                'post_content' => wp_kses_post( $description ),
                'post_status'  => 'publish',
                'post_type'    => 'adoptable_pet',
            ];

            if ( $query->have_posts() ) {
                $post_id = $query->posts[0];

                // Skip updating posts that are explicitly hidden.
                if ( get_post_meta( $post_id, '_rescue_sync_hidden', true ) ) {
                    continue;
                }

                $post_args['ID'] = $post_id;
            }

            $post_id = wp_insert_post( $post_args );

            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, 'rescuegroups_id', $animal_id );
                update_post_meta( $post_id, 'rescuegroups_raw', wp_json_encode( $animal ) );
            }
        }
    }
}
