<?php

class YooChoose_JsTracking_Model_Observer {

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
}