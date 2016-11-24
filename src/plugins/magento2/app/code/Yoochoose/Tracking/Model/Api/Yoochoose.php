<?php

namespace Yoochoose\Tracking\Model\Api;

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
        $storeId =  ($storeId ? : $this->request->getParam('storeViewId'));

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
        $storeId =  ($storeId ? : $this->request->getParam('storeViewId'));

        /* @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $categoryCollection = $this->om->create('Magento\Catalog\Model\ResourceModel\Category\Collection');
        $categoryCollection->setStoreId($storeId)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect(['url_path', 'name', 'level']);

        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $categoryCollection->getSelect()->limit($limit, $offset);
        }

        $result = [];
        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categoryCollection as $category) {
            $result[] = [
                'id' => $category->getId(),
                'path' => $category->getPath(),
                'url' => $category->getUrl(),
                'name' => $category->getName(),
                'level' => $category->getLevel(),
                'parentId' => $category->getParentId(),
                'storeId' => $category->getStoreId(),
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
        $storeId =  ($storeId ? : $this->request->getParam('storeViewId'));
        $storeCode = $this->storeManager->getStore($storeId)->getCode();

        /** @var \Magento\Catalog\Model\Product\Media\Config $helper */
        $helper = $this->om->get('Magento\Catalog\Model\Product\Media\Config');
        $placeHolderPath = $helper->getBaseMediaUrl() . '/placeholder/';
        $imagePh = $this->config->getValue("catalog/placeholder/image_placeholder", 'store', $storeCode);

        /* @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->om->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->setStoreId($storeId);
        $collection->addFieldToFilter('visibility', ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]);
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
            $productModel = $this->productRepository->getById($id, true, $storeId);
            $manufacturer = $product->getAttributeText('manufacturer');
            $temp = [
                'id' => $id,
                'name' => $product->getName(),
                'description' => $product->getData('description'),
                'price' => $product->getPrice(),
                'url' => $product->getProductUrl(),
                'image' => ($product->getImage() ? $helper->getMediaUrl($product->getImage()) :
                    ($imagePh ? $placeHolderPath . $imagePh : null)),
                'manufacturer' => $manufacturer ? $manufacturer : null,
                'categories' => [],
                'storeViewId' => $product->getStoreId(),
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


            if ($temp['image']) {
                $imageInfo = getimagesize($temp['image']);
                if (is_array($imageInfo)) {
                    $temp['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
                }
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

                    if (!empty($rewrite)) {
                        // remove .html suffix if it exists
                        $parts = explode('.', $rewrite->getRequestPath());
                        $categoriesRel[$categoryId] = $parts[0];
                    } else {
                        $categoriesRel[$categoryId] = '';
                    }
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

    /**
     * Returns list of manufacturers that are visible on frontend
     *
     * @return mixed
     */
    public function getVendors()
    {

        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('offset');

        $eavConfig = $this->om->create('\Magento\Eav\Model\Config');
        $attribute = $eavConfig->getAttribute('catalog_product', 'manufacturer');
        $vendors = $attribute->getSource()->getAllOptions();

        $result = [];
        $i = 0;
        foreach ($vendors as $key => $option) {
            if ($i >= $offset) {
                if (!empty($option['value'])) {
                    $result[$option['value']] = $option['label'];
                }
                if (count($result) == $limit) {
                    break;
                }
            }
            $i++;
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
        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $resizedImage = $this->productImageHelper->create()->init($productModel, 'product_small_image')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(25, 25)
            ->save()
            ->getUrl();

        $this->appEmulation->stopEnvironmentEmulation();

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
}
