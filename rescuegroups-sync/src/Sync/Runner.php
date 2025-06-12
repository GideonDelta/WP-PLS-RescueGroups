<?php
namespace RescueSync\Sync;

use RescueSync\API\Client;
use RescueSync\Utils\Options;

/**
 * Execute synchronization with the RescueGroups API.
 */
class Runner {
    const META_SPECIES = '_rescue_sync_species';
    const META_BREED   = '_rescue_sync_breed';
    const META_AGE     = '_rescue_sync_age';
    const META_GENDER  = '_rescue_sync_gender';
    const META_PHOTOS  = '_rescue_sync_photos';
    const META_STATUS  = '_rescue_sync_status';

    /** @var Client */
    private $client;

    /**
     * Schedule cron event on activation.
     */
    public static function activate() : void {
        $labels = [ 'name' => 'Adoptable Pets', 'singular_name' => 'Adoptable Pet' ];
        $args   = [
            'labels'      => $labels,
            'public'      => true,
            'supports'    => [ 'title', 'editor', 'thumbnail' ],
            'has_archive' => true,
            'rewrite'     => [ 'slug' => Options::archiveSlug() ],
        ];
        register_post_type( 'adoptable_pet', $args );
        flush_rewrite_rules();
        if ( ! wp_next_scheduled( 'rescue_sync_cron' ) ) {
            $frequency = Options::get( 'frequency', 'hourly' );
            wp_schedule_event( time(), $frequency, 'rescue_sync_cron' );
        }
    }

    /** Clear cron on deactivation. */
    public static function deactivate() : void {
        wp_clear_scheduled_hook( 'rescue_sync_cron' );
    }

    /**
     * Reschedule cron when the frequency option changes.
     *
     * @param string $old_value Previous value.
     * @param string $value     New value.
     */
    public static function updateSchedule( $old_value, $value ) : void {
        wp_clear_scheduled_hook( 'rescue_sync_cron' );
        if ( $value ) {
            wp_schedule_event( time(), sanitize_text_field( $value ), 'rescue_sync_cron' );
        }
    }

    /**
     * Constructor.
     *
     * @param Client $client API client.
     */
    public function __construct( Client $client ) {
        $this->client = $client;
        add_action( 'rescue_sync_cron', [ $this, 'run' ] );
    }

    /**
     * Run the sync process.
     */
    public function run() : void {
        $start_time = microtime( true );
        $params = [];
        $species_filter = Options::get( 'species_filter', '' );
        $status_filter  = Options::get( 'status_filter', '' );
        if ( $species_filter ) {
            $params['species'] = $species_filter;
        }
        if ( $status_filter ) {
            $params['status'] = $status_filter;
        }

        $store_raw     = (bool) Options::get( 'store_raw', false );
        $raw_retention = absint( Options::get( 'raw_retention', 30 ) );

        $results = $this->client->fetchAll( $params );

        if ( empty( $results['data'] ) || ! is_array( $results['data'] ) ) {
            update_option( 'rescue_sync_last_sync', current_time( 'timestamp' ) );
            update_option( 'rescue_sync_last_status', 'no_data' );
            return;
        }

        $manifest = get_option( 'rescue_sync_manifest', [] );
        if ( ! is_array( $manifest ) ) {
            $manifest = [];
        }

        foreach ( $results['data'] as $animal ) {
            $animal_id = isset( $animal['id'] ) ? intval( $animal['id'] ) : 0;
            if ( ! $animal_id ) {
                continue;
            }
            $animal_hash = md5( wp_json_encode( $animal ) );
            if ( isset( $manifest[ $animal_id ] ) && $manifest[ $animal_id ] === $animal_hash ) {
                continue;
            }

            $name        = $animal['attributes']['name'] ?? '';
            $description = $animal['attributes']['descriptionText'] ?? '';
            $species = $animal['attributes']['species'] ?? ( $animal['attributes']['speciesString'] ?? '' );
            $breed   = $animal['attributes']['breedPrimary'] ?? ( $animal['attributes']['breedString'] ?? '' );
            $age     = $animal['attributes']['ageGroup'] ?? ( $animal['attributes']['ageString'] ?? '' );
            $gender  = $animal['attributes']['sex'] ?? '';
            $status  = $animal['attributes']['status'] ?? ( $animal['attributes']['statusString'] ?? 'adoptable' );

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
                if ( get_post_meta( $post_id, '_rescue_sync_hidden', true ) ) {
                    continue;
                }
                $post_args['ID'] = $post_id;
            }

            $post_id = wp_insert_post( $post_args );
            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, 'rescuegroups_id', $animal_id );
                if ( $store_raw ) {
                    $trimmed = [
                        'id'         => $animal_id,
                        'attributes' => $animal['attributes'] ?? [],
                    ];
                    if ( isset( $animal['relationships'] ) ) {
                        $trimmed['relationships'] = $animal['relationships'];
                    }
                    update_post_meta( $post_id, 'rescuegroups_raw', wp_json_encode( $trimmed ) );
                }
                update_post_meta( $post_id, self::META_SPECIES, sanitize_text_field( $species ) );
                update_post_meta( $post_id, self::META_BREED, sanitize_text_field( $breed ) );
                update_post_meta( $post_id, self::META_AGE, sanitize_text_field( $age ) );
                update_post_meta( $post_id, self::META_GENDER, sanitize_text_field( $gender ) );
                update_post_meta( $post_id, self::META_STATUS, sanitize_text_field( $status ) );
                if ( ! empty( $photos ) ) {
                    update_post_meta( $post_id, self::META_PHOTOS, wp_json_encode( $photos ) );
                }
                if ( $species ) {
                    wp_set_object_terms( $post_id, sanitize_text_field( $species ), 'pet_species', false );
                }
                if ( $breed ) {
                    wp_set_object_terms( $post_id, sanitize_text_field( $breed ), 'pet_breed', false );
                }
                $manifest[ $animal_id ] = $animal_hash;
            }
        }

        update_option( 'rescue_sync_manifest', $manifest );
        update_option( 'rescue_sync_last_sync', current_time( 'timestamp' ) );
        update_option( 'rescue_sync_last_status', 'success' );

        if ( $raw_retention > 0 ) {
            $this->pruneRawMeta( $raw_retention );
        }

        $runtime = sprintf( '%.2f s', microtime( true ) - $start_time );
        $memory  = size_format( memory_get_peak_usage(), 2 );
        update_option( 'rescue_sync_last_runtime', $runtime );
        update_option( 'rescue_sync_last_memory', $memory );
    }

    /**
     * Delete raw meta older than the given number of days.
     *
     * @param int $days Retention period.
     */
    private function pruneRawMeta( int $days ) : void {
        $query = new \WP_Query([
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_key'       => 'rescuegroups_raw',
            'date_query'     => [
                [
                    'column' => 'post_modified_gmt',
                    'before' => gmdate( 'Y-m-d', time() - $days * DAY_IN_SECONDS ),
                ],
            ],
        ]);

        foreach ( $query->posts as $post_id ) {
            delete_post_meta( $post_id, 'rescuegroups_raw' );
        }
    }
}
