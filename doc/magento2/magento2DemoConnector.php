<?php

$token = 'htqk6d01sg13m60ynnelg83qg4i5ccu7';
$shopUrl = '127.0.0.1/Magento2/';

class MagentoClient
{
    const API_VERSION = '/rest/V1/';

    private $token;

    private $url;

    /**
     * MagentoClient constructor.
     * @param $url
     * @param $token
     */
    public function __construct($url, $token)
    {
        $this->token = $token;
        $this->url = trim($url, '/') . self::API_VERSION;
    }

    public function get($action = '', $query = [])
    {
        return $this->execute($action, $query);
    }

    private function execute($action = '', $query = [], $method = 'GET', $post = [])
    {
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token,
        ];

        $url = $this->url . $action;
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($cURL, CURLOPT_URL, $url);

        if ($method !== 'GET') {
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $post);
            $headers[] = 'Content-Type:application/json';
        }

        curl_setopt($cURL, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($cURL);

        return $result;
    }

}

$client = new MagentoClient($shopUrl, $token);

$params = [
    'limit' => 2,
    'offset' => 0,
    'storeId' => 1,
];
$data = $client->get('yoochoose/products', $params);
echo '<h1> Product export from store(1) with limit(10) and offset(100)</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$params = [
    'limit' => 10,
    'offset' => 0,
];
$data = $client->get('yoochoose/subscribers', $params);
echo '<h1> Subscribers export with limit(10) and offset(0)</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$data = $client->get('yoochoose/stores');
echo '<h1> Store views export</h1>';
echo '<pre>' . print_r($data, true) . '</pre>';

$params = [
    'limit' => 3,
    'offset' => 5,
];
$data = $client->get('yoochoose/categories', $params);
echo '<h1> Categories export from store(1) with limit(3) and offset(5)</h1>';
echo '<pre>' . print_r($data, true) . '</pre>';