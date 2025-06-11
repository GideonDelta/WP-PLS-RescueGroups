<?php
namespace RescueSync;

class Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function register_settings() {
        register_setting( 'rescue_sync', 'rescue_sync_api_key', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ] );
    }

    public function add_settings_page() {
        add_options_page( 'Rescue Sync', 'Rescue Sync', 'manage_options', 'rescue-sync', [ $this, 'render_settings_page' ] );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( 'Rescue Sync Settings' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'rescue_sync' );
                $api_key = Utils::get_option( 'api_key' );
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_api_key"><?php echo esc_html( 'API Key' ); ?></label>
                        </th>
                        <td>
                            <input name="rescue_sync_api_key" id="rescue_sync_api_key" type="text" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
