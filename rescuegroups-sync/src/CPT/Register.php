<?php
namespace RescueSync\CPT;

use RescueSync\Utils\Options;

/**
 * Register custom post type and related taxonomies/meta.
 */
class Register {
    /**
     * Flush rewrite rules when the archive slug changes.
     *
     * @param string $old_value Previous option value.
     * @param string $value     New option value.
     */
    public static function flushRewrite( $old_value, $value ) : void {
        if ( ! is_admin() ) {
            return;
        }

        if ( $old_value !== $value ) {
            flush_rewrite_rules();
        }
    }
    /** Register hooks. */
    public function register() : void {
        add_action( 'init', [ $this, 'registerCPT' ] );
        add_action( 'init', [ $this, 'registerMeta' ] );
        add_action( 'init', [ $this, 'registerTaxonomies' ] );
    }

    /** Register CPT. */
    public function registerCPT() : void {
        $labels = [
            'name'          => __( 'Adoptable Pets', 'rescuegroups-sync' ),
            'singular_name' => __( 'Adoptable Pet', 'rescuegroups-sync' ),
        ];
        $args = [
            'labels'       => $labels,
            'public'       => true,
            'supports'     => [ 'title', 'editor', 'thumbnail' ],
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => Options::archiveSlug() ],
            'show_in_rest' => true,
        ];
        register_post_type( 'adoptable_pet', $args );
    }

    /** Register post meta. */
    public function registerMeta() : void {
        $args = [
            'type'              => 'boolean',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ];
        register_post_meta( 'adoptable_pet', '_rescue_sync_featured', $args );
        register_post_meta( 'adoptable_pet', '_rescue_sync_hidden', $args );
    }

    /** Register taxonomies. */
    public function registerTaxonomies() : void {
        $species_labels = [
            'name'          => __( 'Species', 'rescuegroups-sync' ),
            'singular_name' => __( 'Species', 'rescuegroups-sync' ),
        ];
        $species_args = [
            'labels'       => $species_labels,
            'public'       => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite'      => [ 'slug' => 'species' ],
        ];
        register_taxonomy( 'pet_species', 'adoptable_pet', $species_args );

        $breed_labels = [
            'name'          => __( 'Breeds', 'rescuegroups-sync' ),
            'singular_name' => __( 'Breed', 'rescuegroups-sync' ),
        ];
        $breed_args = [
            'labels'       => $breed_labels,
            'public'       => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite'      => [ 'slug' => 'breed' ],
        ];
        register_taxonomy( 'pet_breed', 'adoptable_pet', $breed_args );
    }
}
