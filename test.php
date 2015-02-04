<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

require_once realpath(dirname(__FILE__) . '/autoload.php');

// $consumer_key = 'ck_250a148201c18c65deb2437d1fa46308';
// $consumer_secret = 'cs_56afec57ff650d51bea14c9e26773599'; 
// $store_url = 'http://helpforfitness.com/'; 

$consumer_key = 'ck_250a148201c18c65deb2437d1fa46308';
$consumer_secret = 'cs_56afec57ff650d51bea14c9e26773599'; 
$store_url = 'http://dev.helpforfitness.com/'; 

$wc_api = new WC_API($consumer_key, $consumer_secret, $store_url);
print_r($wc_api->Customer()->craete(array(
	'customer' => array(
        'email' => 'guillermo+5@protein-up.com',
        'username' => 'guillermo5@protein-up.com',
        'first_name' => 'Guillermo',
        'last_name' => 'Gette',
        'password' => ''
	)
)));