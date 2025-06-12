<?php
namespace RescueSync;

class Shortcodes {
    public function __construct() {
        add_shortcode( 'adoptable_pets', [ $this, 'adoptable_pets' ] );
    }

    /**
     * Shortcode handler for [adoptable_pets].
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function adoptable_pets( $atts = [] ) {
        $defaults = Utils::get_default_query_args();
        $atts = shortcode_atts(
            [
                'number'       => $defaults['number'],
                'featured_only'=> $defaults['featured_only'],
            ],
            $atts,
            'adoptable_pets'
        );

        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $atts['number'] ),
            'post_status'    => 'publish',
        ];

        if ( ! empty( $atts['featured_only'] ) ) {
            $query_args['meta_query'] = [
                [
                    'key'     => '_rescue_sync_featured',
                    'value'   => '1',
                    'compare' => '=',
                ],
            ];
        }

        $query = new \WP_Query( $query_args );
        ob_start();
        echo '<ul class="adoptable-pets-shortcode">';
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
