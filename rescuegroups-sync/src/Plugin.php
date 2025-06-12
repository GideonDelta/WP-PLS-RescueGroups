<?php
namespace RescueSync;

use RescueSync\API\Client;
use RescueSync\Admin\SettingsRegistrar;
use RescueSync\Admin\SettingsPage;
use RescueSync\Admin\MetaBoxRegistrar;
use RescueSync\Admin\ActionHandlers;
use RescueSync\CPT\Register as CPTRegister;
use RescueSync\Widgets\Registrar as WidgetRegistrar;
use RescueSync\Shortcodes\Handlers as ShortcodeHandlers;
use RescueSync\Utils\Options;
use RescueSync\Sync\Runner;

/**
 * Bootstrap the plugin components.
 */
class Plugin {
    public function register() : void {
        $api_key = Options::get( 'api_key' );
        $client  = new Client( $api_key );
        $runner  = new Runner( $client );

        add_action( 'update_option_rescue_sync_frequency', [ Runner::class, 'updateSchedule' ], 10, 2 );

        $settingsRegistrar = new SettingsRegistrar();
        ( new CPTRegister() )->register();
        ( new MetaBoxRegistrar() )->register();
        $settingsRegistrar->register();
        ( new SettingsPage( $settingsRegistrar ) )->register();
        ( new ActionHandlers( $runner ) )->register();
        ( new WidgetRegistrar() )->register();
        ( new ShortcodeHandlers() )->register();
        // Blocks left as-is in includes/class-blocks.php if present
    }
}
