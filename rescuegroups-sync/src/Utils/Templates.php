<?php
namespace RescueSync\Utils;

class Templates {
    public static function render( string $name, array $vars = [] ) {
        $file = RESCUE_SYNC_DIR . 'templates/' . $name . '.php';
        if ( ! file_exists( $file ) ) {
            return '';
        }
        ob_start();
        extract( $vars, EXTR_SKIP );
        include $file;
        return ob_get_clean();
    }
}
