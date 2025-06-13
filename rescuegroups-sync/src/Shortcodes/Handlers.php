<?php
namespace RescueSync\Shortcodes;

use RescueSync\Utils\Options;
use RescueSync\Utils\Templates;
use RescueSync\Sync\Runner;

/**
 * Shortcode handlers.
 */
class Handlers {
    public function register() : void {
        add_shortcode( 'adoptable_pets', [ $this, 'adoptablePets' ] );
        add_shortcode( 'random_pet', [ $this, 'randomPet' ] );
        add_shortcode( 'count_pets', [ $this, 'countPets' ] );
    }

    /** Handle [adoptable_pets]. */
    public function adoptablePets( array $atts = [] ) : string {
        $defaults = array_merge( Options::defaultQueryArgs(), [
            'random'  => false,
            'species' => '',
            'breed'   => '',
            'orderby' => 'date',
            'order'   => 'DESC',
        ] );
        $atts = shortcode_atts( $defaults, $atts, 'adoptable_pets' );

        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $atts['number'] ),
            'post_status'    => 'publish',
        ];

        $tax_query = [];
        if ( $atts['species'] ) {
            $tax_query[] = [
                'taxonomy' => 'pet_species',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $atts['species'] ) ) ),
            ];
        }
        if ( $atts['breed'] ) {
            $tax_query[] = [
                'taxonomy' => 'pet_breed',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $atts['breed'] ) ) ),
            ];
        }
        if ( $tax_query ) {
            $query_args['tax_query'] = $tax_query;
        }

        if ( $atts['random'] ) {
            $query_args['orderby'] = 'rand';
        } else {
            $orderby = in_array( strtolower( $atts['orderby'] ), [ 'date', 'title', 'rand' ], true ) ? strtolower( $atts['orderby'] ) : 'date';
            $order   = strtoupper( $atts['order'] ) === 'ASC' ? 'ASC' : 'DESC';
            $query_args['orderby'] = $orderby;
            $query_args['order']   = $order;
        }

        if ( $atts['featured_only'] ) {
            $query_args['meta_query'] = [
                [
                    'key'     => '_rescue_sync_featured',
                    'value'   => '1',
                    'compare' => '=',
                ],
            ];
        }

        $query = new \WP_Query( $query_args );
        $output = Templates::render( 'adoptable-pets-list', [
            'query' => $query,
            'class' => 'adoptable-pets-shortcode',
        ] );
        wp_reset_postdata();

        return $output;
    }

    /** Display a single random pet via [random_pet]. */
    public function randomPet( array $atts = [] ) : string {
        $atts['number'] = 1;
        $atts['random'] = true;
        return $this->adoptablePets( $atts );
    }

    /** Handle [count_pets]. */
    public function countPets( array $atts = [] ) : string {
        $atts = shortcode_atts( [ 'type' => '', 'status' => 'adoptable' ], $atts, 'count_pets' );

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
                'key'   => Runner::META_STATUS,
                'value' => sanitize_text_field( $atts['status'] ),
            ],
        ];

        $query = new \WP_Query( [
            'post_type'      => 'adoptable_pet',
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'tax_query'      => $tax_query,
            'meta_query'     => $meta_query,
        ] );
        $count = $query->found_posts;
        wp_reset_postdata();

        $species_name = $atts['type'] ? sanitize_text_field( $atts['type'] ) : __( 'pets', 'rescuegroups-sync' );
        $status_text  = sanitize_text_field( $atts['status'] );
        return sprintf( esc_html( _n( 'There is %1$d %2$s %3$s.', 'There are %1$d %2$s %3$s.', $count, 'rescuegroups-sync' ) ), $count, $status_text, $species_name );
    }
}
