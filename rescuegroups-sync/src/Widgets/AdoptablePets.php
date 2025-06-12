<?php
namespace RescueSync\Widgets;

use RescueSync\Utils\Options;

/**
 * Widget displaying adoptable pets.
 */
class AdoptablePets extends \WP_Widget {
    public function __construct() {
        parent::__construct( 'adoptable_pets_widget', __( 'Adoptable Pets', 'rescuegroups-sync' ), [ 'description' => __( 'Displays latest adoptable pets.', 'rescuegroups-sync' ) ] );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        $defaults = Options::defaultQueryArgs();
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = apply_filters( 'widget_title', $instance['title'] ?? '' );
        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }
        $query_args = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $instance['number'] ),
            'post_status'    => 'publish',
        ];
        $query = new \WP_Query( array_merge( $query_args, $this->getOverrides( $instance ) ) );
        echo '<ul class="adoptable-pets-widget">';
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
            }
        }
        echo '</ul>';
        wp_reset_postdata();
        echo $args['after_widget'];
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
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'rescuegroups-sync' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of pets to show:', 'rescuegroups-sync' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" value="<?php echo $number; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'species' ); ?>"><?php esc_html_e( 'Species slugs (comma separated):', 'rescuegroups-sync' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'species' ); ?>" name="<?php echo $this->get_field_name( 'species' ); ?>" type="text" value="<?php echo $species; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'breed' ); ?>"><?php esc_html_e( 'Breed slugs (comma separated):', 'rescuegroups-sync' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'breed' ); ?>" name="<?php echo $this->get_field_name( 'breed' ); ?>" type="text" value="<?php echo $breed; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order by:', 'rescuegroups-sync' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
                <option value="date"   <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'rescuegroups-sync' ); ?></option>
                <option value="title"  <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'rescuegroups-sync' ); ?></option>
                <option value="rand"   <?php selected( $orderby, 'rand' ); ?>><?php esc_html_e( 'Random', 'rescuegroups-sync' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order:', 'rescuegroups-sync' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
                <option value="DESC" <?php selected( $order, 'DESC' ); ?>>DESC</option>
                <option value="ASC"  <?php selected( $order, 'ASC' ); ?>>ASC</option>
            </select>
        </p>
        <p>
            <label><input type="checkbox" id="<?php echo $this->get_field_id( 'featured_only' ); ?>" name="<?php echo $this->get_field_name( 'featured_only' ); ?>" value="1" <?php checked( $featured_only ); ?>> <?php esc_html_e( 'Only show featured pets', 'rescuegroups-sync' ); ?></label>
        </p>
        <p>
            <label><input type="checkbox" id="<?php echo $this->get_field_id( 'featured_first' ); ?>" name="<?php echo $this->get_field_name( 'featured_first' ); ?>" value="1" <?php checked( $featured_first ); ?>> <?php esc_html_e( 'Show featured pets first', 'rescuegroups-sync' ); ?></label>
        </p>
        <p>
            <label><input type="checkbox" id="<?php echo $this->get_field_id( 'random' ); ?>" name="<?php echo $this->get_field_name( 'random' ); ?>" value="1" <?php checked( $random ); ?>> <?php esc_html_e( 'Display randomly', 'rescuegroups-sync' ); ?></label>
        </p>
        <?php
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
