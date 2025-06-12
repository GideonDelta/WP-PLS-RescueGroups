<?php
namespace RescueSync;

class Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post_adoptable_pet', [ $this, 'save_meta' ] );
        add_action( 'admin_post_rescue_sync_manual', [ $this, 'handle_manual_sync' ] );
    }

    public function register_settings() {
        register_setting( 'rescue_sync', 'rescue_sync_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_frequency', [
            'type'              => 'string',
            'sanitize_callback' => [ $this, 'sanitize_frequency' ],
            'default'           => 'hourly',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_last_sync', [ 'type' => 'integer' ] );
        register_setting( 'rescue_sync', 'rescue_sync_last_status', [ 'type' => 'string' ] );

        register_setting( 'rescue_sync', 'rescue_sync_archive_slug', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_title',
            'default'           => 'adopt',
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_default_number', [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 5,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_default_featured', [
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false,
        ] );

        register_setting( 'rescue_sync', 'rescue_sync_fetch_limit', [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 100,
        ] );
    }

    public function sanitize_frequency( $value ) {
        $allowed = [ 'hourly', 'twicedaily', 'daily' ];
        return in_array( $value, $allowed, true ) ? $value : 'hourly';
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
                $api_key   = Utils::get_option( 'api_key' );
                $frequency = Utils::get_option( 'frequency', 'hourly' );
                $slug      = Utils::get_option( 'archive_slug', 'adopt' );
                $number    = Utils::get_option( 'default_number', 5 );
                $featured  = Utils::get_option( 'default_featured', false );
                $limit     = Utils::get_option( 'fetch_limit', 100 );
                $last_sync = Utils::get_option( 'last_sync', 0 );
                $status    = Utils::get_option( 'last_status', '' );
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_api_key"><?php echo esc_html__( 'API Key', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <input
                                name="rescue_sync_api_key"
                                id="rescue_sync_api_key"
                                type="text"
                                value="<?php echo esc_attr( $api_key ); ?>"
                                class="regular-text"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rescue_sync_frequency"><?php echo esc_html__( 'Sync Frequency', 'rescuegroups-sync' ); ?></label>
                        </th>
                        <td>
                            <select name="rescue_sync_frequency" id="rescue_sync_frequency">
                                <option value="hourly"    <?php selected( $frequency, 'hourly' );    ?>><?php esc_html_e( 'Hourly', 'rescuegroups-sync' );    ?></option>
                                <option value="twicedaily"<?php selected( $frequency, 'twicedaily'); ?>><?php esc_html_e( 'Twice Daily', 'rescuegroups-sync'); ?></option>
                                <option value="daily"     <?php selected( $frequency, 'daily' );     ?>><?php esc_html_e( 'Daily', 'rescuegroups-sync' );     ?></option>
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
                                echo esc_html( date( 'Y-m-d H:i:s', intval( $last_sync ) ) );
                            } else {
                                esc_html_e( 'Never', 'rescuegroups-sync' );
                            }
                            if ( $status ) {
                                echo ' (' . esc_html( $status ) . ')';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:20px;">
                <?php wp_nonce_field( 'rescue_sync_manual' ); ?>
                <input type="hidden" name="action" value="rescue_sync_manual" />
                <?php submit_button( __( 'Run Sync Now', 'rescuegroups-sync' ), 'secondary' ); ?>
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
        echo '<label><input type="checkbox" name="_rescue_sync_hidden"   value="1" ' . checked( $hidden,   true, false ) . '> ' . esc_html__( 'Hidden',   'rescuegroups-sync' ) . '</label>';
        echo '</p>';
    }

    /**
     * Save the meta box selections.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['rescue_sync_meta_nonce'] ) ||
             ! wp_verify_nonce( $_POST['rescue_sync_meta_nonce'], 'rescue_sync_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $featured = isset( $_POST['_rescue_sync_featured'] ) ? '1' : '0';
        $hidden   = isset( $_POST['_rescue_sync_hidden'] )   ? '1' : '0';

        update_post_meta( $post_id, '_rescue_sync_featured', $featured );
        update_post_meta( $post_id, '_rescue_sync_hidden',   $hidden );
    }

    /**
     * Handle the manual sync request.
     */
    public function handle_manual_sync() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'rescuegroups-sync' ) );
        }
        check_admin_referer( 'rescue_sync_manual' );

        $sync = new Sync();
        $sync->run();

        wp_redirect( wp_get_referer() );
        exit;
    }
}
