<?php

class Yoochoose_JsTracking_Model_Observer
{
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    protected $orderIds;

    /**
     * Applies the special price percentage discount
     * @param   Varien_Event_Observer $observer
     * @return  Yoochoose_JsTracking_Observer
     */
    public function trackBuy($observer)
    {
        Mage::getStoreConfig('yoochoose/general/endpoint');
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('yoochoose.jstracking');
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
        $apiCredentials = array('consumerKey' => '', 'consumerSecret' => '');
        $code = Mage::getSingleton('adminhtml/config_data')->getStore();
        $scopeId = Mage::getModel('core/store')->load($code)->getId();
        if ($scopeId === null) {
            $scopeId = 0;
        }

        $scopeName = $scopeId === 0 ? 'default' : 'stores';
        if (!$customerId && !$licenseKey) {
            return;
        }

        if($this->isApiClientConfigured()) {
            $apiCredentials = $this->getApiCredentialsFromDatabase();
        }else{
            Mage::getSingleton('adminhtml/session')->addWarning('Please, go to Data Import Configuration tab and configure new api client, to allow communication between your store and Yoochoose!');
        }


        try {
            $body = array(
                'base' => array(
                    'type' => 'MAGENTO',
                    'pluginId' => Mage::getStoreConfig('yoochoose/general/plugin_id'),
                    'endpoint' => Mage::getStoreConfig('yoochoose/general/endpoint'),
                    'appKey' => $apiCredentials['consumerKey'],
                    'appSecret' => $apiCredentials['consumerSecret'],
                ),
                'frontend' => array(
                    'design' => Mage::getStoreConfig('yoochoose/general/design'),
                ));

            $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/';
            $url .= (Mage::getStoreConfig('yoochoose/general/endpoint_overwrite') ? 'update?createIfNeeded' : 'create?recheckType') . '=true&fallbackDesign=true';
            Mage::getModel('core/config')->saveConfig('yoochoose/general/endpoint_overwrite', 0, $scopeName, $scopeId);

            Mage::helper('yoochoose_jstracking')->_getHttpPage($url, $body, $customerId, $licenseKey);
            Mage::getSingleton('adminhtml/session')->addSuccess('Plugin registrated successfully');

        } catch (Exception $ex) {
            Mage::throwException('Plugin registration failed: ' . $ex->getMessage());
        }
    }

    /**
     * Adds additional filters to search results
     *
     * @param Varien_Event_Observer $observer
     */
    public function filterParameters(Varien_Event_Observer $observer)
    {
        $manufacturerName = Mage::app()->getRequest()->getParam('manufacturer');
        $block = Mage::app()->getLayout()->getBlock('search_result_list');
        if ($block && $manufacturerName) {
            $productModel = Mage::getModel('catalog/product');
            $attr = $productModel->getResource()->getAttribute('manufacturer');
            if ($attr->usesSource()) {
                $manufacturerId = $attr->getSource()->getOptionId($manufacturerName);
            }

            $collection = $block->getLoadedProductCollection();
            $collection->addAttributeToFilter('manufacturer', $manufacturerId);
        }
    }

    /**
     * Helper function to check if yoochoose api client is configured.
     *
     * @return bool
     */
    private function isApiClientConfigured(){
        $postData = Mage::app()->getRequest()->getPost();

        return($postData['groups']['data_export']['fields']['consumer_key']['value'] !== null) ? true :false;
    }

    /**
     * Prepares api credentials for sending to Yoochoose.
     *
     * @return array
     */
    private function getApiCredentialsFromDatabase(){
        $oauthConsumer = Mage::getModel('oauth/consumer')->getCollection()
            ->addFieldToFilter('name', 'Yoochoose-Consumer')
            ->getFirstItem();

        $apiCredentials['consumerKey'] = $oauthConsumer->getKey();
        $apiCredentials['consumerSecret'] = $oauthConsumer->getSecret();

        return $apiCredentials;
    }
}