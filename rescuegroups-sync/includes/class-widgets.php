<?php
namespace RescueSync;

class Widgets {
    public function __construct() {
        add_action( 'widgets_init', [ $this, 'register_widgets' ] );
    }

    public function register_widgets() {
        register_widget( Adoptable_Pets_Widget::class );
    }
}

class Adoptable_Pets_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'adoptable_pets_widget',
            __( 'Adoptable Pets', 'rescuegroups-sync' ),
            [ 'description' => __( 'Displays latest adoptable pets.', 'rescuegroups-sync' ) ]
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        $title = apply_filters( 'widget_title', $instance['title'] ?? '' );
        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        $args_query = [
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $instance['number'] ?? 5 ),
            'post_status'    => 'publish',
        ];
        $query = new \WP_Query( array_merge( $args_query, $this->get_query_overrides( $instance ) ) );

        echo '<ul class="adoptable-pets-widget">';
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

        echo $args['after_widget'];
    }

    /**
     * Determine query modifiers based on widget settings.
     *
     * @param array $instance Widget instance settings.
     * @return array Arguments to merge into WP_Query args.
     */
    protected function get_query_overrides( $instance ) {
        if ( ! empty( $instance['featured_only'] ) ) {
            return [
                'meta_query' => [
                    [
                        'key'     => '_rescue_sync_featured',
                        'value'   => '1',
                        'compare' => '=',
                    ],
                ],
            ];
        }

        if ( ! empty( $instance['featured_first'] ) ) {
            return [
                'meta_key' => '_rescue_sync_featured',
                'orderby'  => [
                    'meta_value_num' => 'DESC',
                    'date'           => 'DESC',
                ],
            ];
        }

        return [];
    }

    public function form( $instance ) {
        $title  = esc_attr( $instance['title'] ?? '' );
        $number = absint( $instance['number'] ?? 5 );
        $featured_only  = ! empty( $instance['featured_only'] );
        $featured_first = ! empty( $instance['featured_first'] );
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
            <label><input type="checkbox" id="<?php echo $this->get_field_id( 'featured_only' ); ?>" name="<?php echo $this->get_field_name( 'featured_only' ); ?>" value="1" <?php checked( $featured_only ); ?>> <?php esc_html_e( 'Only show featured pets', 'rescuegroups-sync' ); ?></label>
        </p>
        <p>
            <label><input type="checkbox" id="<?php echo $this->get_field_id( 'featured_first' ); ?>" name="<?php echo $this->get_field_name( 'featured_first' ); ?>" value="1" <?php checked( $featured_first ); ?>> <?php esc_html_e( 'Show featured pets first', 'rescuegroups-sync' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title']  = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['number'] = absint( $new_instance['number'] ?? 5 );
        $instance['featured_only']  = ! empty( $new_instance['featured_only'] ) ? 1 : 0;
        $instance['featured_first'] = ! empty( $new_instance['featured_first'] ) ? 1 : 0;
        return $instance;
    }
}
