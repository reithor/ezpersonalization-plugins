<?php

namespace Yoochoose\Tracking\Model\Api;

use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Yoochoose\Tracking\Api\YoochooseInterface;
use Zend_Db_Select;

class Yoochoose implements YoochooseInterface
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * Yoochoose constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     * @param Response $response
     * @param Request $request
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        Response $response,
        Request $request,
        UrlFinderInterface $urlFinder
    )
    {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->response = $response;
        $this->request = $request;
        $this->om = ObjectManager::getInstance();
        $this->urlFinder = $urlFinder;
    }

    /**
     * Returns list of store views with language codes
     *
     * @api
     * @return mixed
     */
    public function getStoreViews()
    {
        $result = [];
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $language = $this->config->getValue('general/locale/code', 'stores', $store->getCode());
            $result[] = [
                'id' => $store['store_id'],
                'name' => $store['name'],
                'item_type_id' => $this->config->getValue('yoochoose/general/item_type', 'store', $store->getCode()),
                'language' => str_replace('_', '-', $language),
            ];
        }

        if (empty($result)) {
            $this->response->setStatusCode(204);
        }

        return $result;
    }

    /**
     * Returns list of subscribers
     *
     * @api
     * @return mixed
     */
    public function getSubscribers()
    {
        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('offset');
        $storeId = $this->request->getParam('storeId');

        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->om->get('Magento\Customer\Model\ResourceModel\Customer\Collection');
        if ($storeId) {
            $collection->addAttributeToFilter('store_id', $storeId);
        }

        $collection->joinTable(['ns' => 'newsletter_subscriber'], 'customer_id=entity_id', ['subscriber_status'], 'ns.subscriber_status=1', 'left');
        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $collection->getSelect()->limit($limit, $offset);
        }

        $subscribers = [];
        /** @var \Magento\Customer\Model\Customer $item */
        foreach ($collection as $item) {
            $subscribers[] = [
                'id' => $item->getId(),
                'email' => $item->getEmail(),
                'name' => $item->getName(),
                'group' => $item->getGroupId(),
                'gender' => $item->getData('gender'),
                'subscribed' => $item->getData('subscriber_status') == Subscriber::STATUS_SUBSCRIBED,
            ];
        }

        return $subscribers;
    }

    /**
     * Returns list of categories that are visible on frontend
     *
     * @api
     * @return mixed
     */
    public function getCategories()
    {
        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('offset');
        $storeId = $this->request->getParam('storeId');

        /* @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $this->om->get('Magento\Catalog\Model\ResourceModel\Category\Collection');
        $categoryCollection->setStoreId($storeId)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect(['url_path', 'name', 'level']);

        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $categoryCollection->getSelect()->limit($limit, $offset);
        }

        $result = [];
        /** @var \Magento\Catalog\Model\Category  $category */
        foreach ($categoryCollection as $category) {
            $result[] = [
                'id' => $category->getId(),
                'path' => $category->getPath(),
                'url' => $category->getUrl(),
                'name' => $category->getName(),
                'level' => $category->getLevel(),
                'parentId' => $category->getParentId(),
            ];
        }

        if (empty($result)) {
            $this->response->setStatusCode(204);
        }

        return $result;
    }

    /**
     * Returns list of products that are visible on frontend
     *
     * @api
     * @return mixed
     */
    public function getProducts()
    {
        $categoriesRel = [];
        $products = [];
        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('offset');
        $storeId = $this->request->getParam('storeId');
        $storeCode = $this->storeManager->getStore($storeId)->getCode();

        /** @var \Magento\Catalog\Model\Product\Media\Config $helper */
        $helper = $this->om->get('Magento\Catalog\Model\Product\Media\Config');
        $placeHolderPath = $helper->getBaseMediaUrl(). '/placeholder/';
        $imagePh = $this->config->getValue("catalog/placeholder/image_placeholder", 'store', $storeCode);
        $thumbPh = $this->config->getValue("catalog/placeholder/thumbnail_placeholder", 'store', $storeCode);


        /* @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->om->get('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->setStoreId($storeId);
        $collection->addFieldToFilter('visibility', ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]);
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(['e.entity_id']);
        $collection->addAttributeToSelect(['name', 'thumbnail', 'description', 'price', 'url_path', 'image', 'manufacturer', 'qty']);
        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $collection->getSelect()->limit($limit, $offset);
        }

        $collection->setOrder('entity_id', 'ASC');

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $id = $product->getId();
            $manufacturer = $product->getAttributeText('manufacturer');
            $temp = array(
                'id' => $id,
                'name' => $product->getName(),
                'description' => $product->getData('description'),
                'price' => $product->getPrice(),
                'url' => $product->getProductUrl(),
                'image' => ($product->getImage() ? $helper->getMediaUrl($product->getImage()) :
                    ($imagePh ? $placeHolderPath . $imagePh : null)),
                'icon_image' => ($product->getData('thumbnail') ? $helper->getMediaUrl($product->getData('thumbnail')) :
                    ($thumbPh ? $placeHolderPath . $thumbPh : null)),
                'manufacturer' => $manufacturer ? $manufacturer : null,
                'categories' => [],
            );
            $imageInfo = getimagesize($temp['image']);
            if (is_array($imageInfo)) {
                $temp['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
            }

            // Categories
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($product->getCategoryCollection() as $category) {
                $category->getUrl();
                $categoryId = $category->getId();
                if (!array_key_exists($categoryId, $categoriesRel)) {
                    $rewrite = $this->urlFinder->findOneByData([
                        UrlRewrite::ENTITY_ID => $categoryId,
                        UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                        UrlRewrite::STORE_ID => $category->getStoreId(),
                    ]);

                    // remove .html suffix if it exists
                    $parts = explode('.', $rewrite->getRequestPath());
                    $categoriesRel[$categoryId] = $parts[0];
                }

                $temp['categories'][] = $categoriesRel[$categoryId];
            }

            $products[$id] = $temp;
        }

        if (empty($products)) {
            $this->response->setStatusCode(204);
        }

        return $products;
    }

}