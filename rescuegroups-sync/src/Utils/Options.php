<?php
namespace RescueSync\Utils;

/**
 * Helper for plugin options.
 */
class Options {
    /**
     * Get a plugin option value.
     *
     * @param string $key     Option key without prefix.
     * @param mixed  $default Default value.
     * @return mixed
     */
    public static function get( string $key, $default = '' ) {
        return get_option( 'rescue_sync_' . $key, $default );
    }

    /**
     * Retrieve the adoptable_pet archive slug.
     *
     * @return string
     */
    public static function archiveSlug() : string {
        $slug = sanitize_title( self::get( 'archive_slug', 'adopt' ) );
        return $slug ? $slug : 'adopt';
    }

    /**
     * Default query arguments for widgets and shortcodes.
     *
     * @return array
     */
    public static function defaultQueryArgs() : array {
        return [
            'number'       => absint( self::get( 'default_number', 5 ) ),
            'featured_only'=> (bool) self::get( 'default_featured', false ),
        ];
    }
}
