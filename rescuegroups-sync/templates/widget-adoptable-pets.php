<?php echo $before_widget; ?>
<?php if ( $title ) { echo $before_title . esc_html( $title ) . $after_title; } ?>
<ul class="adoptable-pets-widget">
<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
    <li><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></li>
<?php endwhile; endif; ?>
</ul>
<?php echo $after_widget; ?>
