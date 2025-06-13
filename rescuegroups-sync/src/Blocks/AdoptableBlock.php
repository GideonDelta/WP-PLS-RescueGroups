<?php
namespace RescueSync\Blocks;

use RescueSync\Utils\Templates;

/**
 * Register Gutenberg block for adoptable pets.
 */
class AdoptableBlock {
    public function register() : void {
        add_action( 'init', [ $this, 'registerBlock' ] );
    }

    public function registerBlock() {
        $handle = 'rescue-sync-block';
        wp_register_script( $handle, RESCUE_SYNC_URL . 'build/block.js', [ 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-components' ], RESCUE_SYNC_VERSION, true );
        wp_set_script_translations( $handle, 'rescuegroups-sync', RESCUE_SYNC_DIR . 'languages' );
        register_block_type( 'rescue-sync/adoptable-pets', [
            'editor_script'   => $handle,
            'render_callback' => [ $this, 'renderBlock' ],
            'attributes'      => [
                'number' => [ 'type' => 'number', 'default' => 5 ],
                'featured_only' => [ 'type' => 'boolean', 'default' => false ],
            ],
        ] );
    }

    public function renderBlock( $atts = [] ) {
        $atts = shortcode_atts( [ 'number' => 5, 'featured_only' => false ], $atts, 'rescue-sync/adoptable-pets' );
        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $atts['number'] ),
            'post_status'    => 'publish',
        ];
        if ( ! empty( $atts['featured_only'] ) ) {
            $query_args['meta_query'] = [ [ 'key' => '_rescue_sync_featured', 'value' => '1' ] ];
        }
        $query = new \WP_Query( $query_args );

        $output = Templates::render( 'block-adoptable-pets', [ 'query' => $query ] );
        \wp_reset_postdata();
        return $output;
    }
}
