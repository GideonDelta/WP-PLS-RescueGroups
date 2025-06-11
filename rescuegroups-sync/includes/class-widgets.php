<?php
namespace RescueSync;

class Widgets {
    public function __construct() {
        add_action( 'widgets_init', [ $this, 'register_widgets' ] );
    }

    public function register_widgets() {
        // Placeholder for widget registration.
    }
}
