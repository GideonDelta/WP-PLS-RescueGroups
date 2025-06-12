<?php
namespace RescueSync;

class API_Client {
    protected $api_key;

    public function __construct( $api_key ) {
        $this->api_key = $api_key;
    }

    public function get_available_animals( $page = 1 ) {
        $url  = 'https://api.rescuegroups.org/v5/public/animals/search/available?limit=100&page=' . intval( $page );
        $url .= '&include=pictures,species,breeds';
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

    /**
     * Retrieve all available animals by iterating through each page.
     *
     * @return array Combined API response data.
     */
    public function get_all_available_animals() {
        $page      = 1;
        $all_data  = [ 'data' => [] ];

        do {
            $results = $this->get_available_animals( $page );

            if ( isset( $results['data'] ) && is_array( $results['data'] ) ) {
                $all_data['data'] = array_merge( $all_data['data'], $results['data'] );
            } else {
                break;
            }

            $page++;
        } while ( ! empty( $results['data'] ) );

        return $all_data;
    }
}
