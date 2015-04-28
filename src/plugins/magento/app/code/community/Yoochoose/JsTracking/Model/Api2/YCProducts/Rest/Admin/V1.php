<?php

class Yoochoose_JsTracking_Model_Api2_YCProducts_Rest_Admin_V1 extends Yoochoose_JsTracking_Model_Api2_YCProducts
{

    /**
     * Retrieve list of products.
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $categoriesRel = array();
        $productsCategories = array();
        $limit = $this->getRequest()->getParam('limit');
        $offset = $this->getRequest()->getParam('offset');
        $storeId = $this->_getStore()->getId();
        $helper = Mage::getModel('catalog/product_media_config');
        $storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setStoreId($storeId);
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(array('e.entity_id'));
        $collection->addAttributeToSelect(array('name', 'description', 'price', 'url_path', 'image'));
        if ($limit && is_numeric($limit)) {
            $offset = $offset ? $offset : 0;
            $collection->getSelect()->limit($limit, $offset);
        }

        foreach ($collection as $product) {
            $productsCategories[$product->getId()] = $product->getCategoryCollection();
        }

        $products = $collection->load()->toArray();

        foreach ($products as &$product) {
            $product['url'] = $storeUrl . $product['url_path'];
            unset($product['url_path']);
            
            //image
            $product['image'] = $helper->getMediaUrl($product['image']);
            $imageInfo = getimagesize($product['image']);
            if (is_array($imageInfo)) {
                $product['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
            }

            //Categories
            $product['categories'] = array();
            foreach ($productsCategories[$product['entity_id']] as $category) {
                $path = $category->getPath();
                $categoryIds = array_slice(explode('/', $path), 2);

                $breadcrumb = array();
                foreach ($categoryIds as $catId) {
                    if (!isset($categoriesRel[$catId])) {
                        $cat = Mage::getResourceModel('catalog/category_collection')
                                ->setStoreId($storeId)
                                ->addAttributeToSelect('name')
                                ->addFieldToFilter('entity_id', $catId)
                                ->getFirstItem();
                        
                        $categoriesRel[$catId] = $cat->getName();
                    }

                    $breadcrumb[] = $categoriesRel[$catId];
                }

                $product['categories'][] = implode('/', $breadcrumb);
            }

            //Tags
            $tags = Mage::getModel('tag/tag')->getCollection()->joinRel()->addProductFilter($product['entity_id']);
            $tags->addStoreFilter($storeId);
            $product['tags'] = array();
            foreach ($tags as $tag) {
                $product['tags'][] = $tag->getName();
            }
        }

        return array('products' => array($products));
    }

}
