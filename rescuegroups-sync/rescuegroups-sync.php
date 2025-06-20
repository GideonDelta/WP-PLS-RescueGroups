<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Plugin Name: RescueGroups Sync
 * Plugin URI: https://purelightstudios.com
 * Description: Syncs adoptable animals from RescueGroups.org and displays them as custom posts.
 * Version: 0.1.0
 * Requires at least: 5.6
 * Requires PHP: 7.2
 * Author: Pure Light Studios, LLC
 * Author URI: https://purelightstudios.com
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

require_once RESCUE_SYNC_DIR . 'src/autoload.php';

register_activation_hook( __FILE__, [ 'RescueSync\\Sync\\Runner', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'RescueSync\\Sync\\Runner', 'deactivate' ] );

// Initialize components.
add_action( 'plugins_loaded', function() {
    ( new RescueSync\Plugin() )->register();
    if ( class_exists( 'RescueSync\\Blocks\\AdoptableBlock' ) ) {
        ( new RescueSync\Blocks\AdoptableBlock() )->register();
    }
} );
