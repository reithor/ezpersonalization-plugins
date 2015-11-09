<?php

define('SHOP_URL', 'http://oxid.dev.soprex.com/');
define('APP_SECRET', '7f34c649a97d7914a4154473522d2f4a');

class OXIDApiClient
{
    /** @var  String */
    private $appSecret;

    /** @var  String */
    private $shopUrl;

    /**
     * OXIDApiClient constructor.
     * @param String $shopUrl
     * @param String $appSecret
     * @throws Exception if parameters are invalid
     */
    public function __construct($shopUrl, $appSecret)
    {
        if (trim($appSecret) && filter_var($shopUrl, FILTER_VALIDATE_URL)) {
            $this->appSecret = trim($appSecret);
            $this->shopUrl = rtrim($shopUrl, '/') . '/';
        } else {
            throw new Exception('Parameters are invalid!');
        }
    }

    /**
     * Executes API request to OXID
     * @param string $endpoint
     * @param array $params
     * @return mixed
     */
    public function execute($endpoint = '', $params = array())
    {
        $params['appSecret'] = $this->appSecret;
        $endpoint = trim($endpoint, '/') . '/';

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_POST, true);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $params);
        curl_setopt($cURL, CURLOPT_URL, $this->shopUrl . $endpoint);

        $result = curl_exec($cURL);
        return $result;
    }

}

$apiClient = new OXIDApiClient(SHOP_URL, APP_SECRET);

$data = $apiClient->execute('Yoochoose/Stores/');
echo '<h1>Stores export</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$params = array(
    'limit' => 2,
    'offset' => 10,
    'lang' => 1,
    'shop' => 'oxbaseshop',
);
$data = $apiClient->execute('Yoochoose/Articles/', $params);
echo '<h1>Product export from shop (oxbaseshop) with limit(2) and offset(10) in language with id 1</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$params = array(
    'limit' => 2,
    'offset' => 10,
    'lang' => 0,
);
$data = $apiClient->execute('Yoochoose/Categories/', $params);
echo '<h1> Categories export from default shop with limit(2) and offset(10) in language with id 0</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$data = $apiClient->execute('Yoochoose/Users/');
echo '<h1> Export of all customers</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';
