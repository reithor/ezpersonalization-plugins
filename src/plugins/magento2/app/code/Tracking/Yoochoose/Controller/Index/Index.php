<?php

namespace Yoochoose\Tracking\Controller\Index;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{

    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

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
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scope
     * @param StoreManagerInterface $store
     */
    public function __construct(Context $context, PageFactory $resultPageFactory, ScopeConfigInterface $scope, StoreManagerInterface $store)
    {

        $this->resultPageFactory = $resultPageFactory;
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
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $image = $product->getData('thumbnail');
            $products[] = [
                'id' => $product->getId(),
                'link' => $product->getProductUrl(),
                'price' => $priceHelper->currency($product->getFinalPrice(), true, false),
                'image' => ($image ? $helper->getMediaUrl($image) : ($thumbnailHolder ? $placeholderPath : null)),
                'title' => $product->getName(),
            ];
        }

        header('Content-Type: application/json;');
        return(json_encode(array_values($products)));
    }
}
