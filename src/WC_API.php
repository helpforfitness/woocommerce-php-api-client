<?php
class WC_API
{
	const API_ENDPOINT = 'wc-api/v2/';
	const HASH_ALGORITHM = 'SHA256';

	protected $_store_url;
	protected $_api_url;
	protected $_consumer_secret;
	protected $_consumer_key;

	public function __construct($consumer_key, $consumer_secret, $store_url)
	{
		$this->_store_url = $store_url;
		$this->_api_url = rtrim($store_url,'/') . '/' . self::API_ENDPOINT;
		$this->_consumer_key = $consumer_key;
		$this->_consumer_secret = $consumer_secret;
	}

	public function Customer()
	{
		return new WC_API_Customer($this);
	}

	public function Product()
	{
		return new WC_API_Product($this);
	}

	public function toArray()
	{
		return array('yes' => 'si');
	}

	/**
	 * Make the call to the API
	 * @param  string $endpoint
	 * @param  array  $get_params
	 * @param  string $method
	 * @return mixed|json string
	 */
	public function call($endpoint, $data = array(), $method = 'GET')
	{
		$ch = curl_init();

		//If the method is other than GET we will send the data in the body of the request
		if ($method == 'GET') {
			$get_params = $data;
		} else{
			$get_params = array();
		}

		// Check if we must use Basic Auth or 1 legged oAuth, if SSL we use basic, if not we use OAuth 1.0a one-legged
		if ($this->_is_ssl()) {
			curl_setopt( $ch, CURLOPT_USERPWD, $this->_consumer_key . ":" . $this->_consumer_secret );
		} else {
			$get_params['oauth_consumer_key'] = $this->_consumer_key;
			$get_params['oauth_timestamp'] = time();
			$get_params['oauth_nonce'] = sha1( microtime() );
			$get_params['oauth_signature_method'] = 'HMAC-' . self::HASH_ALGORITHM;
			$get_params['oauth_signature'] = $this->_generate_oauth_signature( $get_params, $method, $endpoint );
		}

		if (isset($get_params) && is_array($get_params)) {
			$paramString = '?' . http_build_query( $get_params );
		} else {
			$paramString = null;
		}

		// Set up the enpoint URL
		curl_setopt($ch, CURLOPT_URL, $this->_api_url . $endpoint . $paramString);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ('POST' === $method && count($data) > 0) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data));
    	} else if ( 'DELETE' === $method ) {
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		}

		$return = curl_exec( $ch );
		$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		if ($return === false) {
			$return = '{"errors":[{"code":"500","message":"cURL error ' . $curl_error($ch) . '"}]}';
		}

		if ( empty( $return ) && $code != 200) {
			$return = '{"errors":[{"code":"' . $code . '","message":"cURL HTTP error ' . $code . '"}]}';
		}

		return new WC_API_Response($return);
	}

	/**
	 * Helper to detect if the API url is https or not
	 */
	protected function _is_ssl()
	{
		if ( strtolower( substr( $this->_store_url, 0, 5 ) ) == 'https' ) {
			return true;
		} else {
			return false;
		} 
	}

	/**
	 * Generate oAuth signature
	 * @param  array  $get_params
	 * @param  string $http_method
	 * @param  string $endpoint
	 * @return string
	 */
	protected function _generate_oauth_signature($get_params, $http_method, $endpoint)
	{
		$base_request_uri = rawurlencode( $this->_api_url . $endpoint );

		// normalize parameter key/values and sort them
		$get_params = $this->_normalize_parameters( $get_params );
		uksort( $get_params, 'strcmp' );

		// form query string
		$query_params = array();
		foreach ( $get_params as $param_key => $param_value ) {
			$query_params[] = $param_key . '%3D' . $param_value; // join with equals sign
		}

		$query_string = implode( '%26', $query_params ); // join with ampersand

		// form string to sign (first key)
		$string_to_sign = $http_method . '&' . $base_request_uri . '&' . $query_string;

		return base64_encode( hash_hmac( self::HASH_ALGORITHM, $string_to_sign, $this->_consumer_secret, true ) );
	}

	/**
	 * Normalize each parameter by assuming each parameter may have already been
	 * encoded, so attempt to decode, and then re-encode according to RFC 3986
	 *
	 * Note both the key and value is normalized so a filter param like:
	 *
	 * 'filter[period]' => 'week'
	 *
	 * is encoded to:
	 *
	 * 'filter%5Bperiod%5D' => 'week'
	 *
	 * This conforms to the OAuth 1.0a spec which indicates the entire query string
	 * should be URL encoded
	 *
	 * @see rawurlencode()
	 * @param array $parameters un-normalized pararmeters
	 * @return array normalized parameters
	 */
	protected function _normalize_parameters($parameters)
	{
		$normalized_parameters = array();

		foreach ($parameters as $key => $value) {
			// percent symbols (%) must be double-encoded
			$key   = str_replace('%', '%25', rawurlencode(rawurldecode($key)));
			$value = str_replace('%', '%25', rawurlencode(rawurldecode($value)));
			$normalized_parameters[$key] = $value;
		}

		return $normalized_parameters;
	}	
}
