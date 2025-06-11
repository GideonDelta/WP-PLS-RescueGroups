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

        $query = new \WP_Query([
            'post_type'      => 'adoptable_pet',
            'posts_per_page' => absint( $instance['number'] ?? 5 ),
            'post_status'    => 'publish',
        ]);

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

    public function form( $instance ) {
        $title  = esc_attr( $instance['title'] ?? '' );
        $number = absint( $instance['number'] ?? 5 );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'rescuegroups-sync' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of pets to show:', 'rescuegroups-sync' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" value="<?php echo $number; ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title']  = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['number'] = absint( $new_instance['number'] ?? 5 );
        return $instance;
    }
}
