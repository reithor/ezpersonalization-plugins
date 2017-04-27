<?php



class  Yoochoose_JsTracking_Model_YoochooseExport extends Mage_Core_Model_Config_Data
{

    /**
     * Returns list of categories that are visible on frontend
     *
     * @return mixed
     */
    public function getCategories()
    {
        $result = array();
        $limit = Mage::app()->getRequest()->getParam('limit');
        $offset = Mage::app()->getRequest()->getParam('offset');
        $storeId = Mage::app()->getRequest()->getParam('storeViewId');

        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $id = Mage::app()->getStore($storeId)->getRootCategoryId();


        /* @var $categoryCollection Mage_Catalog_Model_Resource_Category_Collection */
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->setStoreId($storeId)
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('path', ['like' => "$rootId/$id/%"])
            ->addAttributeToSelect(array('url_path', 'name', 'level', 'store_id'));


        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $categoryCollection->getSelect()->limit($limit, $offset);
        }

        foreach ($categoryCollection as $category) {
            $url = $category->getUrlPath();
            $path = explode('.', $url);
            $result[] = [
                'id' => $category->getId(),
                'path' => $path[0],
                'url' => $storeUrl . $url,
                'name' => $category->getName(),
                'level' => $category->getLevel(),
                'parentId' => $category->getParentId(),
            ];
        }

        $categoryCollection->clear();

        if (empty($result)) {
            http_response_code(204);
        }

        return $result;

    }

    /**
     * Returns list of products that are visible on frontend
     *
     * @return mixed
     */
    public function getProducts()
    {
        $categoriesRel = array();
        $products = array();

        $limit = Mage::app()->getRequest()->getParam('limit');
        $offset = Mage::app()->getRequest()->getParam('offset');
        $storeId = Mage::app()->getRequest()->getParam('storeViewId');

        $helper = Mage::getModel('catalog/product_media_config');
        $imagePlaceholder = Mage::getStoreConfig("catalog/placeholder/image_placeholder");
        $placeholderFullPath = $helper->getBaseMediaUrl(). '/placeholder/' . $imagePlaceholder;
        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        Mage::app()->setCurrentStore($storeId);

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addStoreFilter($storeId);
        $collection->addFieldToFilter('visibility', array(
                'neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE,
            )
        );
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(array('e.entity_id'));
        $collection->addAttributeToSelect(array('name', 'description', 'price', 'url_path', 'image', 'manufacturer', 'qty'));
        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $collection->getSelect()->limit($limit, $offset);
        }

        $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $catRootId = Mage::app()->getStore($storeId)->getRootCategoryId();
        foreach ($collection as $product) {
            $id = $product->getId();
            $product->setStoreId($storeId);
            $manufacturer = $product->getAttributeText('manufacturer');
            $temp = array(
                'entity_id' => $id,
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'url' => $storeUrl . $product->getUrlPath(),
                'image' => ($product->getImage() ? $helper->getMediaUrl($product->getImage()) :
                    ($imagePlaceholder ? $placeholderFullPath : null)),
                'manufacturer' => $manufacturer ? $manufacturer : null,
                'categories' => array(),
                'tags' => array(),
                'storeViewId' => $storeId,
            );

            $temp['icon_image'] = $this->makeSmallImage($product);

            $productModel = Mage::getModel('catalog/product')->load($id);
            $customAttributes = $productModel->getAttributes();
            foreach ($customAttributes as $customAttribute) {
                $customKey = $customAttribute->getAttributeCode();
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $customKey);
                if (!isset($temp[$customKey]) && $attribute->getIsUserDefined()) {
                    $customValue = $customAttribute->getFrontend()->getValue($productModel);
                    $temp[$customKey] = $this->getCustomAttributeValue($customValue);
                }
            }

            $imageInfo = getimagesize($temp['image']);
            if (is_array($imageInfo)) {
                $temp['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
            }

            //Categories
            foreach ($product->getCategoryCollection() as $category) {
                $categoryPath = $category->getPath();
                if (strpos($categoryPath . '/', "$rootId/$catRootId/") !== 0) {
                    continue;
                }

                $catId = end(explode('/', $categoryPath));
                if (!isset($categoriesRel[$catId])) {
                    $cat = Mage::getResourceModel('catalog/category_collection')
                        ->addAttributeToSelect('url_path')
                        ->addFieldToFilter('entity_id', $catId)
                        ->getFirstItem()
                        ->load();

                    $url = $cat->getUrlPath();
                    $path = explode('.', $url);
                    $categoriesRel[$catId] = $path[0];
                }

                $temp['categories'][] = $categoriesRel[$catId];
            }

            //Tags
            $tags = Mage::getModel('tag/tag')->getCollection()->joinRel()->addProductFilter($temp['entity_id']);
            foreach ($tags as $tag) {
                $temp['tags'][] = $tag->getName();
            }

            $products[$id] = $temp;
        }

        $collection->clear();

        if (empty($products)) {
            http_response_code(204);
        }

        return array_values($products);
    }



    /**
     * Returns list of manufacturers that are visible on frontend
     *
     * @return mixed
     */
    public function getVendors()
    {

        $limit = Mage::app()->getRequest()->getParam('limit');
        $offset = Mage::app()->getRequest()->getParam('offset');
        $storeId = Mage::app()->getRequest()->getParam('storeViewId');

        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode('catalog_product', 'manufacturer');

        $vendorCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getData('attribute_id'))
            ->setStoreFilter($storeId, false);



        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $vendorCollection->getSelect()->limit($limit, $offset);
        }

        $vendors = array();
        foreach ($vendorCollection as $value) {
            $temp = array(
                'id' => $value->getOptionId(),
                'name' => $value->getValue()
            );
            $vendors[] = $temp;
        }

        $vendorCollection->clear();

        if (empty($vendors)) {
            http_response_code(204);
        }

        return $vendors;
    }

    public function getStoreViews()
    {
        $result = array();
        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            Mage::app()->setCurrentStore($store['store_id']);
            $lang = Mage::getStoreConfig('yoochoose/general/language', $store['store_id']);
            if (Mage::getStoreConfig('yoochoose/general/language_country', $store['store_id'])) {
                $lang = substr($lang, 0, strpos($lang, '_'));
            }

            $result[] = array(
                'id' => $store['store_id'],
                'name' => $store['name'],
                'item_type_id' => Mage::getStoreConfig('yoochoose/general/itemtypeid', $store['store_id']),
                'language' => str_replace('_', '-', $lang),
            );
        }

        return $result;
    }

    /**
     * @param $productModel
     * @return string
     */
    protected function makeSmallImage($productModel)
    {
        $resizedImage = Mage::helper('catalog/image')->init($productModel, 'image')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(100, 100);

        return (string)$resizedImage;
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
