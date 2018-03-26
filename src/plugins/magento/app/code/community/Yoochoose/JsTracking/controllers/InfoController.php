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
 * Yoochoose JS-Tracking Info Controller
 *
 * @category   Yoochoose
 * @package    Yoochoose_JsTracking
 * @author     Yoochoose, yoochoose.net
 */
class Yoochoose_JsTracking_InfoController extends Mage_Core_Controller_Front_Action
{

    const YOOCHOOSE_CDN_SCRIPT = '//event.yoochoose.net/cdn';
    const AMAZON_CDN_SCRIPT = '//cdn.yoochoose.net';

    public function indexAction()
    {
        $header = apache_request_headers();
        $appSecret = str_replace('Bearer ', '', $header['Authorization']);

        $storeId = $this->getRequest()->getParam('storeViewId');
        $storeId = isset($storeId) ? $storeId : Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();
        $storeIdValid = $this->validateStoreId($storeId);

        if ($storeIdValid) {
            $licenceKey = Mage::getStoreConfig('yoochoose/general/license_key', $storeId);

            if (md5($licenceKey) == $appSecret) {
                $store = Mage::app()->getStore($storeId);

                $mandator = Mage::getStoreConfig('yoochoose/general/customer_id', $storeId);
                $pluginId = Mage::getStoreConfig('yoochoose/general/plugin_id', $storeId);
                $plugin = $pluginId ? '/' . $pluginId : '';
                $scriptOverwrite = Mage::getStoreConfig('yoochoose/advanced/overwrite', $storeId);

                if ($scriptOverwrite) {
                    $scriptOverwrite = (!preg_match('/^(http|\/\/)/', $scriptOverwrite) ? '//' : '') . $scriptOverwrite;
                    $scriptUrl = preg_replace('(^https?:)', '', $scriptOverwrite);
                } else {
                    $scriptUrl = Mage::getStoreConfig('yoochoose/advanced/performance') ?
                        self::AMAZON_CDN_SCRIPT : self::YOOCHOOSE_CDN_SCRIPT;
                }

                $scriptUrl = rtrim($scriptUrl, '/') . "/v1/{$mandator}{$plugin}/tracking.";

                $pluginVersion = $this->getPluginVersion();

                $result = [
                    'shop' => $store->getName(),
                    'shop_version' => Mage::getVersion(),
                    'plugin_version' => isset($pluginVersion) ? $pluginVersion : '',
                    'mandator' => $mandator,
                    'license_key' => $licenceKey,
                    'plugin_id' => $pluginId,
                    'endpoint' => Mage::getStoreConfig('yoochoose/general/endpoint', $storeId),
                    'design' => Mage::getStoreConfig('yoochoose/general/design', $storeId),
                    'itemtype' => Mage::getStoreConfig('yoochoose/general/itemtypeid', $storeId),
                    'script_uris' => [
                        $scriptUrl . 'js',
                        $scriptUrl . 'css'
                    ],
                    'overwrite_endpoint' => $scriptOverwrite,
                    'php_version' => phpversion(),
                    'os' => PHP_OS
                ];

                $this->sendResponse(true, $result);
            } else {
                $this->sendResponse(false, 'Authentication failed!');
            }
        } else {
            $this->sendResponse(false, 'Store view id parameter is not valid!');
        }
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
            $result = $message;
        } else {
            $result['success'] = $success;
            $result['message'] = $message;
        }

        echo json_encode($result);
        exit;
    }

    /**
     * @param mixed $storeId
     * @return boolean
     */
    private function validateStoreId($storeId)
    {
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $website = Mage::getModel('core/website')->load($websiteId);
        $storeIds = $website->getStoreIds();
        if (in_array($storeId, $storeIds)) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves module version
     *
     * @return mixed
     */
    private function getPluginVersion()
    {
        return (string)Mage::getConfig()->getNode()->modules->Yoochoose_JsTracking->version;
    }
}
