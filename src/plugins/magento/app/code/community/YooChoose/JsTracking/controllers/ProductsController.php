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
 * YooChoose JS-Tracking Products Controller
 *
 * @category   YooChoose
 * @package    YooChoose_JsTracking
 * @author     YooChoose, yoochoose.net
 */
class YooChoose_JsTracking_ProductsController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $productIds = $this->getRequest()->getParam('productIds');

        $helper = Mage::getModel('catalog/product_media_config');
        $storeId = Mage::app()->getStore('default')->getId();
        $attributes = array('name', 'thumbnail', 'price', 'url_path');
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $collection = Mage::getResourceModel('catalog/product_collection')->
                setStoreId($storeId)->
                addAttributeToSelect($attributes)->
                addFinalPrice()->
                addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))->
                addAttributeToFilter('entity_id', array('in' => explode(',', $productIds)));

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(array('e.entity_id'));
        $products = $collection->load()->toArray();

        foreach ($products as &$product) {
            unset($product['stock_item']);
            $product['thumbnail'] = $helper->getMediaUrl($product['thumbnail']);
            $product['price'] = round($product['price'], 2);
        }

        $result = array('products' => array_values($products), 'currency' => $currency);
        header('Content-Type: application/json;');
        exit(json_encode($result));
    }
    
}
