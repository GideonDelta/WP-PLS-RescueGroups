<?php
namespace RescueSync\Admin;

/**
 * Register plugin settings.
 */
class SettingsRegistrar {
    /**
     * Register WordPress options for plugin settings.
     */
    public function register() : void {
        register_setting( 'rescue_sync', 'rescue_sync_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_frequency', [
            'type'              => 'string',
            'sanitize_callback' => [ $this, 'sanitizeFrequency' ],
            'default'           => 'hourly',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_last_sync', [ 'type' => 'integer' ] );
        register_setting( 'rescue_sync', 'rescue_sync_last_status', [ 'type' => 'string' ] );

        register_setting( 'rescue_sync', 'rescue_sync_archive_slug', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_title',
            'default'           => 'adopt',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_default_number', [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 5,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_default_featured', [
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_fetch_limit', [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 100,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_store_raw', [
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_raw_retention', [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 30,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_last_runtime', [
            'type' => 'string',
            'default' => '',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_last_memory', [
            'type' => 'string',
            'default' => '',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_species_filter', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_status_filter', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_manifest_ids', [
            'type'              => 'array',
            'sanitize_callback' => 'wp_parse_id_list',
            'default'           => [],
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_manifest_timestamp', [
            'type'    => 'integer',
            'default' => 0,
        ] );
    }

    /**
     * Sanitize frequency value.
     *
     * @param string $value Frequency slug.
     * @return string Sanitized value.
     */
    public function sanitizeFrequency( string $value ) : string {
        $allowed = [ 'hourly', 'twicedaily', 'daily' ];
        return in_array( $value, $allowed, true ) ? $value : 'hourly';
    }
}
