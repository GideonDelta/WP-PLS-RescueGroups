```php
<?php
namespace RescueSync;

class Shortcodes {
    public function __construct() {
        add_shortcode( 'adoptable_pets', [ $this, 'adoptable_pets' ] );
        add_shortcode( 'count_pets', [ $this, 'count_pets' ] );
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
                'number'        => $defaults['number'],
                'featured_only' => $defaults['featured_only'],
                'species'       => '',
                'breed'         => '',
                'orderby'       => 'date',
                'order'         => 'DESC',
            ],
            $atts,
            'adoptable_pets'
        );

        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $atts['number'] ),
            'post_status'    => 'publish',
        ];

        $tax_query = [];
        if ( ! empty( $atts['species'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'pet_species',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $atts['species'] ) ) ),
            ];
        }
        if ( ! empty( $atts['breed'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'pet_breed',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $atts['breed'] ) ) ),
            ];
        }
        if ( ! empty( $tax_query ) ) {
            $query_args['tax_query'] = $tax_query;
        }

        $orderby = strtolower( $atts['orderby'] );
        if ( ! in_array( $orderby, [ 'date', 'title', 'rand' ], true ) ) {
            $orderby = 'date';
        }
        $order = strtoupper( $atts['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $query_args['orderby'] = $orderby;
        $query_args['order']   = $order;

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
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Shortcode handler for [count_pets].
     *
     * @param array $atts Shortcode attributes.
     * @return string Translated count text.
     */
    public function count_pets( $atts = [] ) {
        $atts = shortcode_atts(
            [
                'type'   => '',
                'status' => 'adoptable',
            ],
            $atts,
            'count_pets'
        );

        $tax_query = [];
        if ( $atts['type'] ) {
            $tax_query[] = [
                'taxonomy' => 'pet_species',
                'field'    => 'slug',
                'terms'    => sanitize_title( $atts['type'] ),
            ];
        }

        $meta_query = [
            [
                'key'   => Sync::META_STATUS,
                'value' => sanitize_text_field( $atts['status'] ),
            ],
        ];

        $query = new \WP_Query([
            'post_type'      => 'adoptable_pet',
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'tax_query'      => $tax_query,
            'meta_query'     => $meta_query,
        ]);

        $count = $query->found_posts;
        \wp_reset_postdata();

        $species_name = $atts['type'] ? sanitize_text_field( $atts['type'] ) : __( 'pets', 'rescuegroups-sync' );
        $status_text  = sanitize_text_field( $atts['status'] );

        return sprintf(
            esc_html(
                _n(
                    'There is %1$d %2$s %3$s.',
                    'There are %1$d %2$s %3$s.',
                    $count,
                    'rescuegroups-sync'
                )
            ),
            $count,
            $status_text,
            $species_name
        );
    }
}
```
