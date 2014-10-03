<?php

namespace wpscholar\geocoding;

/**
 * Class GoogleGeocodingAPI
 *
 * @package wpscholar\geocoding
 * @link    https://developers.google.com/maps/documentation/geocoding/
 */
class GoogleGeocodingAPI {

	const URL = 'https://maps.google.com/maps/api/geocode/json';

	/**
	 * Address - required for standard geocoding requests
	 *
	 * @var string
	 */
	public $address;

	/**
	 * Bounds (optional)
	 *
	 * @var string
	 */
	public $bounds;

	/**
	 * Components (optional)
	 *
	 * @var string
	 * @link https://developers.google.com/maps/documentation/geocoding/#ComponentFiltering
	 */
	public $components;

	/**
	 * Key (optional)
	 * Your applications API key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Language (optional)
	 * The language in which to return results
	 *
	 * @var string
	 */
	public $language;

	/**
	 * Latitude and longitude - required for reverse geocoding requests
	 *
	 * @var string
	 */
	public $latlng;

	/**
	 * Location type (optional) - specific to reverse geocoding requests
	 * Allowed values: ROOFTOP, RANGE_INTERPOLATED, GEOMETRIC_CENTER, APPROXIMATE
	 *
	 * @var string
	 */
	public $location_type;

	/**
	 * Region (optional) - specific to standard geocoding requests
	 *
	 * @var string
	 */
	public $region;

	/**
	 * Result type (optional) - specific to reverse geocoding requests
	 *
	 * @var string
	 * @link https://developers.google.com/maps/documentation/geocoding/#Types
	 */
	public $result_type;

	/**
	 * Geocode - convert an address into coordinates
	 *
	 * @return null | object
	 */
	public function geocode() {
		$results              = null;
		$queryArgs            = array_filter( get_object_vars( $this ) );
		$queryArgs['address'] = preg_replace( '#\s+#', '+', join( ', ', (array) $this->address ) );
		$queryString          = http_build_query( $queryArgs );
		$response             = wp_remote_get( self::URL . '?' . $queryString );
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $data && is_object( $data ) && property_exists( $data, 'status' ) && 'OK' == $data->status ) {
				$results = $data;
			}
		}

		return $results;
	}

	/**
	 * Reverse Geocode - convert coordinates into an address
	 *
	 * @return null | string
	 */
	public function reverseGeocode() {
		$results             = null;
		$queryArgs           = array_filter( get_object_vars( $this ) );
		$queryArgs['latlng'] = str_replace( ' ', '', join( ',', (array) $this->latlng ) );
		$queryString         = http_build_query( $queryArgs );
		$response            = wp_remote_get( self::URL . '?' . $queryString );
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $data && is_object( $data ) && property_exists( $data, 'status' ) && 'OK' == $data->status ) {
				$results = $data;
			}
		}

		return $results;
	}

	/**
	 * Reset all properties in preparation for a new request
	 */
	public function reset() {
		foreach ( get_object_vars( $this ) as $property ) {
			$this->$property = null;
		}
	}

	/**
	 * Get address component
	 *
	 * @param array  $componentType
	 * @param string $addressComponents
	 *
	 * @return null | object
	 */
	public function getAddressComponent( array $componentType, array $addressComponents ) {
		$component = null;
		foreach ( $addressComponents as $addressComponent ) {
			if ( $componentType == (array) $addressComponent->types ) {
				$component = $addressComponent;
				break;
			}
		}

		return $component;
	}

}