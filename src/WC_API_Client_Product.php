<?php
class WC_API_Client_Product extends WC_API
{
	protected $wc_api;

	public function __construct(WC_API $wc_api)
	{
		$this->wc_api = $wc_api;
	}

	public function getAll($filters = array())
	{
		return $this->wc_api->call("products", $filters);
	}

	public function get($id)
	{
		return $this->wc_api->call("products/{$id}");
	}
}