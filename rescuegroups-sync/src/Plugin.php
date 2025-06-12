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
        add_action( 'update_option_rescue_sync_archive_slug', [ CPTRegister::class, 'flushRewrite' ], 10, 2 );

        $settingsRegistrar = new SettingsRegistrar();
        $settingsPage      = new SettingsPage( $settingsRegistrar );

        ( new CPTRegister() )->register();
        ( new MetaBoxRegistrar() )->register();
        $settingsPage->registerSettings();
        $settingsPage->register();
        ( new ActionHandlers( $runner ) )->register();
        ( new WidgetRegistrar() )->register();
        ( new ShortcodeHandlers() )->register();
        // Blocks left as-is in includes/class-blocks.php if present
    }
}
