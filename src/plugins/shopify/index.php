<?php

session_start();

require_once 'apiClient/shopifyClient.php';
const SHOPIFY_API_KEY = '4a9caf03c269f192e0dd87310da4b0b3';
const SHOPIFY_SECRET = 'f51aa9138958c7b93c1dc53753ebc637';

$jsonpCallback = filter_input(INPUT_GET, 'jsonpCallback');
$shop = filter_input(INPUT_GET, 'shop');
$ids = filter_input(INPUT_GET, 'ids');

$sc = new ShopifyClient($shop, '4085f2f4ac84caa7b86dfa48684ab6b7', SHOPIFY_API_KEY, SHOPIFY_SECRET);

try {
    // Adding new tracking script, must be https, beacuse of injecting on checkout page
//    $params = array('script_tag' => array('event' => 'onload', 'src' => 'https://jstracking.sy.rs/yc-tracking-sy.js'));
//    $result = $sc->call('POST', '/admin/script_tags.json', $params);
//    echo '<pre>' . print_r($result, true) . '</pre>';
//    $id = $result['id'];

    $result = array();
    $response = $sc->call('GET', '/admin/products.json?fields=id,handle&ids=' . $ids);
    foreach ($response as $product) {
        $result[$product['id']] =  $product['handle'];
    }

    exit($jsonpCallback . '(' . json_encode($result) . ');');

    // Update existing script src
//    $params = array('script_tag' => array('id' => $id, 'src' => 'https://jstracking.sy.rs/yc-tracking-sy.js'));
//    $result = $sc->call('PUT', "/admin/script_tags/{$id}.json", $params);
//    echo '<pre>' . print_r($result, true) . '</pre>';
} catch (ShopifyApiException $e) {
    echo '<pre>' . print_r($e->getResponse(), true) . '</pre>';
} catch (ShopifyCurlException $e) {
    echo '<pre>' . print_r($e, true) . '</pre>';
}