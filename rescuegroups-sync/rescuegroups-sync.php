<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
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

function rescuegroups_sync_load_textdomain() {
    load_plugin_textdomain( 'rescuegroups-sync', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
// Load translations before other components are initialized.
add_action( 'plugins_loaded', 'rescuegroups_sync_load_textdomain', 1 );

spl_autoload_register( function( $class ) {
    if ( 0 !== strpos( $class, 'RescueSync\\' ) ) {
        return;
    }

    $relative = str_replace( 'RescueSync\\', '', $class );
    $relative = str_replace( '\\', '-', $relative );
    $relative = str_replace( '_', '-', $relative );
    $relative = preg_replace( '/([a-z])([A-Z])/', '$1-$2', $relative );
    $relative = strtolower( $relative );

    $path = RESCUE_SYNC_DIR . 'includes/class-' . $relative . '.php';
    if ( file_exists( $path ) ) {
        require $path;
    }
});

register_activation_hook( __FILE__, [ 'RescueSync\\Sync', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'RescueSync\\Sync', 'deactivate' ] );

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
    if ( class_exists( 'RescueSync\\Shortcodes' ) ) {
        new RescueSync\Shortcodes();
    }
} );
