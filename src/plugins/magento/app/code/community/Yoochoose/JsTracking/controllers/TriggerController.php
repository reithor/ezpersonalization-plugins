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
 * Yoochoose JS-Tracking Trigger Controller
 *
 * @category   Yoochoose
 * @package    Yoochoose_JsTracking
 * @author     Yoochoose, yoochoose.net
 */
class Yoochoose_JsTracking_TriggerController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {

        $configModel = Mage::getModel('core/config');

        $limit = $this->getRequest()->getParam('limit');
        $callbackUrl = $this->getRequest()->getParam('webHookUrl');
        $postPassword = $this->getRequest()->getParam('password');
        $transaction = $this->getRequest()->getParam('transaction');
        $storeData = json_decode($this->getRequest()->getParam('storeData'), true);
        $customerId = $this->getRequest()->getParam('mandator');

        $shopId = key($storeData);
        $licenceKey = Mage::getStoreConfig('yoochoose/general/license_key', $shopId);
        Mage::app()->cleanCache();
        $password = Mage::getStoreConfig('yoochoose/export/password', 0);

        if ($password === $postPassword) {
            $configModel->saveConfig('yoochoose/export/enable_flag', 1, 'default', 0);

            Mage::log('Export has started for all resources. ', Zend_Log::INFO, 'yoochoose.log');

            try {
                $postData = Mage::helper('yoochoose_jstracking')->export($storeData, $transaction, $limit, $customerId);
                Mage::log('Export has finished for all resources.', Zend_Log::INFO, 'yoochoose.log');
                $this->setCallback($callbackUrl, $postData, $customerId, $licenceKey);
                $response['success'] = true;
            } catch (Exception $exc) {
                $response['success'] = false;
                $response['message'] = $exc->getMessage();
            } finally {
                $configModel->saveConfig('yoochoose/export/enable_flag', 0, 'default', 0);
            }
        } else {
            $response['message'] = 'Passwords do not match!';
        }

        return json_encode($response);
    }

    /**
     * Creates request and returns response
     *
     * @param string @url
     * @param array @post
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
        curl_setopt($cURL, CURLOPT_USERPWD, $customerId.":".$licenceKey);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_HEADER, true);

        $response = curl_exec($cURL);

        $header_size = curl_getinfo($cURL, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $header = str_replace("\r\n", '', $header);
        $body = substr($response, $header_size);
        Mage::log('Callback header : '. $header .' Callback body : '. $body, Zend_Log::INFO, 'yoochoose.log');

        curl_close($cURL);

        return json_decode($response, true);
    }

}
