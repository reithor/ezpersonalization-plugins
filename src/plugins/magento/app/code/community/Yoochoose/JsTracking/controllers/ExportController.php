<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Yoochoose JS-Tracking Export Controller
 *
 * @category   Yoochoose
 * @package    Yoochoose_JsTracking
 * @author     Yoochoose, yoochoose.net
 */
class Yoochoose_JsTracking_ExportController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $header = apache_request_headers();
        $appSecret = str_replace('Bearer ', '', $header['Authorization']);
        $licenceKey = Mage::getStoreConfig('yoochoose/general/license_key');

        if (md5($licenceKey) == $appSecret) {
            $post['limit'] = $this->getRequest()->getParam('size');
            $post['webHookUrl'] = $this->getRequest()->getParam('webHook');
            $post['mandator'] = $this->getRequest()->getParam('mandator');
            $post['transaction'] = $this->getRequest()->getParam('transaction');
            $forceStart = $this->getRequest()->getParam('forceStart');

            /** Checks if size, mandator, web hook is set */
            if (!isset($post['limit']) || empty($post['limit']) || !isset($post['webHookUrl'])
                || empty($post['webHookUrl']) || !isset($post['mandator']) || empty($post['mandator'])
            ) {
                $this->sendResponse(false, 'Size, mandator and webHook parameters must be set!');
            } else {
                $configModel = Mage::getModel('core/config');
                if ($forceStart) {
                    $configModel->saveConfig('yoochoose/export/enable_flag', 0, 'default', 0);
                }

                $enable = Mage::getStoreConfig('yoochoose/export/enable_flag');

                if ($enable != 1) {
                    $requestUri = $this->getRequest()->getRequestUri();
                    $queryString = substr($requestUri, strpos($requestUri, '?') + 1);
                    Mage::log('Export has started, with this query string : ' . $queryString, Zend_Log::INFO,
                        'yoochoose.log');

                    $post['transaction'] = $this->getRequest()->getParam('transaction');
                    $post['password'] = Mage::helper('yoochoose_jstracking')->generateRandomString();
                    $post['storeData'] = $this->getStoreData($post['mandator']);

                    if (empty($post['storeData'])) {
                        $this->sendResponse(false, 'Mandator is not correct!');
                    } else {
                        $post['storeData'] = json_encode($post['storeData']);
                    }


                    $configModel->saveConfig('yoochoose/export/password', $post['password'], 'default', 0);
                    $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                    $this->triggerExport($baseUrl . 'yoochoose/trigger', $post);

                } else {
                    $this->sendResponse(false, 'Job not sent!');
                }
            }
        } else {
            $this->sendResponse(false, 'Authentication failed!');
        }

        $this->sendResponse(true);
    }

    /**
     * Helper method for sending response
     *
     * @param $success
     * @param string $message
     */
    protected function sendResponse($success, $message = '')
    {
        $result = array();
        header('Content-Type: application/json');

        if ($success) {
            $result['success'] = true;
        } else {
            $result['success'] = $success;
            $result['message'] = $message;
        }

        echo json_encode(array_values($result));
        exit;
    }

    /**
     * triggerExport
     *
     * @param string @url
     * @param array $post
     * @return cURL execute
     */
    private function triggerExport($url, $post = array())
    {
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cURL, CURLOPT_HEADER, false);
        curl_setopt($cURL, CURLOPT_NOBODY, true);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);

        curl_setopt($cURL, CURLOPT_TIMEOUT, 1);

        return curl_exec($cURL);
    }

    /**
     *
     *
     * @param $mandator
     * @return array
     */
    private function getStoreData($mandator)
    {
        $result = array();
        $storeViews = Mage::getModel('yoochoose_jstracking/YoochooseExport')->getStoreViews();

        foreach ($storeViews as $storeView) {
            $baseMandator = Mage::getStoreConfig('yoochoose/general/customer_id', $storeView['id']);

            if ($baseMandator == $mandator) {
                $result[$storeView['id']] = $storeView['language'];
            }
        }

        return $result;
    }

}
