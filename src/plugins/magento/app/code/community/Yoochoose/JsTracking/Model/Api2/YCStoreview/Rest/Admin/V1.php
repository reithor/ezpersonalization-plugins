<?php

class Yoochoose_JsTracking_Model_Api2_YCStoreView_Rest_Admin_V1 extends Yoochoose_JsTracking_Model_Api2_YCStoreView
{
    protected function getStoreRelations()
    {
        $result = array();
        $storeId = $this->_getStore()->getId();
        $stores = Mage::app()->getStores();
        $justLanguage = Mage::getStoreConfig('yoochoose/general/language_country', $storeId);
        foreach ($stores as $store) {
            Mage::app()->setCurrentStore($store['store_id']);
            $lang = Mage::getStoreConfig('yoochoose/general/language', $store['store_id']);
            if ($justLanguage) {
                $lang = substr($lang, 0, strpos($lang, '_'));
            }

            $result[] = array(
                'id' => $store['store_id'],
                'name' => $store['name'],
                'item_type_id' => Mage::getStoreConfig('yoochoose/general/itemtypeid', $store['store_id']),
                'languange' => str_replace('_', '-', $lang),
            );
        }

        if (empty($result)) {
            $this->getResponse()->setHeader('HTTP/1.0','204', true);
        }

        return array('views' => array($result));
    }
    
    /**
     * Retrieve list of customers.
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        return $this->getStoreRelations();
    }

}
