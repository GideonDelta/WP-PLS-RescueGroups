<?php
namespace RescueSync;

class Sync {

    const META_SPECIES = '_rescue_sync_species';
    const META_BREED   = '_rescue_sync_breed';
    const META_AGE     = '_rescue_sync_age';
    const META_GENDER  = '_rescue_sync_gender';
    const META_PHOTOS  = '_rescue_sync_photos';

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
        // Ensure the custom post type is registered so rewrite rules exist.
        $labels = [
            'name'          => 'Adoptable Pets',
            'singular_name' => 'Adoptable Pet',
        ];
        $args   = [
            'labels'      => $labels,
            'public'      => true,
            'supports'    => [ 'title', 'editor', 'thumbnail' ],
            'has_archive' => true,
            'rewrite'     => [ 'slug' => Utils::get_archive_slug() ],
        ];
        register_post_type( 'adoptable_pet', $args );

        flush_rewrite_rules();

        if ( ! wp_next_scheduled( 'rescue_sync_cron' ) ) {
            $frequency = Utils::get_option( 'frequency', 'hourly' );
            wp_schedule_event( time(), $frequency, 'rescue_sync_cron' );
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
            update_option( 'rescue_sync_last_sync', current_time( 'timestamp' ) );
            update_option( 'rescue_sync_last_status', 'missing_api_key' );
            return;
        }

        $client  = new API_Client( $api_key );
        $results = $client->get_all_available_animals();

        if ( empty( $results['data'] ) || ! is_array( $results['data'] ) ) {
            update_option( 'rescue_sync_last_sync', current_time( 'timestamp' ) );
            update_option( 'rescue_sync_last_status', 'no_data' );
            return;
        }

        foreach ( $results['data'] as $animal ) {
            $animal_id = isset( $animal['id'] ) ? intval( $animal['id'] ) : 0;
            if ( ! $animal_id ) {
                continue;
            }

            $name        = isset( $animal['attributes']['name'] ) ? $animal['attributes']['name'] : '';
            $description = isset( $animal['attributes']['descriptionText'] ) ? $animal['attributes']['descriptionText'] : '';

            $species = $animal['attributes']['species'] ?? ( $animal['attributes']['speciesString'] ?? '' );
            $breed   = $animal['attributes']['breedPrimary'] ?? ( $animal['attributes']['breedString'] ?? '' );
            $age     = $animal['attributes']['ageGroup'] ?? ( $animal['attributes']['ageString'] ?? '' );
            $gender  = $animal['attributes']['sex'] ?? '';

            $photos = [];
            if ( isset( $animal['relationships']['pictures']['data'], $results['included']['pictures'] ) && is_array( $animal['relationships']['pictures']['data'] ) ) {
                foreach ( $animal['relationships']['pictures']['data'] as $pic ) {
                    $pic_id = $pic['id'] ?? 0;
                    if ( $pic_id && isset( $results['included']['pictures'][ $pic_id ]['attributes']['urlLarge'] ) ) {
                        $photos[] = esc_url_raw( $results['included']['pictures'][ $pic_id ]['attributes']['urlLarge'] );
                    }
                }
            }

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

                update_post_meta( $post_id, self::META_SPECIES, sanitize_text_field( $species ) );
                update_post_meta( $post_id, self::META_BREED, sanitize_text_field( $breed ) );
                update_post_meta( $post_id, self::META_AGE, sanitize_text_field( $age ) );
                update_post_meta( $post_id, self::META_GENDER, sanitize_text_field( $gender ) );
                if ( ! empty( $photos ) ) {
                    update_post_meta( $post_id, self::META_PHOTOS, wp_json_encode( $photos ) );
                }

                if ( $species ) {
                    wp_set_object_terms( $post_id, sanitize_text_field( $species ), 'pet_species', false );
                }
                if ( $breed ) {
                    wp_set_object_terms( $post_id, sanitize_text_field( $breed ), 'pet_breed', false );
                }
            }
        }

        update_option( 'rescue_sync_last_sync', current_time( 'timestamp' ) );
        update_option( 'rescue_sync_last_status', 'success' );
    }
}
