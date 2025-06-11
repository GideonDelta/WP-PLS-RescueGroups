<?php
namespace RescueSync;

class Sync {
    public function __construct() {
        add_action( 'rescue_sync_cron', [ $this, 'run' ] );
    }

    public function run() {
        // Placeholder for sync logic.
    }
}
