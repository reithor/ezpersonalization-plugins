<?php

namespace Yoochoose\Tracking\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;

class Head extends Template
{
    const YOOCHOOSE_CDN_SCRIPT = '//event.yoochoose.net/cdn';
    const AMAZON_CDN_SCRIPT = '//cdn.yoochoose.net';

    /** @var  ObjectManager */
    private $objectManager;

    /**
     * Returns script with js and css url, and script configuration data
     * @return string
     */
    public function renderScripts()
    {
        $yoochoose = $this->getRequest()->getParam('yoochoose');
        if ($yoochoose == 'off') {
            return '';
        }

        $this->objectManager = ObjectManager::getInstance();
        $mandator = $this->_scopeConfig->getValue('yoochoose/general/customer_id', 'stores');
        $plugin = $this->_scopeConfig->getValue('yoochoose/general/plugin_id', 'stores');
        $plugin = $plugin ? '/' . $plugin : '';
        $scriptOverwrite = $this->_scopeConfig->getValue('yoochoose/script/script_id', 'stores');

        if ($scriptOverwrite) {
            $scriptOverwrite = (!preg_match('/^(http|\/\/)/', $scriptOverwrite) ? '//' : '') . $scriptOverwrite;
            $scriptUrl = preg_replace('(^https?:)', '', $scriptOverwrite);
        } else {
            $scriptUrl = $this->_scopeConfig->getValue('yoochoose/script/cdn_source', 'stores') ?
            self::AMAZON_CDN_SCRIPT : self::YOOCHOOSE_CDN_SCRIPT;
        }

        $scriptUrl = rtrim($scriptUrl, '/') . "/v1/{$mandator}{$plugin}/tracking.";
        $result = sprintf('<script type="text/javascript" src="%s"></script>', $scriptUrl . 'js');
        $result .= sprintf('<link type="text/css" rel="stylesheet" href="%s">', $scriptUrl . 'css');
        $result .= $this->injectTrackingData();

        return $result;
    }

    /**
     * Injects configuration object that provides information for tracking script
     * @return string
     */
    private function injectTrackingData()
    {
        $language = $this->_scopeConfig->getValue('general/locale/code', 'stores');
        $customerId = 0;

        /** @var \Magento\Framework\App\Http\Context $context */
        $context = $this->objectManager->get('Magento\Framework\App\Http\Context');
        $customerLogged = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

        /** @var \Magento\Customer\Model\Session $customerSession */
        $customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getCustomerId();
        }

        $order = null;
        if ($this->getData('orderId')) {
            $order = $this->getOrderData($this->getData('orderId'));
        }

        $itemTypeId = $this->_scopeConfig->getValue('yoochoose/general/item_type', 'stores');
        $currentPage = $this->getCurrentPage();

        $enableSearch = $this->_scopeConfig->getValue('yoochoose/search/search_enable', 'stores');

        $storeViewId = $this->_storeManager->getStore()->getId();

        $json = [
            'url' => $this->_storeManager->getStore()->getBaseUrl(),
            'trackid' => (int)$customerId,
            'orderData' => $order,
            'itemType' => $itemTypeId,
            'language' => str_replace('_', '-', $language),
            'currentPage' => $currentPage,
            'productIds' => $this->getContextProductIds($currentPage),
            'enableSearch' => $enableSearch,
            'storeViewId' => $storeViewId,
            'customerLogged' => $customerLogged
        ];

        return sprintf('<script type="text/javascript">var yc_config_object = %s;</script>', json_encode($json));
    }

    /**
     * Returns array of products that are in order with given id ($orderId)
     * @param int $orderId
     * @return array
     */
    private function getOrderData($orderId)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->get('Magento\Sales\Model\Order')->load($orderId);

        $result = [];
        $currency = $order->getOrderCurrencyCode();

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $result[] = [
                'id' => $this->getParentProductId($item->getProduct()),
                'qty' => $item->getQtyOrdered(),
                'price' => $item->getBasePrice(),
                'currency' => $currency,
            ];
        }

        return $result;
    }

    /**
     * Returns current page that is displayed on frontend
     * @return null|string
     */
    private function getCurrentPage()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        $module = $this->getRequest()->getModuleName();
        $action = $this->getRequest()->getActionName();

        switch ($module) {
            case 'cms':
                return $action === 'index' ? 'home' : null;
            case 'catalog':
                return $registry->registry('current_product') ? 'product' : 'category';
            case 'checkout':
                return ($action === 'index' ? 'cart' : ($action === 'success' ? 'buyout' : null));
        }

        return null;
    }

    /**
     * Returns array of product ids from page context
     * @param string $currentPage
     * @return string
     */
    private function getContextProductIds($currentPage)
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get('Magento\Framework\Registry');
        $result = [];

        if ($currentPage === 'product') {
            $result[] = $this->getParentProductId($registry->registry('current_product'));
        } elseif ($currentPage === 'cart') {
            /** @var \Magento\Checkout\Model\Cart $cart */
            $cart = $this->objectManager->get('Magento\Checkout\Model\Cart');
            foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
                $result[] = $this->getParentProductId($item->getProduct());
            }
        } elseif ($currentPage === 'category') {
            /** @var \Magento\Catalog\Block\Product\ListProduct $block */
            /** @var \Magento\Catalog\Model\Product $product */
            $block = $this->_layout->getBlock('category.products.list');
            foreach ($block->getLoadedProductCollection() as $product) {
                $result[] = $this->getParentProductId($product);
            }
        }

        return implode(',', $result);
    }

    /**
     * Retrieves parent product id if product is configurable
     * @param $product
     * @return mixed
     */
    private function getParentProductId($product)
    {
        $id = $product->getId();
        $type = $product->getTypeId();
        if ($type == 'configurable') {
            $parents = $this->objectManager->
            create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->
            getParentIdsByChild($id);
            if (!empty($parents)) {
                return $parents[0];
            }
        }

        return $id;
    }
}
