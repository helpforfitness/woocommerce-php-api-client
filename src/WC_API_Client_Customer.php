<?php
class WC_API_Client_Customer
{
	protected $wc_api;

	public function __construct(WC_API_Client $wc_api)
	{
		$this->wc_api = $wc_api;
	}

	public function get($customer_id = "")
	{
		if (!empty($customer_id)) {
			return $this->wc_api->call("customers/{$customer_id}");
		}
	}

	public function byEmail($email = "")
	{
		if (!empty($email)) {
			return $this->wc_api->call("customers/email/{$email}");
		}
	}

	public function craete($data = array())
	{
		return $this->wc_api->call("customers", $data, "POST");
	}
}