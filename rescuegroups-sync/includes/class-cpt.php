<?php
namespace RescueSync;

class CPT {
    public function __construct() {
        add_action( 'init', [ $this, 'register_cpt' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
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

    public function register_taxonomies() {
        $species_labels = [
            'name' => 'Species',
            'singular_name' => 'Species',
        ];
        register_taxonomy( 'pet_species', 'adoptable_pet', [
            'labels' => $species_labels,
            'public' => true,
            'rewrite' => [ 'slug' => 'species' ],
        ] );

        $breed_labels = [
            'name' => 'Breeds',
            'singular_name' => 'Breed',
        ];
        register_taxonomy( 'pet_breed', 'adoptable_pet', [
            'labels' => $breed_labels,
            'public' => true,
            'rewrite' => [ 'slug' => 'breed' ],
        ] );
    }
}
