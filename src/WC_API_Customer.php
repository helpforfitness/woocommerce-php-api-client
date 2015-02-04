<?php
use WC_API;
namespace WC_API\Customer;

class WC_API_Customer
{
	protected $wc_api;

	public function __construct(WC_API $wc_api)
	{
		$this->wc_api = $wc_api;
	}

	public function get($customer_id = "")
	{
		if (!empty($customer_id)) {
			return $this->wc_api->call("customers/{$customer_id}");
		}
	}

	public function craete($data = array())
	{
		return $this->wc_api->call("customers", $data, "POST");
	}
}