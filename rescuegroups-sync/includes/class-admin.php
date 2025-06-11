<?php
namespace RescueSync;

class Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
    }

    public function add_settings_page() {
        add_options_page( 'Rescue Sync', 'Rescue Sync', 'manage_options', 'rescue-sync', [ $this, 'render_settings_page' ] );
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>Rescue Sync Settings</h1></div>';
    }
}
