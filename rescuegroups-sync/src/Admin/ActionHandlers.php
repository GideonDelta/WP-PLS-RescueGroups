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
        wp_redirect( wp_get_referer() );
        exit;
    }

    /** Reset manifest. */
    public function resetManifest() : void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'rescuegroups-sync' ) );
        }
        check_admin_referer( 'rescue_sync_reset_manifest' );
        delete_option( 'rescue_sync_manifest_ids' );
        delete_option( 'rescue_sync_manifest_timestamp' );
        wp_redirect( wp_get_referer() );
        exit;
    }
}
