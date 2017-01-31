<?php

class Yoochooseexport extends Yoochooseapi
{

    public function init()
    {
        parent::init();

        try {
            $response = $this->startExport();
            echo json_encode($result['success'] = $response);
            die;
        } catch (Exception $exc) {
            $this->sendResponse(array(), $exc->getMessage(), 400);
        }
    }

    protected function startExport()
    {

        $post = array();
        $oxConfig = oxNew('oxConfig');
        /** @var Yoochoosemodel $model */
        $model = oxNew('yoochoosemodel');
        $flag = $oxConfig->getShopConfVar('ycEnableFlag');

        if ($flag != 1) {
            $requestUri = $_SERVER['REQUEST_URI'];
            $queryString = substr($requestUri, strpos($requestUri, '?') + 1);
            $message = 'Export has started, with this query string : ' . $queryString;
            $model::log($message, '', '' ,'');

            $post['mandator'] = $this->getMandator();
            $post['limit'] = $this->getLimit();
            $post['webHook'] = $this->getWebHook();
            $post['password'] = $this->generateRandomString();
            $post['transaction'] = $this->getTransaction();
            $storeIds = $oxConfig->getShopIds();
            $lang = $this->getLanguage();
            $lang = (-1 ? '' : $lang);

            $post['storeData'] = $this->getStoreData($storeIds, $post['mandator'], $lang);

            if (empty($post['storeData'])) {
                $this->sendResponse(array(), "Mandator is not correct!", 400);
            }

            $oxConfig->saveShopConfVar('str', 'ycPassword', $post['password'], $oxConfig->getShopId(),
                'module:yoochoose');

            $baseUrl = $oxConfig->getShopMainUrl();
            $this->triggerExport($baseUrl . 'Yoochoose/Trigger?' . http_build_query($post));
        } else {
            $this->sendResponse(array(), "Job not sent.", 400);
        }

        return true;
    }

    /**
     * Generates random string with $length characters
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * triggerExport
     *
     * @param string @url
     * @return string execute
     */
    private function triggerExport($url)
    {

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cURL, CURLOPT_HEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($cURL, CURLOPT_NOBODY, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($cURL, CURLOPT_TIMEOUT, 1);

        $test = curl_exec($cURL);

        return $test;
    }

    /**
     * Returns array of languages and shop ids filtered by mandator
     *
     * @param array $storeIds
     * @param integer $mandator
     * @param string $language
     * @return array
     */
    private function getStoreData($storeIds, $mandator, $language)
    {

        $result = array();
        $lang = oxNew('oxlang');
        $oxConfig = oxNew('oxConfig');

        foreach ($storeIds as $storeId) {
            $baseMandator = $oxConfig->getShopConfVar('ycCustomerId', $storeId, 'module:yoochoose');
            if ($baseMandator == $mandator) {
                if ($language != '') {
                    $result[$language] = $storeId;
                } else {
                    $langIds = $lang->getLanguageIds($storeId);
                    foreach ($langIds as $langId) {
                        $result[$langId] = $storeId;
                    }
                }
            }
        }

        return $result;
    }

}