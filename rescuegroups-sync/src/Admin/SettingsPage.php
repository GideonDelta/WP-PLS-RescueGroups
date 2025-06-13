<?php
namespace RescueSync\Admin;

use RescueSync\Utils\Options;
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
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Rescue Sync Settings', 'rescuegroups-sync' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'rescue_sync' );
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
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_api_key"><?php echo esc_html__( 'API Key', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_api_key" id="rescue_sync_api_key" type="password" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
                            <label for="rescue_sync_show_api_key" style="margin-left:10px;">
                                <input type="checkbox" id="rescue_sync_show_api_key" />
                                <?php esc_html_e( 'Show API Key', 'rescuegroups-sync' ); ?>
                            </label>
                            <script>
                                document.addEventListener( 'DOMContentLoaded', function () {
                                    var checkbox = document.getElementById( 'rescue_sync_show_api_key' );
                                    var field    = document.getElementById( 'rescue_sync_api_key' );
                                    if ( checkbox && field ) {
                                        checkbox.addEventListener( 'change', function () {
                                            field.type = this.checked ? 'text' : 'password';
                                        } );
                                    }
                                } );
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_frequency"><?php echo esc_html__( 'Sync Frequency', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <select name="rescue_sync_frequency" id="rescue_sync_frequency">
                                <option value="hourly"    <?php selected( $frequency, 'hourly' );    ?>><?php esc_html_e( 'Hourly',      'rescuegroups-sync' ); ?></option>
                                <option value="twicedaily"<?php selected( $frequency, 'twicedaily'); ?>><?php esc_html_e( 'Twice Daily', 'rescuegroups-sync' ); ?></option>
                                <option value="daily"     <?php selected( $frequency, 'daily' );     ?>><?php esc_html_e( 'Daily',       'rescuegroups-sync' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_fetch_limit"><?php echo esc_html__( 'Fetch Limit', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_fetch_limit" id="rescue_sync_fetch_limit" type="number" min="1" value="<?php echo esc_attr( $limit ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_store_raw">
                                <input name="rescue_sync_store_raw" id="rescue_sync_store_raw" type="checkbox" value="1" <?php checked( $store_raw ); ?> />
                                <?php echo esc_html__( 'Store Raw API Data', 'rescuegroups-sync' ); ?>
                            </label>
                        </th>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_raw_retention"><?php echo esc_html__( 'Raw Data Retention (days)', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_raw_retention" id="rescue_sync_raw_retention" type="number" min="0" value="<?php echo esc_attr( $raw_retention ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_species_filter"><?php echo esc_html__( 'Species Filter', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_species_filter" id="rescue_sync_species_filter" type="text" value="<?php echo esc_attr( $species_filter ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_status_filter"><?php echo esc_html__( 'Status Filter', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_status_filter" id="rescue_sync_status_filter" type="text" value="<?php echo esc_attr( $status_filter ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_archive_slug"><?php echo esc_html__( 'Archive Slug', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_archive_slug" id="rescue_sync_archive_slug" type="text" value="<?php echo esc_attr( $slug ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_default_number"><?php echo esc_html__( 'Default Number', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_default_number" id="rescue_sync_default_number" type="number" min="1" value="<?php echo esc_attr( $number ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_default_featured">
                                <input name="rescue_sync_default_featured" id="rescue_sync_default_featured" type="checkbox" value="1" <?php checked( $featured ); ?> />
                                <?php echo esc_html__( 'Featured Only by Default', 'rescuegroups-sync' ); ?>
                            </label>
                        </th>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__( 'Last Sync', 'rescuegroups-sync' ); ?></th>
                        <td>
                            <?php
                            if ( $last_sync ) {
                                echo esc_html( wp_date( 'Y-m-d H:i:s', intval( $last_sync ) ) );
                            } else {
                                esc_html_e( 'Never', 'rescuegroups-sync' );
                            }
                            if ( $status ) {
                                echo ' (' . esc_html( $status ) . ')';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__( 'Last Run Time', 'rescuegroups-sync' ); ?></th>
                        <td><?php echo esc_html( $last_runtime ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__( 'Last Peak Memory', 'rescuegroups-sync' ); ?></th>
                        <td><?php echo esc_html( $last_memory ); ?></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:20px;">
                <?php wp_nonce_field( 'rescue_sync_manual' ); ?>
                <input type="hidden" name="action" value="rescue_sync_manual" />
                <?php submit_button( __( 'Run Sync Now', 'rescuegroups-sync' ), 'secondary' ); ?>
            </form>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:20px;">
                <?php wp_nonce_field( 'rescue_sync_reset_manifest' ); ?>
                <input type="hidden" name="action" value="rescue_sync_reset_manifest" />
                <?php submit_button( __( 'Reset Manifest', 'rescuegroups-sync' ), 'delete' ); ?>
            </form>
        </div>
        <?php
    }

}
