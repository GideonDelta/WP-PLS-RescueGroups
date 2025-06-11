<?php
namespace RescueSync;

class Utils {
    public static function get_option( $key, $default = '' ) {
        return get_option( 'rescue_sync_' . $key, $default );
    }

    public static function get_api_key() {
        return self::get_option( 'api_key' );
    }
}
