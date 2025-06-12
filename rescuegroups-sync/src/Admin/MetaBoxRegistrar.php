<?php
namespace RescueSync\Admin;

/**
 * Manage meta boxes for adoptable pets.
 */
class MetaBoxRegistrar {
    /**
     * Register actions.
     */
    public function register() : void {
        add_action( 'add_meta_boxes', [ $this, 'addMetaBoxes' ] );
        add_action( 'save_post_adoptable_pet', [ $this, 'saveMeta' ] );
    }

    /**
     * Register the meta box.
     */
    public function addMetaBoxes() : void {
        add_meta_box( 'rescue_sync_flags', __( 'Rescue Sync Options', 'rescuegroups-sync' ), [ $this, 'render' ], 'adoptable_pet', 'side' );
    }

    /**
     * Render meta box content.
     *
     * @param \WP_Post $post Current post.
     */
    public function render( $post ) : void {
        wp_nonce_field( 'rescue_sync_meta', 'rescue_sync_meta_nonce' );
        $featured = (bool) get_post_meta( $post->ID, '_rescue_sync_featured', true );
        $hidden   = (bool) get_post_meta( $post->ID, '_rescue_sync_hidden', true );
        echo '<p>';
        echo '<label><input type="checkbox" name="_rescue_sync_featured" value="1" ' . checked( $featured, true, false ) . '> ' . esc_html__( 'Featured', 'rescuegroups-sync' ) . '</label><br />';
        echo '<label><input type="checkbox" name="_rescue_sync_hidden"   value="1" ' . checked( $hidden, true, false ) . '> ' . esc_html__( 'Hidden', 'rescuegroups-sync' ) . '</label>';
        echo '</p>';
    }

    /**
     * Save meta box values.
     *
     * @param int $post_id Post ID.
     */
    public function saveMeta( int $post_id ) : void {
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
        update_post_meta( $post_id, '_rescue_sync_hidden',   $hidden );
    }
}
