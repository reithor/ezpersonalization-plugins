<?php

/**
 * WooCommerce API Client Class
 *
 * @author Gerhard Potgieter
 * @since 2013.12.05
 * @copyright Gerhard Potgieter
 * @version 0.3.1
 * @license GPL 3 or later http://www.gnu.org/licenses/gpl.html
 */
class WoocommerceApiClient
{

    /**
     * API base endpoint
     */
    const API_ENDPOINT = 'wc-api/v2/';

    /**
     * The HASH alorithm to use for oAuth signature, SHA256 or SHA1
     */
    const HASH_ALGORITHM = 'SHA256';

    /**
     * The API URL
     * @var string
     */
    private $apiUrl;

    /**
     * The WooCommerce Consumer Key
     * @var string
     */
    private $consulerKey;

    /**
     * The WooCommerce Consumer Secret
     * @var string
     */
    private $consulerSecret;

    /**
     * If the URL is secure, used to decide if oAuth or Basic Auth must be used
     * @var boolean
     */
    private $isSsl;

    /**
     * Should client decodeJson
     * @var boolean
     */
    private $decodeJson = true;

    /**
     * Default contructor
     * @param string  $consumerKey    The consumer key
     * @param string  $consumerSecret The consumer secret
     * @param string  $storeUrl       The URL to the WooCommerce store
     * @param boolean $isSsl          If the URL is secure or not, optional
     */
    public function __construct($consumerKey, $consumerSecret, $storeUrl, $isSsl = false)
    {
        if (!empty($consumerKey) && !empty($consumerSecret) && !empty($storeUrl)) {
            $this->apiUrl = ( rtrim($storeUrl, '/') . '/' ) . self::API_ENDPOINT;
            $this->setConsumerKey($consumerKey);
            $this->setConsumerSecret($consumerSecret);
            $this->setIsSsl($isSsl);
        } else if (!isset($consumerKey) && !isset($consumerSecret)) {
            throw new Exception('Error: __construct() - Consumer Key / Consumer Secret missing.');
        } else {
            throw new Exception('Error: __construct() - Store URL missing.');
        }
    }

    /**
     * Get API Index
     * @return mixed|json string
     */
    public function getIndex()
    {
        return $this->makeApiCall('');
    }

    /**
     * Get all orders
     * @param  array  $params
     * @return mixed|jason string
     */
    public function getOrders($params = array())
    {
        return $this->makeApiCall('orders', $params);
    }

    /**
     * Get a single order
     * @param  integer $orderId
     * @return mixed|json string
     */
    public function getOder($orderId)
    {
        return $this->makeApiCall('orders/' . $orderId);
    }

    /**
     * Get the total order count
     * @return mixed|json string
     */
    public function getOrdersCount()
    {
        return $this->makeApiCall('orders/count');
    }

    /**
     * Get orders notes for an order
     * @param  integer $orderId
     * @return mixed|json string
     */
    public function getOrderNotes($orderId)
    {
        return $this->makeApiCall('orders/' . $orderId . '/notes');
    }

    /**
     * Update the order, currently only status update suported by API
     * @param  integer $orderId
     * @param  array  $data
     * @return mixed|json string
     */
    public function updateOrder($orderId, $data = array())
    {
        return $this->makeApiCall('orders/' . $orderId, $data, 'POST');
    }

    /**
     * Delete the order, not suported in WC 2.1, scheduled for 2.2
     * @param  integer $orderId
     * @return mixed|json string
     */
    public function deleteOrder($orderId)
    {
        return $this->makeApiCall('orders/' . $orderId, $data = array(), 'DELETE');
    }

    /**
     * Get all coupons
     * @param  array  $params
     * @return mixed|json string
     */
    public function getCoupons($params = array())
    {
        return $this->makeApiCall('coupons', $params);
    }

