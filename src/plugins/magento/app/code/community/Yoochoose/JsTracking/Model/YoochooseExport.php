<?php



class  Yoochoose_JsTracking_Model_YoochooseExport extends Mage_Core_Model_Config_Data
{
    
    /**
     * Returns list of subscribers
     *
     * @return mixed
     */
    public function getSubscribers()
    {
        $limit = $this->getRequest()->getParam('limit');
        $offset = $this->getRequest()->getParam('offset');
        $storeId = $this->getRequest()->getParam('storeId');

    }

    /**
     * Returns list of categories that are visible on frontend
     *
     * @return mixed
     */
    public function getCategories()
    {

        $limit = Mage::app()->getRequest()->getParam('limit');
        $offset = Mage::app()->getRequest()->getParam('offset');
        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        /* @var $categoryCollection Mage_Catalog_Model_Resource_Category_Collection */
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect(array('url_path', 'name', 'level'));

        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $categoryCollection->getSelect()->limit($limit, $offset);
        }

        foreach ($categoryCollection as $category) {
            $url = $category->getUrlPath();
            $path = explode('.', $url);
            $result[] = array(
                'id' => $category->getId(),
                'path' => $path[0],
                'url' => $storeUrl . $url,
                'name' => $category->getName(),
                'level' => $category->getLevel(),
                'parentId' => $category->getParentId(),
                'storeId' => $category->getStore()->getId()
            );
        }

        if (empty($result)) {
            return $result;
        }

        return array('categories' => array($result));

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
        $helper = Mage::getModel('catalog/product_media_config');
        $imagePlaceholder = Mage::getStoreConfig("catalog/placeholder/image_placeholder");
        $placeholderFullPath = $helper->getBaseMediaUrl(). '/placeholder/' . $imagePlaceholder;
        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
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

        foreach ($collection as $product) {
            $id = $product->getId();
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
                'storeIds' => $product->getStoreIds()
            );
            $imageInfo = getimagesize($temp['image']);
            if (is_array($imageInfo)) {
                $temp['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
            }

            //Categories
            foreach ($product->getCategoryCollection() as $category) {
                $catId = end(explode('/', $category->getPath()));
                if (!isset($categoriesRel[$catId])) {
                    $cat = Mage::getResourceModel('catalog/category_collection')
                        ->addAttributeToSelect('url_path')
                        ->addFieldToFilter('entity_id', $catId)
                        ->getFirstItem();

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

        if (empty($products)) {
            return $products;
        }

        return array('products' => array($products));
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

        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode('catalog_product', 'manufacturer');

        $vendorCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getData('attribute_id'))
            ->setStoreFilter(0, false);


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

        if (empty($vendors)) {
            return $vendors;
        }

        return array('vendors' => array($vendors));
    }

}
