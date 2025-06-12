<?php
namespace RescueSync;

class Utils {
    public static function get_option( $key, $default = '' ) {
        return get_option( 'rescue_sync_' . $key, $default );
    }

    public static function get_api_key() {
        return self::get_option( 'api_key' );
    }

    /**
     * Retrieve the archive slug for the adoptable_pet post type.
     *
     * @return string
     */
    public static function get_archive_slug() {
        $slug = self::get_option( 'archive_slug', 'adopt' );
        $slug = sanitize_title( $slug );
        return $slug ? $slug : 'adopt';
    }

    /**
     * Get default query arguments used by shortcodes and widgets.
     *
     * @return array
     */
    public static function get_default_query_args() {
        return [
            'number'       => absint( self::get_option( 'default_number', 5 ) ),
            'featured_only'=> (bool) self::get_option( 'default_featured', false ),
        ];
    }
}