    /**
     * Get a single coupon
     * @param  integer $couponId
     * @return mixed|json string
     */
    public function getCoupon($couponId)
    {
        return $this->makeApiCall('coupons/' . $couponId);
    }

    /**
     * Get the total coupon count
     * @return mixed|json string
     */
    public function getCouponsCount()
    {
        return $this->makeApiCall('coupons/count');
    }

    /**
     * Get a coupon by the coupon code
     * @param  string $couponCode
     * @return mixed|json string
     */
    public function getCouponByCode($couponCode)
    {
        return $this->makeApiCall('coupons/code/' . rawurlencode(rawurldecode($couponCode)));
    }

    /**
     * Get all customers
     * @param  array  $params
     * @return mixed|json string
     */
    public function getCustomers($params = array())
    {
        return $this->makeApiCall('customers', $params);
    }

    /**
     * Get a single customer
     * @param  integer $customerId
     * @return mixed|json string
     */
    public function getCustomer($customerId)
    {
        return $this->makeApiCall('customers/' . $customerId);
    }

    /**
     * Get a single customer by email
     * @param  string $email
     * @return mixed|json string
     */
    public function getCustomerByEmail($email)
    {
        return $this->makeApiCall('customers/email/' . $email);
    }

    /**
     * Get the total customer count
     * @return mixed|json string
     */
    public function getCustomersCount()
    {
        return $this->makeApiCall('customers/count');
    }

    /**
     * Get the customer's orders
     * @param  integer $customerId
     * @return mixed|json string
     */
    public function getCustomerOrders($customerId)
    {
        return $this->makeApiCall('customers/' . $customerId . '/orders');
    }

    /**
     * Get all the products
     * @param  array  $params
     * @return mixed|json string
     */
    public function getProducts($params = array())
    {
        return $this->makeApiCall('products', $params);
    }

    /**
     * Get a single product
     * @param  integer $productId
     * @return mixed|json string
     */
    public function getProduct($productId)
    {
        return $this->makeApiCall('products/' . $productId);
    }

    /**
     * Get the total product count
     * @return mixed|json string
     */
    public function getProductsCount()
    {
        return $this->makeApiCall('products/count');
    }

    /**
     * Get reviews for a product
     * @param  integer $productId
     * @return mixed|json string
     */
    public function getProductReviews($productId)
    {
        return $this->makeApiCall('products/' . $productId . '/reviews');
    }
    
    /**
     * Get product categories
     * @param  array  $params
     * @return mixed|json string
     */
    public function getCategories($params = array())
    {
        return $this->makeApiCall('products/categories', $params);
    }

    /**
     * Get product categories
     * @param  integer $categoryId
     * @return mixed|json string
     */
    public function getCategory($categoryId)
    {
        return $this->makeApiCall('products/categories/' . $categoryId);
    }

    /**
     * Get reports
     * @param  array  $params
     * @return mixed|json string
     */
    public function getReports($params = array())
    {
        return $this->makeApiCall('reports', $params);
    }

    /**
     * Get the sales report
     * @param  array  $params
     * @return mixed|json string
     */
    public function getSalesReport($params = array())
    {
        return $this->makeApiCall('reports/sales', $params);
    }

    /**
     * Get the top sellers report
     * @param  array  $params
     * @return mixed|json string
     */
    public function getTopSellersReport($params = array())
    {
        return $this->makeApiCall('reports/sales/top_sellers', $params);
    }

    /**
     * Run a custom endpoint call, for when you extended the API with your own endpoints
     * @param  string $endpoint
     * @param  array  $params
     * @param  string $method
     * @return mixed|json string
     */
    public function makeCustomEndpointCall($endpoint, $params = array(), $method = 'GET')
    {
        return $this->makeApiCall($endpoint, $params, $method);
    }

