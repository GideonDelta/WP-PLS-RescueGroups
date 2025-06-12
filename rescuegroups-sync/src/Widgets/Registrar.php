<?php
namespace RescueSync\Widgets;

/** Register plugin widgets. */
class Registrar {
    public function register() : void {
        add_action( 'widgets_init', function() {
            register_widget( AdoptablePets::class );
        } );
    }
}
