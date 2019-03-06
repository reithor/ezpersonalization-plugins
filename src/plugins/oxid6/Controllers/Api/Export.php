<?php

namespace Yoochoose\Oxid\Controllers\Api;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use Yoochoose\Oxid\Models\Yoochoose;

/**
 * Class Export
 * @package Yoochoose\Oxid\Controllers\Api
 */
class Export extends BaseApi
{
    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        parent::init();

        try {
            $result = [];
            $response = $this->startExport();
            echo json_encode($result['success'] = $response);
            die;
        } catch (\Exception $exc) {
            $this->sendResponse([], $exc->getMessage(), 400);
        }
    }

    protected function startExport()
    {
        $post = [];
        $oxConfig = Registry::getConfig();
        $model = new Yoochoose();
        if ($this->getRequestParameter('forceStart')) {
            $this->getConfig()->saveShopConfVar('bool', 'ycEnableFlag', 0, $oxConfig->getShopId(), 'module:yoochoose');
        }

        $flag = $oxConfig->getShopConfVar('ycEnableFlag', $oxConfig->getShopId(), 'module:yoochoose');

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
            $shopIds = $oxConfig->getShopIds();
            $lang = $this->getLanguage();
            $lang = ($lang == -1 ? '' : $lang);

            $post['shopData'] = $this->getStoreData($shopIds, $post['mandator'], $lang);

            if (empty($post['shopData'])) {
                $this->sendResponse([], "Mandator is not correct!", 400);
            }

            $oxConfig->saveShopConfVar('str', 'ycPassword', $post['password'], $oxConfig->getShopId(),
                'module:yoochoose');

            $baseUrl = $oxConfig->getShopUrl() ?: $oxConfig->getShopMainUrl();
            
            $this->triggerExport($baseUrl . 'Yoochoose/Trigger?' . http_build_query($post));
        } else {
            $this->sendResponse([], "Job not sent.", 400);
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
     *
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
     * @param array $shopIds
     * @param integer $mandator
     * @param string $language
     * @return array
     */
    private function getStoreData($shopIds, $mandator, $language)
    {

        $result = [];
        $lang = new Language();
        $oxConfig = Registry::getConfig();

        $i = 0;
        foreach ($shopIds as $shopId) {
            $baseMandator = $oxConfig->getShopConfVar('ycCustomerId', $shopId, 'module:yoochoose');
            if ($baseMandator == $mandator) {
                if (!empty($language)) {
                    $result[$i]['shopId'] = $shopId;
                    $result[$i]['language'] = $language;
                    $i++;
                } else {
                    $langIds = $lang->getLanguageIds($shopId);
                    foreach ($langIds as $langId) {
                        $result[$i]['shopId'] = $shopId;
                        $result[$i]['language'] = $langId;
                        $i++;
                    }
                }
            }
        }

        return $result;
    }
}