    /**
     * Set the consumer key
     * @param string $consumerKey
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consulerKey = $consumerKey;
    }

    /**
     * Set the consumer secret
     * @param string $consumerSecret
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consulerSecret = $consumerSecret;
    }

    /**
     * Set SSL variable
     * @param boolean $isSsl
     */
    public function setIsSsl($isSsl)
    {
        if ($isSsl == '') {
            if (strtolower(substr($this->apiUrl, 0, 5)) == 'https') {
                $this->isSsl = true;
            } else {
                $this->isSsl = false;
            }
        } else {
            $this->isSsl = $isSsl;
        }
    }

    /**
     * Set the return data as object
     * @param boolean $decode
     */
    public function setDecodeJson($decode = true)
    {
        $this->decodeJson = $decode;
    }

    /**
     * Make the call to the API
     * @param  string $endpoint
     * @param  array  $params
     * @param  string $method
     * @return mixed|json string
     */
    private function makeApiCall($endpoint, $params = array(), $method = 'GET')
    {
        $ch = curl_init();

        // Check if we must use Basic Auth or 1 legged oAuth, if SSL we use basic, if not we use OAuth 1.0a one-legged
        if ($this->isSsl) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->consulerKey . ":" . $this->consulerSecret);
        } else {
            $params['oauth_consumer_key'] = $this->consulerKey;
            $params['oauth_timestamp'] = time();
            $params['oauth_nonce'] = sha1(microtime());
            $params['oauth_signature_method'] = 'HMAC-' . self::HASH_ALGORITHM;
            $params['oauth_signature'] = $this->generateOauthSignature($params, $method, $endpoint);
        }

        if (isset($params) && is_array($params)) {
            $paramString = '?' . http_build_query($params);
        } else {
            $paramString = null;
        }

        // Set up the enpoint URL
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $endpoint . $paramString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ('POST' === $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } else if ('DELETE' === $method) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $return = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($this->decodeJson) {
            $return = json_decode($return);
        }

        if (empty($return)) {
            $return = '{"errors":[{"code":"' . $code . '","message":"cURL HTTP error ' . $code . '"}]}';
            $return = json_decode($return);
        }
        
        return $return;
    }

    /**
     * Generate oAuth signature
     * @param  array  $params
     * @param  string $httpMethod
     * @param  string $endpoint
     * @return string
     */
    public function generateOauthSignature($params, $httpMethod, $endpoint)
    {
        $baseRequestUri = rawurlencode($this->apiUrl . $endpoint);

        // normalize parameter key/values and sort them
        $params = $this->normalizeParameters($params);
        uksort($params, 'strcmp');

        // form query string
        $queryParams = array();
        foreach ($params as $key => $value) {
            $queryParams[] = $key . '%3D' . $value; // join with equals sign
        }

        $queryString = implode('%26', $queryParams); // join with ampersand
        // form string to sign (first key)
        $stringToSign = $httpMethod . '&' . $baseRequestUri . '&' . $queryString;

        return base64_encode(hash_hmac(self::HASH_ALGORITHM, $stringToSign, $this->consulerSecret, true));
    }

    /**
     * Normalize each parameter by assuming each parameter may have already been
     * encoded, so attempt to decode, and then re-encode according to RFC 3986
     *
     * Note both the key and value is normalized so a filter param like:
     *
     * 'filter[period]' => 'week'
     *
     * is encoded to:
     *
     * 'filter%5Bperiod%5D' => 'week'
     *
     * This conforms to the OAuth 1.0a spec which indicates the entire query string
     * should be URL encoded
     *
     * @since 0.3.1
     * @see rawurlencode()
     * @param array $parameters un-normalized pararmeters
     * @return array normalized parameters
     */
    private function normalizeParameters($parameters)
    {

        $normalizedParameters = array();

        foreach ($parameters as $key => $value) {

            // percent symbols (%) must be double-encoded
            $key = str_replace('%', '%25', rawurlencode(rawurldecode($key)));
            $value = str_replace('%', '%25', rawurlencode(rawurldecode($value)));

            $normalizedParameters[$key] = $value;
        }

        return $normalizedParameters;
    }

}
