<?php
/**
 * Plugin Name: RescueGroups Sync
 * Plugin URI: https://example.com/rescuegroups-sync
 * Description: Syncs adoptable animals from RescueGroups.org and displays them as custom posts.
 * Version: 0.1.0
 * Requires at least: 5.6
 * Requires PHP: 7.2
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rescuegroups-sync
 * Domain Path: /languages
 */

define( 'RESCUE_SYNC_VERSION', '0.1.0' );

define( 'RESCUE_SYNC_DIR', plugin_dir_path( __FILE__ ) );
define( 'RESCUE_SYNC_URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register( function( $class ) {
    if ( 0 !== strpos( $class, 'RescueSync\\' ) ) {
        return;
    }
    $path = RESCUE_SYNC_DIR . 'includes/' . str_replace( '\\', '/', strtolower( str_replace( 'RescueSync\\', '', $class ) ) ) . '.php';
    if ( file_exists( $path ) ) {
        require $path;
    }
});

// Initialize components.
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'RescueSync\\CPT' ) ) {
        new RescueSync\CPT();
    }
    if ( class_exists( 'RescueSync\\Admin' ) ) {
        new RescueSync\Admin();
    }
    if ( class_exists( 'RescueSync\\Sync' ) ) {
        new RescueSync\Sync();
    }
    if ( class_exists( 'RescueSync\\Widgets' ) ) {
        new RescueSync\Widgets();
    }
} );
