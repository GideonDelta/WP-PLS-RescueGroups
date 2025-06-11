<?php
namespace RescueSync;

class CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
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
}
