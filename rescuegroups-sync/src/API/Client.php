<?php
namespace RescueSync\API;

use RescueSync\Utils\Options;

/**
 * API client for RescueGroups API.
 */
class Client {
    /** @var string */
    private $apiKey;

    /**
     * Constructor.
     *
     * @param string $apiKey API key for requests.
     */
    public function __construct( string $apiKey ) {
        $this->apiKey = $apiKey;
    }

    /**
     * Fetch a single page of available animals.
     *
     * @param int   $page   Page number.
     * @param array $params Optional query parameters.
     * @return array Response array or empty array on failure.
     */
    public function fetchPage( int $page = 1, array $params = [] ) : array {
        $base  = 'https://api.rescuegroups.org/v5/public/animals/search/available';
        $limit = absint( Options::get( 'fetch_limit', 100 ) );

        $query = [
            'limit'   => $limit,
            'page'    => $page,
            'include' => 'pictures,species,breeds',
        ];

        foreach ( $params as $key => $value ) {
            if ( '' !== $value && null !== $value ) {
                $query[ $key ] = $value;
            }
        }

        $url      = add_query_arg( $query, $base );
        $response = wp_remote_get( $url, [ 'headers' => [ 'x-api-key' => $this->apiKey ] ] );

        if ( is_wp_error( $response ) ) {
            update_option( 'rescue_sync_last_status', 'Error: ' . $response->get_error_message() );
            return [];
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $code ) {
            update_option( 'rescue_sync_last_status', 'HTTP Error ' . $code );
            return [];
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        return $data ?: [];
    }

    /**
     * Retrieve all available animals by iterating through each page.
     *
     * @param array $params Optional query parameters.
     * @return array Combined response data.
     */
    public function fetchAll( array $params = [] ) : array {
        $page = 1;
        $all  = [ 'data' => [], 'included' => [] ];

        do {
            $results = $this->fetchPage( $page, $params );
            if ( isset( $results['data'] ) && is_array( $results['data'] ) ) {
                $all['data'] = array_merge( $all['data'], $results['data'] );
            } else {
                break;
            }

            if ( isset( $results['included'] ) && is_array( $results['included'] ) ) {
                foreach ( $results['included'] as $type => $items ) {
                    if ( ! isset( $all['included'][ $type ] ) ) {
                        $all['included'][ $type ] = [];
                    }
                    if ( is_array( $items ) ) {
                        $all['included'][ $type ] = array_replace( $all['included'][ $type ], $items );
                    }
                }
            }
            $page++;
        } while ( ! empty( $results['data'] ) );

        return $all;
    }
}
