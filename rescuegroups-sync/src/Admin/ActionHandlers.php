<?php
namespace RescueSync\Admin;

use RescueSync\Sync\Runner;

/**
 * Handle admin actions for manual sync and manifest reset.
 */
class ActionHandlers {
    /** @var Runner */
    private $runner;

    public function __construct( Runner $runner ) {
        $this->runner = $runner;
    }

    /** Register hooks. */
    public function register() : void {
        add_action( 'admin_post_rescue_sync_manual', [ $this, 'manualSync' ] );
        add_action( 'admin_post_rescue_sync_reset_manifest', [ $this, 'resetManifest' ] );
    }

    /** Execute manual sync. */
    public function manualSync() : void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'rescuegroups-sync' ) );
        }
        check_admin_referer( 'rescue_sync_manual' );
        $this->runner->run();
        add_settings_error(
            'rescue_sync_messages',
            'manual_sync_success',
            __( 'Sync completed successfully.', 'rescuegroups-sync' ),
            'updated'
        );
        set_transient( 'settings_errors', get_settings_errors(), 30 );
        wp_redirect( wp_get_referer() );
        exit;
    }

    /** Reset manifest. */
    public function resetManifest() : void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'rescuegroups-sync' ) );
        }
        check_admin_referer( 'rescue_sync_reset_manifest' );
        delete_option( 'rescue_sync_manifest' );
        delete_option( 'rescue_sync_manifest_timestamp' );
        add_settings_error(
            'rescue_sync_messages',
            'manifest_reset_success',
            __( 'Sync manifest reset.', 'rescuegroups-sync' ),
            'updated'
        );
        set_transient( 'settings_errors', get_settings_errors(), 30 );
        wp_redirect( wp_get_referer() );
        exit;
    }
}
