<?php
namespace RescueSync;

class API_Client {
    protected $api_key;

    public function __construct( $api_key ) {
        $this->api_key = $api_key;
    }

    public function get_available_animals( $page = 1, $per_page = 100 ) {
        $per_page = max( 1, min( 100, intval( $per_page ) ) );
        $url  = 'https://api.rescuegroups.org/v5/public/animals/search/available?limit=' . $per_page . '&page=' . intval( $page );
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
     * @param int $limit Maximum number of animals to fetch. 0 for unlimited.
     * @return array Combined API response data.
     */
    public function get_all_available_animals( $limit = 0 ) {
        $page     = 1;
        $all_data = [ 'data' => [] ];

        do {
            $remaining = $limit > 0 ? $limit - count( $all_data['data'] ) : 100;
            if ( $limit > 0 && $remaining <= 0 ) {
                break;
            }

            $results = $this->get_available_animals( $page, $remaining );

            if ( isset( $results['data'] ) && is_array( $results['data'] ) ) {
                $all_data['data'] = array_merge( $all_data['data'], $results['data'] );
            } else {
                break;
            }

            $page++;
        } while ( ! empty( $results['data'] ) && ( $limit <= 0 || count( $all_data['data'] ) < $limit ) );

        if ( $limit > 0 && count( $all_data['data'] ) > $limit ) {
            $all_data['data'] = array_slice( $all_data['data'], 0, $limit );
        }

        return $all_data;
    }
}
