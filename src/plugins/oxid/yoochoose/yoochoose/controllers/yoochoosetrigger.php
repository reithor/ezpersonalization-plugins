<?php
require_once __DIR__ . '/../Helpers/Data.php';
require_once __DIR__ . '/../models/yoochoosemodel.php';

class yoochoosetrigger extends oxUBase
{
    public function init()
    {
        $conf = $this->getConfig();
        $helper = oxNew('Data');


        $limit = $conf->getRequestParameter('limit');
        $callbackUrl = $conf->getRequestParameter('webHook');
        $postPassword = $conf->getRequestParameter('password');
        $transaction = $conf->getRequestParameter('transaction');
        $storeData = $conf->getRequestParameter('storeData');
        $customerId = $conf->getRequestParameter('mandator');

        $password = $conf->getShopConfVar('ycPassword');
        $storeId = key($storeData);
        $licenceKey = $conf->getShopConfVar('ycPassword', $storeId);

        if ($password === $postPassword) {
            $conf->saveShopConfVar('bool', 'ycEnableFlag', 1, $conf->getShopId(), 'module:yoochoose');
            try {
                Yoochoosemodel::logExport('Export has started for all resources.');
                $postData = $helper->export($storeData, $transaction, $limit, $customerId);

                Yoochoosemodel::logExport('Export has finished for all resources.');
                $this->setCallback($callbackUrl, $postData, $customerId, $licenceKey);
                $response['success'] = true;
            } catch (Exception $exc) {
                $response['success'] = false;
                $response['message'] = $exc->getMessage();
            } finally {
                $conf->saveShopConfVar('bool', 'ycEnableFlag', 0, $conf->getShopId(), 'module:yoochoose');
            }
        } else {
            $response['message'] = 'Passwords do not match!';
        }

        return json_encode($response);
    }

    /**
     * Creates request and returns response
     *
     * @param string $customerId
     * @param string $licenceKey
     * @return array
     * @internal param mixed $params
     */
    private function setCallback($url, $post, $customerId, $licenceKey)
    {
        $postString = json_encode($post);

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cURL, CURLOPT_USERPWD, $customerId . ":" . $licenceKey);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, ['Content-Type: application/json',]);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_HEADER, true);

        $response = curl_exec($cURL);

        $header_size = curl_getinfo($cURL, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $header = str_replace("\r\n", '', $header);
        $body = substr($response, $header_size);
        Yoochoosemodel::logExport('Callback header : ' . $header . ' Callback body : ' . $body);
        curl_close($cURL);

        return json_decode($response, true);
    }
}