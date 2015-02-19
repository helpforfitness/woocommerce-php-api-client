<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

require_once 'src/WC_API_Client.php';
require_once 'src/WC_API_Client_Customer.php';
require_once 'src/WC_API_Client_Product.php';
require_once 'src/WC_API_Client_Response.php';

// $consumer_key = 'ck_250a148201c18c65deb2437d1fa46308';
// $consumer_secret = 'cs_56afec57ff650d51bea14c9e26773599'; 
// $store_url = 'http://helpforfitness.com/'; 

$consumer_key = 'ck_250a148201c18c65deb2437d1fa46308';
$consumer_secret = 'cs_56afec57ff650d51bea14c9e26773599'; 
$store_url = 'http://dev.helpforfitness.com/'; 

$wc_api = new WC_API_Client($consumer_key, $consumer_secret, $store_url);
//$rs = $wc_api->Product()->getAll(array('filter[in]' => "1516,1174"))->toArray();
$rs = $wc_api->Customer()->byEmail('guillermo@protein-up.com')->toJson();
print_r($rs);