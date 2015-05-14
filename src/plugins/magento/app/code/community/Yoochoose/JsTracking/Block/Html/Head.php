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
 * Yoochoose JS-Tracking Html page block
 *
 * @category   Yoochoose
 * @package    Yoochoose_JsTracking
 * @author     Yoochoose, yoochoose.net
 */
class Yoochoose_JsTracking_Block_Html_Head extends Mage_Page_Block_Html_Head
{

    /**
     * Inject Yoochoose SJ Tracking script and related data into head.
     *
     * @param array  &$lines
     * @param string $itemIf
     * @param string $itemType
     * @param string $itemParams
     * @param string $itemName
     * @param array  $itemThe
     */
    protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe)
    {
        switch ($itemType) {
            case 'yoochoose_js':
                $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s"></script>', preg_replace('(^https?:)', '', Mage::getStoreConfig('yoochoose/yoochoose_script/url')));
                $lines[$itemIf]['other'][] = $this->injectTracking();
                break;

            default:
                parent::_separateOtherHtmlHeadElements($lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe);
                break;
        }
    }

    private function injectTracking()
    {
        $customerId = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }

        $order = null;
        if ($this->getOrderId()) {
            $order =  $this->getOrderData($this->getOrderId());
        }

        $itemTypeId = Mage::getStoreConfig('yoochoose/general/itemtypeid');
        $language = Mage::getStoreConfig('yoochoose/general/language');
        $currentPage = $this->getCurrentPage();
        
        if (Mage::getStoreConfig('yoochoose/general/language_country')) {
            $language = substr($language, 0, strpos($language, '_'));
        }

        $json = array(
            'trackid' => $customerId,
            'orderData' => $order,
            'itemType' => $itemTypeId,
            'language' => $language,
            'currentPage' => $currentPage,
            'products' => $this->getPageProducts($currentPage),
            'boxes' => $this->getRecommendBoxes($currentPage),
        );

        return '<script type="text/javascript">var yc_config_object = ' . json_encode($json) . ';</script>';
    }

    private function getOrderData($orderId)
    {
        $collection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('entity_id', array('in' => array($orderId)));
        $result = array();
        foreach ($collection as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = array(
                    'id' => $item->getProductId(),
                    'price' => $item->getBasePrice(),
                    'quantity' => $item->getQtyOrdered(),
                    'currency' => $order->getBaseCurrencyCode(),
                );
            }
        }

        return $result;
    }

    private function getCurrentPage()
    {
        $request = $this->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        if ($module == 'cms' && $action == 'index') {
            return 'home';
        }

        if (Mage::registry('current_product')) {
            return 'product';
        }

        if ($module == 'checkout' && $controller == 'cart' && $action == 'index') {
            return 'cart';
        }
        
        if (Mage::registry('current_category')) {
            return 'category';
        }

        return false;
    }

    private function getRecommendBoxes($page)
    {
        if (!$page) {
            return false;
        }

        $result = array();
        switch ($page) {
            case 'home':
                $result[] = $this->createRecommendBox('bestseller');
                $result[] = $this->createRecommendBox('personal');
                break;
            case 'product':
                $result[] = $this->createRecommendBox('upselling');
                $result[] = $this->createRecommendBox('related');
                break;
            case 'cart':
                $result[] = $this->createRecommendBox('crossselling');
                break;
            case 'category':
                $result[] = $this->createRecommendBox('category_page');
                break;
        }

        return $result;
    }

    private function createRecommendBox($id)
    {
        return array(
            'id' => $id,
            'title' => Mage::getStoreConfig("yoochoose/recommendation_blocks/{$id}_title"),
            'display' => !Mage::getStoreConfig("yoochoose/recommendation_blocks/{$id}_display"),
        );
    }

    private function getPageProducts($page)
    {
        $result = array();
        
        if ($page === 'product') {
            $result[] = Mage::registry('current_product')->getId();
        } else if ($page === 'cart') {
            $cart = Mage::getModel('checkout/cart')->getQuote();
            foreach ($cart->getAllItems() as $item) {
                $result[] = $item->getProduct()->getId();
            }
        } else if ($page === 'category') {
            $products = Mage::getSingleton('catalog/layer')->getProductCollection();
            foreach ($products as $product) {
                $result[] = $product->getId();
            }
        }

        return implode(',', $result);
    }

}