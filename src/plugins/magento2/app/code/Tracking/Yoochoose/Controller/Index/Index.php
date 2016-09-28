<?php

namespace Yoochoose\Tracking\Controller\Index;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{

    /** @var  \Magento\Framework\Controller\Result\Json */
    protected $resultJsonFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scope;

    /**
     * @var StoreManagerInterface
     */
    private $store;


    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scope
     * @param StoreManagerInterface $store
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, ScopeConfigInterface $scope, StoreManagerInterface $store)
    {

        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
        $this->scope = $scope;
        $this->store = $store;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $productIds = $this->getRequest()->getParam('productIds');

        /** @var \Magento\Catalog\Model\Product\Media\Config $helper */
        $helper = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config');
        $thumbnailHolder = $this->scope->getValue('catalog/placeholder/thumbnail_placeholder');
        $placeholderPath = $helper->getBaseMediaUrl() . '/placeholder/' . $thumbnailHolder;

        /** @var \Magento\Framework\Pricing\Helper\Data $priceHelper */
        $priceHelper = $this->_objectManager->get('Magento\Framework\Pricing\Helper\Data');
        $attributes = ['entity_id', 'name', 'thumbnail', 'price', 'url_path'];

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->_objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->setStoreId($this->store->getStore()->getId())
            ->addAttributeToSelect($attributes)
            ->addFinalPrice()
            ->addAttributeToFilter('status', ['eq' => Status::STATUS_ENABLED])
            ->addAttributeToFilter('entity_id', ['in' => explode(',', $productIds)]);

        $products = [];

        /** @var \Magento\Framework\Url\EncoderInterface */
        $urlEncoder = $this->_objectManager->get('Magento\Framework\Url\EncoderInterface');
        $newUenc = $urlEncoder->encode($this->getCurrentURL());

        /** @var \Magento\CatalogWidget\Block\Product\ProductsList */
        $block = $this->_objectManager->get('\Magento\CatalogWidget\Block\Product\ProductsList');

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $postData = false;
            $compareData = false;
            $wishlistData = false;
            $image = $product->getData('thumbnail');

            if ($product->isSaleable()){
                /** @var \Magento\Framework\Data\Helper\PostHelper */
                $postDataHelper = $this->_objectManager->get('Magento\Framework\Data\Helper\PostHelper');
                $url = $block->getAddToCartUrl($product);
                $oldUenc = $this->getStringBetween($url, 'uenc/', ",/product");
                $url = str_replace($oldUenc, $newUenc, $url);
                $postData = $postDataHelper->getPostData($url, ['product' => $product->getId()]);
                $postData = $this->changeUenc($postData, $newUenc);
            }

            if ($block->getAddToCompareUrl()){
                /** @var \Magento\Catalog\Helper\Product\Compare */
                $compareHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product\Compare');
                $compareData = $compareHelper->getPostDataParams($product);
                $compareData = $this->changeUenc($compareData, $newUenc);
            }

            /** @var \Magento\Wishlist\Helper\Data */
            if ($this->_objectManager->get('Magento\Wishlist\Helper\Data')->isAllow()){
                $wishlistData = $block->getAddToWishlistParams($product);
            }

            $products[] = [
                'id' => $product->getId(),
                'link' => $product->getUrlModel()->getUrl($product),
                'price' => $priceHelper->currency($product->getFinalPrice(), true, false),
                'image' => ($image ? $helper->getMediaUrl($image) : ($thumbnailHolder ? $placeholderPath : null)),
                'title' => $product->getName(),
                'postData' => $postData,
                'wishlistData' => $wishlistData,
                'compareData' => $compareData,
            ];
        }

//        header('Content-Type: application/json;');

        $result = $this->resultJsonFactory->create();
        $result->setData(array_values($products));
        return $result;
    }

    private function getStringBetween($string, $start, $finish) {
        $string = " ".$string;
        $position = strpos($string, $start);
        if ($position == 0) return "";
        $position += strlen($start);
        $length = strpos($string, $finish, $position) - $position;
        return substr($string, $position, $length);
    }

    private function changeUenc($data, $newUenc){
        $data = json_decode($data);
        $data->data->uenc = $newUenc;
        return json_encode($data);
    }

    private function getCurrentURL(){
        $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }
}
