<?php
namespace RescueSync;

class CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
        add_action( 'init', [ $this, 'register_meta' ] );
    }

    public function register_cpt() {
        $labels = [
            'name' => 'Adoptable Pets',
            'singular_name' => 'Adoptable Pet',
        ];
        $args = [
            'labels' => $labels,
            'public' => true,
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'has_archive' => true,
            'rewrite' => [ 'slug' => 'adopt' ],
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
        register_post_meta( 'adoptable_pet', '_rescue_sync_hidden', $args );
    }
}
