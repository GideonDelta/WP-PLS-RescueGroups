<?php
namespace RescueSync;

class CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
        add_action( 'init', [ $this, 'register_meta' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
    }

    public function register_cpt() {
        $labels = [
            'name'          => __( 'Adoptable Pets', 'rescuegroups-sync' ),
            'singular_name' => __( 'Adoptable Pet', 'rescuegroups-sync' ),
        ];
        $slug   = Utils::get_archive_slug();
        $args = [
            'labels'       => $labels,
            'public'       => true,
            'supports'     => [ 'title', 'editor', 'thumbnail' ],
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => $slug ],
            'show_in_rest' => true,
        ];
        register_post_type( 'adoptable_pet', $args );
    }

    /**
     * Register custom meta fields for the adoptable_pet post type.
     */
    public function register_meta() {
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
        register_post_meta( 'adoptable_pet', '_rescue_sync_hidden',   $args );
    }

    /**
     * Register taxonomies used by the adoptable_pet post type.
     */
    public function register_taxonomies() {
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
