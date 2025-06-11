<?php
namespace RescueSync;

class Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_adoptable_pet', [ $this, 'save_meta' ] );
    }

    public function register_settings() {
        register_setting( 'rescue_sync', 'rescue_sync_api_key', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ] );
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Rescue Sync', 'rescuegroups-sync' ),
            __( 'Rescue Sync', 'rescuegroups-sync' ),
            'manage_options',
            'rescue-sync',
            [ $this, 'render_settings_page' ]
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Rescue Sync Settings', 'rescuegroups-sync' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'rescue_sync' );
                $api_key = Utils::get_option( 'api_key' );
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_api_key"><?php echo esc_html__( 'API Key', 'rescuegroups-sync' ); ?></label>
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

    /**
     * Register the meta box for adoptable pets.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'rescue_sync_flags',
            __( 'Rescue Sync Options', 'rescuegroups-sync' ),
            [ $this, 'render_meta_box' ],
            'adoptable_pet',
            'side'
        );
    }

    /**
     * Render the meta box contents.
     *
     * @param \WP_Post $post Current post object.
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'rescue_sync_meta', 'rescue_sync_meta_nonce' );

        $featured = (bool) get_post_meta( $post->ID, '_rescue_sync_featured', true );
        $hidden   = (bool) get_post_meta( $post->ID, '_rescue_sync_hidden', true );

        echo '<p>';
        echo '<label><input type="checkbox" name="_rescue_sync_featured" value="1" ' . checked( $featured, true, false ) . '> ' . esc_html__( 'Featured', 'rescuegroups-sync' ) . '</label><br />';
        echo '<label><input type="checkbox" name="_rescue_sync_hidden" value="1" ' . checked( $hidden, true, false ) . '> ' . esc_html__( 'Hidden', 'rescuegroups-sync' ) . '</label>';
        echo '</p>';
    }

    /**
     * Save the meta box selections.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['rescue_sync_meta_nonce'] ) || ! wp_verify_nonce( $_POST['rescue_sync_meta_nonce'], 'rescue_sync_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $featured = isset( $_POST['_rescue_sync_featured'] ) ? '1' : '0';
        $hidden   = isset( $_POST['_rescue_sync_hidden'] ) ? '1' : '0';

        update_post_meta( $post_id, '_rescue_sync_featured', $featured );
        update_post_meta( $post_id, '_rescue_sync_hidden', $hidden );
    }
}
