<?php
namespace RescueSync\Widgets;

use RescueSync\Utils\Options;
use RescueSync\Utils\Templates;

/**
 * Widget displaying adoptable pets.
 */
class AdoptablePets extends \WP_Widget {
    public function __construct() {
        parent::__construct( 'adoptable_pets_widget', __( 'Adoptable Pets', 'rescuegroups-sync' ), [ 'description' => __( 'Displays latest adoptable pets.', 'rescuegroups-sync' ) ] );
    }

    public function widget( $args, $instance ) {
        $defaults = Options::defaultQueryArgs();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title    = apply_filters( 'widget_title', $instance['title'] ?? '' );

        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $instance['number'] ),
            'post_status'    => 'publish',
        ];

        $query = new \WP_Query( array_merge( $query_args, $this->getOverrides( $instance ) ) );

        echo Templates::render( 'widget-adoptable-pets', [
            'before_widget' => $args['before_widget'],
            'after_widget'  => $args['after_widget'],
            'before_title'  => $args['before_title'],
            'after_title'   => $args['after_title'],
            'title'         => $title,
            'query'         => $query,
        ] );
        wp_reset_postdata();
    }

    protected function getOverrides( $instance ) {
        $args = [];
        $tax_query = [];
        if ( ! empty( $instance['species'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'pet_species',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $instance['species'] ) ) ),
            ];
        }
        if ( ! empty( $instance['breed'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'pet_breed',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $instance['breed'] ) ) ),
            ];
        }
        if ( $tax_query ) {
            $args['tax_query'] = $tax_query;
        }
        if ( ! empty( $instance['featured_only'] ) ) {
            $args['meta_query'][] = [
                'key'     => '_rescue_sync_featured',
                'value'   => '1',
                'compare' => '=',
            ];
        }
        $orderby = strtolower( $instance['orderby'] );
        if ( ! in_array( $orderby, [ 'date', 'title', 'rand' ], true ) ) {
            $orderby = 'date';
        }
        $order = strtoupper( $instance['order'] ) === 'ASC' ? 'ASC' : 'DESC';
        if ( ! empty( $instance['random'] ) ) {
            return [ 'orderby' => 'rand' ];
        }
        if ( ! empty( $instance['featured_first'] ) ) {
            $args['meta_key'] = '_rescue_sync_featured';
            $args['orderby']  = [ 'meta_value_num' => 'DESC', $orderby => $order ];
        } else {
            $args['orderby'] = $orderby;
            $args['order']   = $order;
        }
        return $args;
    }

    public function form( $instance ) {
        $defaults = Options::defaultQueryArgs();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title          = esc_attr( $instance['title'] ?? '' );
        $number         = absint( $instance['number'] );
        $species        = esc_attr( $instance['species'] ?? '' );
        $breed          = esc_attr( $instance['breed'] ?? '' );
        $orderby        = $instance['orderby'] ?? 'date';
        $order          = $instance['order'] ?? 'DESC';
        $featured_only  = ! empty( $instance['featured_only'] );
        $featured_first = ! empty( $instance['featured_first'] );
        $random         = ! empty( $instance['random'] );

        echo Templates::render( 'widget-adoptable-pets-form', [
            'widget'         => $this,
            'title'          => $title,
            'number'         => $number,
            'species'        => $species,
            'breed'          => $breed,
            'orderby'        => $orderby,
            'order'          => $order,
            'featured_only'  => $featured_only,
            'featured_first' => $featured_first,
            'random'         => $random,
        ] );
    }

    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title']          = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['number']         = absint( $new_instance['number'] ?? 5 );
        $instance['species']        = sanitize_text_field( $new_instance['species'] ?? '' );
        $instance['breed']          = sanitize_text_field( $new_instance['breed'] ?? '' );
        $instance['orderby']        = sanitize_key( $new_instance['orderby'] ?? 'date' );
        $instance['order']          = strtoupper( $new_instance['order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC';
        $instance['featured_only']  = ! empty( $new_instance['featured_only'] ) ? 1 : 0;
        $instance['featured_first'] = ! empty( $new_instance['featured_first'] ) ? 1 : 0;
        $instance['random']         = ! empty( $new_instance['random'] ) ? 1 : 0;
        return $instance;
    }
}
