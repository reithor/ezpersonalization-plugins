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
 * YooChoose JS-Tracking Html page block
 *
 * @category   YooChoose
 * @package    YooChoose_JsTracking
 * @author     YooChoose, yoochoose.net
 */
class YooChoose_JsTracking_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * Inject YooChoose SJ Tracking script and related data into head.
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
                $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s"></script>',
                    preg_replace('(^https?:)', '', Mage::getStoreConfig('yoochoose/yoochoose_script/url')));
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

        $order = '{}';
        if ($this->getOrderId()) {
            $order = json_encode($this->getOrderData($this->getOrderId()));
        }
        
        $itemTypeId = Mage::getStoreConfig('yoochoose/yoochoose_product/itemtypeid');
        $language = Mage::getStoreConfig('yoochoose/yoochoose_product/language');

        return '<script type="text/javascript">var yc_trackid = ' . $customerId . ', yc_orderData = ' . $order . ', yc_itemType = ' 
                . $itemTypeId . ', yc_language = \'' . $language . '\';</script>';
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
}
