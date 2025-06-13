<?php
namespace RescueSync\Admin;

use RescueSync\Utils\Options;
use RescueSync\Utils\Templates;
use RescueSync\Sync\Runner;

/**
 * Settings page renderer and handler.
 */
class SettingsPage {
    /**
     * @var SettingsRegistrar
     */
    private $registrar;


    /**
     * Constructor.
     *
     * @param SettingsRegistrar $registrar Registrar instance.
     */
    public function __construct( SettingsRegistrar $registrar ) {
        $this->registrar = $registrar;
    }

    /**
     * Add menu and hooks.
     */
    public function register() : void {
        add_action( 'admin_menu', [ $this, 'addPage' ] );
    }

    /**
     * Register settings via registrar.
     */
    public function registerSettings() : void {
        add_action( 'admin_init', [ $this->registrar, 'register' ] );
    }

    /**
     * Add options page.
     */
    public function addPage() : void {
        add_options_page( __( 'Rescue Sync', 'rescuegroups-sync' ), __( 'Rescue Sync', 'rescuegroups-sync' ), 'manage_options', 'rescue-sync', [ $this, 'render' ] );
    }

    /**
     * Render settings page.
     */
    public function render() : void {
        $api_key        = Options::get( 'api_key' );
        $frequency      = Options::get( 'frequency', 'hourly' );
        $slug           = Options::get( 'archive_slug', 'adopt' );
        $number         = Options::get( 'default_number', 5 );
        $featured       = Options::get( 'default_featured', false );
        $limit          = Options::get( 'fetch_limit', 100 );
        $species_filter = Options::get( 'species_filter', '' );
        $status_filter  = Options::get( 'status_filter', '' );
        $store_raw      = Options::get( 'store_raw', false );
        $raw_retention  = Options::get( 'raw_retention', 30 );
        $last_sync      = Options::get( 'last_sync', 0 );
        $status         = Options::get( 'last_status', '' );
        $last_runtime   = Options::get( 'last_runtime', '' );
        $last_memory    = Options::get( 'last_memory', '' );

        echo Templates::render( 'settings-page', [
            'api_key'        => $api_key,
            'frequency'      => $frequency,
            'slug'           => $slug,
            'number'         => $number,
            'featured'       => $featured,
            'limit'          => $limit,
            'species_filter' => $species_filter,
            'status_filter'  => $status_filter,
            'store_raw'      => $store_raw,
            'raw_retention'  => $raw_retention,
            'last_sync'      => $last_sync,
            'status'         => $status,
            'last_runtime'   => $last_runtime,
            'last_memory'    => $last_memory,
        ] );
    }

}
