<p>
    <label for="<?php echo $widget->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'rescuegroups-sync' ); ?></label>
    <input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>">
</p>
<p>
    <label for="<?php echo $widget->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of pets to show:', 'rescuegroups-sync' ); ?></label>
    <input id="<?php echo $widget->get_field_id( 'number' ); ?>" name="<?php echo $widget->get_field_name( 'number' ); ?>" type="number" min="1" value="<?php echo $number; ?>">
</p>
<p>
    <label for="<?php echo $widget->get_field_id( 'species' ); ?>"><?php esc_html_e( 'Species slugs (comma separated):', 'rescuegroups-sync' ); ?></label>
    <input class="widefat" id="<?php echo $widget->get_field_id( 'species' ); ?>" name="<?php echo $widget->get_field_name( 'species' ); ?>" type="text" value="<?php echo $species; ?>">
</p>
<p>
    <label for="<?php echo $widget->get_field_id( 'breed' ); ?>"><?php esc_html_e( 'Breed slugs (comma separated):', 'rescuegroups-sync' ); ?></label>
    <input class="widefat" id="<?php echo $widget->get_field_id( 'breed' ); ?>" name="<?php echo $widget->get_field_name( 'breed' ); ?>" type="text" value="<?php echo $breed; ?>">
</p>
<p>
    <label for="<?php echo $widget->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order by:', 'rescuegroups-sync' ); ?></label>
    <select id="<?php echo $widget->get_field_id( 'orderby' ); ?>" name="<?php echo $widget->get_field_name( 'orderby' ); ?>">
        <option value="date"   <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'rescuegroups-sync' ); ?></option>
        <option value="title"  <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'rescuegroups-sync' ); ?></option>
        <option value="rand"   <?php selected( $orderby, 'rand' ); ?>><?php esc_html_e( 'Random', 'rescuegroups-sync' ); ?></option>
    </select>
</p>
<p>
    <label for="<?php echo $widget->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order:', 'rescuegroups-sync' ); ?></label>
    <select id="<?php echo $widget->get_field_id( 'order' ); ?>" name="<?php echo $widget->get_field_name( 'order' ); ?>">
        <option value="DESC" <?php selected( $order, 'DESC' ); ?>>DESC</option>
        <option value="ASC"  <?php selected( $order, 'ASC' ); ?>>ASC</option>
    </select>
</p>
<p>
    <label><input type="checkbox" id="<?php echo $widget->get_field_id( 'featured_only' ); ?>" name="<?php echo $widget->get_field_name( 'featured_only' ); ?>" value="1" <?php checked( $featured_only ); ?>> <?php esc_html_e( 'Only show featured pets', 'rescuegroups-sync' ); ?></label>
</p>
<p>
    <label><input type="checkbox" id="<?php echo $widget->get_field_id( 'featured_first' ); ?>" name="<?php echo $widget->get_field_name( 'featured_first' ); ?>" value="1" <?php checked( $featured_first ); ?>> <?php esc_html_e( 'Show featured pets first', 'rescuegroups-sync' ); ?></label>
</p>
<p>
    <label><input type="checkbox" id="<?php echo $widget->get_field_id( 'random' ); ?>" name="<?php echo $widget->get_field_name( 'random' ); ?>" value="1" <?php checked( $random ); ?>> <?php esc_html_e( 'Display randomly', 'rescuegroups-sync' ); ?></label>
</p>
