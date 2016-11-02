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

        $configModel = Mage::getModel('core/config');
        $enable = Mage::getStoreConfig('yoochoose/export/enable_flag');

        if ($enable != 1) {

            $post = [];
            $post['limit'] = $this->getRequest()->getParam('size');
            $post['webHookUrl'] = $this->getRequest()->getParam('webHook');
            $post['password'] = $this->generateRandomString();

            $configModel->saveConfig('yoochoose/export/password', $post['password'], 'default', 0);

            $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

            $this->triggerExport($baseUrl . 'yoochoose/trigger?XDEBUG_SESSION_START=PHPSTORM', $post);

            $response = [
                'success' => true
            ];

        } else {

            $response = [
                'success' => false,
                'message' => 'Job not sent'
            ];
        }

        header('Content-Type: application/json;');
        exit(json_encode(array_values($response)));
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
     * @param array $post
     * @return cURL execute
     */
    private function triggerExport($url, $post = array()) {

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

}
