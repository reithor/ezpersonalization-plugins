<?php

class YooChoose_JsTracking_Model_Observer 
{
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    protected $orderIds;

    /**
     * Applies the special price percentage discount
     * @param   Varien_Event_Observer $observer
     * @return  YooChoose_JsTracking_Observer
     */
    public function trackBuy($observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('head');
        if ($block) {
            $block->setOrderId(end($orderIds));
        }
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSystemConfigChangedSectionYoochoose($observer)
    {
        $customerId = Mage::getStoreConfig('yoochoose/general/customer_id');
        $licenseKey = Mage::getStoreConfig('yoochoose/general/license_key');

        if (!$customerId && !$licenseKey) {
            return;
        }

        try {
            $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/';
            $pluginVersion = Mage::getStoreConfig('yoochoose/general/plugin_id');
            if ($pluginVersion) {
                $url .= $pluginVersion . '/';
            }
            
            $url .= 'create?fallback_design=true';
            
            $overwrite = Mage::getStoreConfig('yoochoose/general/endpoint_overwrite');
            if (!$overwrite) {
                $url .= '&recheck_type=true';
            }
            
            $response = Mage::helper('yoochoose_jstracking')->_getHttpPage($url, $customerId, $licenseKey);
            
        } catch (Exception $ex) {

        }
    }
   
}