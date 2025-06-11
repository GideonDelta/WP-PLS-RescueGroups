<?php
namespace RescueSync;

class API_Client {
    protected $api_key;

    public function __construct( $api_key ) {
        $this->api_key = $api_key;
    }

    public function get_available_animals( $page = 1 ) {
        $url = 'https://api.rescuegroups.org/v5/public/animals/search/available?limit=100&page=' . intval( $page );
        $response = wp_remote_get( $url, [
            'headers' => [ 'x-api-key' => $this->api_key ],
        ] );

        if ( is_wp_error( $response ) ) {
            return [];
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        return $data ? $data : [];
    }
}
