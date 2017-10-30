<?php

namespace Yoochoose\Tracking\Model\Api;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Yoochoose\Tracking\Api\YoochooseInterface;
use Zend_Db_Select;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

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
     * @var \Magento\Framework\Webapi\Rest\Response
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
     * @var ImageFactory
     */
    private $productImageHelper;
    /**
     * @var Emulation
     */
    private $appEmulation;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var array
     */
    private $loadedCategories = [];
    /**
     * @var int
     */
    private $rootCategoryId = 1;

    /**
     * Yoochoose constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     * @param \Magento\Framework\Webapi\Rest\Response $response
     * @param Request $request
     * @param ImageFactory $productImageHelper
     * @param UrlFinderInterface $urlFinder
     * @param ProductRepository $productRepository
     * @param Emulation $appEmulation
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        \Magento\Framework\Webapi\Rest\Response $response,
        Request $request,
        ImageFactory $productImageHelper,
        UrlFinderInterface $urlFinder,
        ProductRepository $productRepository,
        Emulation $appEmulation
    )
    {

        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->response = $response;
        $this->request = $request;
        $this->om = ObjectManager::getInstance();
        $this->urlFinder = $urlFinder;
        $this->productImageHelper = $productImageHelper;
        $this->appEmulation = $appEmulation;
        $this->productRepository = $productRepository;
        $this->productRepository->cleanCache();
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
        $storeId = $this->request->getParam('storeViewId');

        /** @deprecated storeId is deprecated */
        if (!isset($storeId) || empty($storeId)) {
            $storeId = $this->request->getParam('storeId');
        }

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
        $storeId = $this->request->getParam('storeViewId');

        /** @deprecated storeId is deprecated */
        if (!isset($storeId) || empty($storeId)) {
            $storeId = $this->request->getParam('storeId');
        }

        if (!isset($storeId) || empty($storeId)) {
            $result = array();
            $storeIds = $this->getStoreViews();

            /** Divides limit number evenly between store views */
            $storeIdNumber = count($storeIds);
            $limit /= $storeIdNumber;
            $limit = floor($limit);
            $limit > 1 ? : $limit = 1;

            foreach ($storeIds as $storeId) {
                $result[] = $this->getCategoriesHelper($limit, $offset, $storeId['id']);

            }
            $result = call_user_func_array('array_merge', $result);

            return $result;

        } else {
            return $this->getCategoriesHelper($limit, $offset, $storeId);
        }
    }

    /**
     * Returns list of products that are visible on frontend
     *
     * @api
     * @return mixed
     */
    public function getProducts()
    {
        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('offset');
        $storeId = $this->request->getParam('storeViewId');

        /** @deprecated storeId is deprecated */
        if (!isset($storeId) || empty($storeId)) {
            $storeId = $this->request->getParam('storeId');
        }

        if (!isset($storeId) || empty($storeId)) {
            $result = array();
            $storeIds = $this->getStoreViews();

            /** Divides limit number evenly between store views */
            $storeIdNumber = count($storeIds);
            $limit /= $storeIdNumber;
            $limit = floor($limit);
            $limit > 1 ? : $limit = 1;

            foreach ($storeIds as $storeId) {
                $result[] = $this->getProductsHelper($limit, $offset, $storeId['id']);
            }
            $result = call_user_func_array('array_merge', $result);

            return $result;
        } else {
            return $this->getProductsHelper($limit, $offset, $storeId);

        }
    }

    /**
     * Returns list of manufacturers that are visible on frontend
     *
     * @return mixed
     */
    public function getVendors()
    {
        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('offset');
        $storeId = $this->request->getParam('storeViewId');

        /** @deprecated storeId is deprecated */
        if (!isset($storeId) || empty($storeId)) {
            $storeId = $this->request->getParam('storeId');
        }

        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
        $eavConfig = $this->om->create('\Magento\Eav\Model\Config');
        $attribute = $eavConfig->getAttribute('catalog_product', 'manufacturer')->setStoreId($storeId);
        $vendors = $attribute->getSource()->getAllOptions();
        $searchUrl = rtrim($baseUrl, '/') . '/catalogsearch/result/?';

        if ($limit && is_numeric($limit)) {
            $limit = (int)$limit;
            $offset = $offset && is_numeric($offset) ? $offset : 0;
        }

        $result = [];
        $i = 0;
        foreach ($vendors as $key => $option) {
            //ignore empty values
            if (empty($option['value'])) {
                continue;
            }

            //if value is in our offset add it to results
            if ($i >= $offset) {
                $name = $option['label'];
                $id = $option['value'];
                $result[$id] = [
                    'id' => $id,
                    'name' => $name,
                    'link' => $searchUrl . http_build_query(['q' => $name, 'manufacturer' => $id]),
                ];
            }

            $i++;
            //check limit of results
            if ($limit && count($result) === $limit) {
                break;
            }
        }

        if (empty($result)) {
            $this->response->setStatusCode(204);
        }

        return $result;
    }

    /**
     * @param $storeId
     * @param $productModel
     * @return string
     */
    protected function makeSmallImage($storeId, $productModel)
    {
        $resizedImage = $this->productImageHelper->create()->init($productModel, 'product_small_image')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(100, 100)
            ->getUrl();

        return $resizedImage;
    }

    /**
     * @param $value
     * @return string|array
     */
    protected function getCustomAttributeValue($value)
    {
        $result = '';
        if (is_object($value)) {
            $result = (get_class($value) === 'Magento\Framework\Phrase' ? $value->getText() : '');
        } else if (is_array($value) || is_string($value)) {
            $result = $value;
        }

        return $result;
    }

    /**
     * This method returns categories filtered by storeView
     *
     * @param $limit
     * @param $offset
     * @param $storeId
     * @return array
     */
    protected function getCategoriesHelper($limit, $offset, $storeId)
    {
        $result = [];
        $this->appEmulation->startEnvironmentEmulation($storeId);

        $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
        $id = $this->storeManager->getStore($storeId)->getRootCategoryId();
        /* @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $this->om->create('Magento\Catalog\Model\ResourceModel\Category\Collection');
        $categoryCollection
            ->setStoreId($storeId)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('path', ['like' => "$rootId/$id/%"])
            ->addAttributeToSelect(['url_path', 'name', 'level', 'store_id']);

        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $categoryCollection->getSelect()->limit($limit, $offset);
        }

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categoryCollection as $category) {
            $category->setStoreId($storeId);
            $category->getUrlInstance()->setScope($storeId);
            $rewrite = $this->getCategoryUrlRewrite($category->getId(), $storeId);
            $result[] = [
                'id' => $category->getId(),
                'path' => $category->getPath(),
                'url' => $rewrite ? $baseUrl . $rewrite->getRequestPath() : $category->getUrl(),
                'name' => $category->getName(),
                'level' => $category->getLevel(),
                'parentId' => $category->getParentId(),
                'storeViewId' => $storeId,
            ];
        }
        $categoryCollection->clear();
        $this->appEmulation->stopEnvironmentEmulation();

        if (empty($result)) {
            $this->response->setStatusCode(204);
        }

        return $result;
    }

    /**
     * This method returns products filtered by storeView.
     *
     * @param $limit
     * @param $offset
     * @param $storeId
     * @return array
     */
    protected function getProductsHelper($limit, $offset, $storeId)
    {
        $this->appEmulation->startEnvironmentEmulation($storeId);
        $products = [];

        /** @var \Magento\Catalog\Model\Product\Media\Config $helper */
        $helper = $this->om->get('Magento\Catalog\Model\Product\Media\Config');
        $placeHolderPath = $helper->getBaseMediaUrl() . '/placeholder/';
        $imagePh = $this->config->getValue("catalog/placeholder/image_placeholder", 'store', $storeId);
        $this->rootCategoryId = $this->storeManager->getStore($storeId)->getRootCategoryId();

        /* @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->om->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->setStoreId($storeId);
        $collection->addStoreFilter($storeId);
        $collection->addFieldToFilter('visibility', ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]);
        $collection->addFieldToFilter('status', ['eq' => Status::STATUS_ENABLED]);
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(['e.entity_id']);
        $collection->addAttributeToSelect(['*']);
        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $collection->getSelect()->limit($limit, $offset);
        }

        $collection->setOrder('entity_id', 'ASC');

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $id = $product->getId();
            $product->setStoreId($storeId);
            $productModel = $this->productRepository->getById($id, true, $storeId, true);
            $manufacturer = $product->getAttributeText('manufacturer');
            $temp = [
                'id' => $id,
                'name' => $product->getName(),
                'description' => $product->getData('description'),
                'price' => number_format((float)$product->getPrice(), 2),
                'url' => $product->getUrlInStore(),
                'image' => ($product->getImage() ? $helper->getMediaUrl($product->getImage()) :
                    ($imagePh ? $placeHolderPath . $imagePh : null)),
                'manufacturer' => $manufacturer ? $manufacturer : null,
                'categories' => [],
                'storeViewId' => $storeId,
            ];

            $temp['icon_image'] = $this->makeSmallImage($storeId, $productModel);

            $customAttributes = $product->getCustomAttributes();
            foreach ($customAttributes as $customAttribute) {
                $customKey = $customAttribute->getAttributeCode();
                if (!isset($temp[$customKey])) {
                    $customValue = $product->getAttributeText($customKey);
                    $temp[$customKey] = $this->getCustomAttributeValue($customValue);
                }
            }

            // Categories
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($product->getCategoryCollection() as $category) {
                $categoryId = $category->getId();
                if (!array_key_exists($categoryId, $this->loadedCategories)) {
                    $this->loadedCategories[$categoryId] = $this->buildCategoryPath($categoryId, $storeId);
                }

                $temp['categories'][] = $this->loadedCategories[$categoryId];
            }

            $products[$id] = $temp;
        }

        $this->appEmulation->stopEnvironmentEmulation();

        if (empty($products)) {
            $this->response->setStatusCode(204);
        }

        return $products;
    }

    /**
     * @param $categoryId
     * @param $storeId
     * @return string
     */
    private function buildCategoryPath($categoryId, $storeId)
    {
        if (array_key_exists($categoryId, $this->loadedCategories)) {
            return $this->loadedCategories[$categoryId];
        }

        /** @var Category $category */
        $category = $this->om->get(CategoryRepositoryInterface::class)->get($categoryId, $storeId);
        $categoryPath = $category->getName();
        $parentId = $category->getParentId();
        if ($parentId && $parentId !== $this->rootCategoryId) {
            $categoryPath = $this->buildCategoryPath($parentId, $storeId) . '/' . $categoryPath;
        }

        $categoryPath = htmlspecialchars_decode($categoryPath);
        $this->loadedCategories[$categoryId] = $categoryPath;

        return $categoryPath;
    }

    /**
     * Returns url rewrite for category
     *
     * @param $categoryId
     * @param $storeId
     * @return UrlRewrite|null
     */
    protected function getCategoryUrlRewrite($categoryId, $storeId)
    {
        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::ENTITY_ID => $categoryId,
            UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::STORE_ID => $storeId,
        ]);

        return $rewrite;
    }
}
