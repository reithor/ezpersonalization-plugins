<?php

class ApiClient
{

    const METHODE_GET = 'GET';
    const METHODE_PUT = 'PUT';
    const METHODE_POST = 'POST';
    const METHODE_DELETE = 'DELETE';

    protected $validMethods = array(
        self::METHODE_GET,
        self::METHODE_PUT,
        self::METHODE_POST,
        self::METHODE_DELETE
    );
    protected $apiUrl;
    protected $cURL;

    public function __construct($apiUrl, $username, $apiKey)
    {
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
        ));
    }

    public function call($url, $method = self::METHODE_GET, $data = array(), $params = array())
    {
        if (!in_array($method, $this->validMethods)) {
            throw new Exception('Invalid HTTP-Methode: ' . $method);
        }
        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
        }
        $url = rtrim($url, '?') . '?';
        $url = $this->apiUrl . $url . $queryString;
        $dataString = json_encode($data);
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
        $result = curl_exec($this->cURL);

        return $result;
    }

    public function get($url, $params = array())
    {
        return $this->call($url, self::METHODE_GET, array(), $params);
    }

}

$client = new ApiClient(
    //URL des Shopware Rest Servers
    'http://localhost/shopware/api',
    //Benutzername
    'demo',
    //API-Key des Benutzers
    'lTsm3m1y9WXeCKKW1iU6ChW3nWKJsKvTh9sxzsKt'
);

$params = array(
    'limit' => 2,
    'start' => 1,
);
$data = $client->get('ycsubscribers', $params);
echo '<h1> Subscribers export with limit(1) and start(2)</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$params = array();
$data = $client->get('shops', $params);
echo '<h1> Shops export with default params (start(0), limit(1000))</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$params = array(
    'start' => 15,
    'limit' => 3,
);
$data = $client->get('yccategories', $params);
echo '<h1> Categories export with limit(3) and start(15)</h1>';
echo '<pre>' . print_r($data, true) . '</pre><hr />';

$params = array(
    'start' => 15,
    'limit' => 3,
    'language' => 'en_GB',
);
$data = $client->get('ycarticles', $params);
echo '<h1> Articles export with limit(3), start(15) and language(en_GB)</h1>';
echo '<pre>' . print_r($data, true) . '</pre>';