<?php
class WC_API_Client_Order
{
	protected $wc_api;

	public function __construct(WC_API_Client $wc_api)
	{
		$this->wc_api = $wc_api;
	}

	public function get($order_id = "")
	{
		if (!empty($order_id)) {
			return $this->wc_api->call("orders/{$order_id}");
		}
	}

	public function create($data = array())
	{
		return $this->wc_api->call("orders", $data, "POST");
	}

	public function listAll($status = "")
	{
		$query = array();
		if (!empty($status)) {
			$query['status'] = $status;
		}
		return $this->wc_api->call("orders", $query);
	}

	public function update($order_id, $data = array())
	{
		if (!empty($order_id)) {
			return $this->wc_api->call("orders/{$order_id}", $data, "POST");
		}
	}
}