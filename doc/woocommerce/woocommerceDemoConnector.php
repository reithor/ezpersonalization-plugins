<?php

require_once 'woocommerceApiClient.php';

$consumerKey = 'ck_6138bb59401befd2b88e7ad24c2d56b1'; // Add your own Consumer Key here
$consumerSecret = 'cs_131245921720d5d7e4c238c1d8604daa'; // Add your own Consumer Secret here
$storeUrl = 'http://localhost/woocommerce/'; // Add the home URL to the store you want to connect to here

// Initialize the class
$client = new WoocommerceApiClient($consumerKey, $consumerSecret, $storeUrl);
$client->setDecodeJson(false);

$params = array(
    'filter[limit]' => 2,
    'filter[offset]' => 10,
);
$response = $client->getProducts($params);
echo '<h1> Products export with limit(2) and offset(10)</h1>';
echo  print_r($response, true) .'<hr />';

$response = $client->getProduct(50);
echo '<h1> Product export by product Id.</h1>';
echo print_r($response, true) . '<hr />';

$params = array(
    'filter[limit]' => 5,
    'filter[offset]' => 0,
);
$response = $client->getCategories($params);
echo '<h1> Categories export with limit(0) and offset(5)</h1>';
echo print_r($response, true) . '<hr />';

$response = $client->getCategory(11);
echo '<h1> Category export by category Id.</h1>';
echo print_r($response, true) . '<hr />';

$params = array(
    'filter[limit]' => 5,
    'filter[offset]' => 0,
);
$response = $client->getCustomers($params);
echo '<h1> Customers export with limit(0) and offset(5)</h1>';
echo print_r($response, true) . '<hr />';

$response = $client->getCustomer(3);
echo '<h1> Customer export by customer Id.</h1>';
echo print_r($response, true) . '<hr />';