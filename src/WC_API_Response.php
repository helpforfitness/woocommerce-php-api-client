<?php
class WC_API_Response
{
	protected $_response;

	function __construct($response = "")
	{
		$this->_response = $response;
	}

	public function toArray()
	{
		return json_decode($this->_response,true);
	}
}