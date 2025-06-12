<?php
namespace RescueSync;

class Blocks {
    public function __construct() {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    public function register_block() {
        $handle = 'rescue-sync-block';
        wp_register_script(
            $handle,
            RESCUE_SYNC_URL . 'build/block.js',
            [ 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-components' ],
            RESCUE_SYNC_VERSION,
            true
        );

        register_block_type( 'rescue-sync/adoptable-pets', [
            'editor_script'   => $handle,
            'render_callback' => [ $this, 'render_block' ],
            'attributes'      => [
                'number' => [
                    'type'    => 'number',
                    'default' => 5,
                ],
                'featured_only' => [
                    'type'    => 'boolean',
                    'default' => false,
                ],
            ],
        ] );
    }

    public function render_block( $atts = [] ) {
        $atts = shortcode_atts(
            [
                'number'       => 5,
                'featured_only'=> false,
            ],
            $atts,
            'rescue-sync/adoptable-pets'
        );

        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $atts['number'] ),
            'post_status'    => 'publish',
        ];

        if ( ! empty( $atts['featured_only'] ) ) {
            $query_args['meta_query'] = [
                [
                    'key'   => '_rescue_sync_featured',
                    'value' => '1',
                ],
            ];
        }

        $query = new \WP_Query( $query_args );
        ob_start();
        echo '<ul class="adoptable-pets-block">';
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                printf(
                    '<li><a href="%s">%s</a></li>',
                    esc_url( get_permalink() ),
                    esc_html( get_the_title() )
                );
            }
        }
        echo '</ul>';
        \wp_reset_postdata();
        return ob_get_clean();
    }
}
