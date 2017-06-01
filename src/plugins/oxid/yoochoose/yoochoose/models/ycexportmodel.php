<?php

class Ycexportmodel extends oxUBase
{


    private $loadedCategories = array();
    private $shopUrl;


    /**
     * Returns list of categories that are visible on frontend
     *
     * @param string $shopId
     * @param integer $offset
     * @param integer $limit
     * @param string $language
     * @return array
     */
    public function getCategories($shopId, $offset, $limit, $language)
    {
        $conf = $this->getConfig();
        $categories = array();
        $langId = $this->getLanguageId($language);
        $sCatTable = getViewName('oxcategories', $language, $shopId);
        $conf->setShopId($shopId);

        $sql = "SELECT OXID, OXTITLE FROM $sCatTable 
        WHERE OXACTIVE=1 " . $this->getLimitSQL($offset, $limit);
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            /* @var oxCategory $oCategory */
            $oCategory = oxNew('oxcategory');
            $oCategory->load($id);
            $parent = $oCategory->getParentCategory();
            $categories[] = array(
                'id' => $id,
                'url' => $oCategory->getLink($langId),
                'name' => $val['OXTITLE'],
                'level' => $this->getCategoryLevel($oCategory),
                'parentId' => $parent ? $parent->getId() : null,
                'path' => $this->getCategoryPath($oCategory, $langId),
                'shopId' => $this->getConfig()->getShopId(),
            );
        }

        return $categories;
    }

    /**
     * Returns list of products that are visible on frontend
     *
     * @param $shopId
     * @param $offset
     * @param $limit
     * @param $language
     * @return array
     */
    public function getProducts($shopId, $offset, $limit, $language)
    {
        $conf = $this->getConfig();
        $langId = $this->getLanguageId($language);
        $this->shopUrl = $this->getConfig()->getShopMainUrl();
        $sArtTable = getViewName('oxarticles', $language, $shopId);
        $conf->setShopId($shopId);

        $sql = "SELECT * FROM $sArtTable AS art 
        LEFT JOIN oxv_oxartextends_$language artExt ON artExt.OXID = art.OXID 
        WHERE OXPARENTID='' AND OXACTIVE='1' " . $this->getLimitSQL($offset, $limit);

        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sql);
        $articles = array();

        foreach ($result as $val) {
            $id = $val['OXID'];
            /** @var oxArticle $oArticle */
            $oArticle = oxNew('oxarticle');
            $oArticle->load($id);

            /* @var oxManufacturer $oxManufacturer */
            $oxManufacturer = oxNew('oxmanufacturer');
            $manufacturerLoaded = $oxManufacturer->load($oArticle->getManufacturerId());
            $categoryIds = $oArticle->getCategoryIds();
            $gallery = $oArticle->getPictureGallery();
            $coverPicture = $gallery['ActPic'];
            $imageSize = '';
//            $imageInfo = getimagesize($coverPicture);
//            if (is_array($imageInfo)) {
//                $imageSize = $imageInfo[0] . 'x' . $imageInfo[1];
//            }

            if ($oArticle->isVisible()) {
                $articles[] = array(
                    'id' => $val['OXID'],
                    'name' => $val['OXTITLE'],
                    'description' => $val['OXLONGDESC'],
                    'price' => $oArticle->getBasePrice(),
                    'url' => $oArticle->getLink($langId),
                    'image' => $coverPicture,
                    'image_size' => $imageSize,
                    'icon_image' => !empty($gallery['Icons']) ? $gallery['Icons'][1] : $coverPicture,
                    'manufacturer' => $manufacturerLoaded ? $this->getManufacturer($val['OXMANUFACTURERID'],
                        $language) : null,
                    'categories' => $this->getCategoryList($categoryIds, $langId),
                    'tags' => $this->getTags($id),
                    'shopId' => $this->getConfig()->getShopId(),
                );
            }
        }

        return $articles;
    }

    /**
     * Returns list of manufacturers that are visible on frontend
     *
     * @param string $shopId
     * @param integer $offset
     * @param integer $limit
     * @param string $language
     * @return array
     */
    public function getVendors($shopId, $offset, $limit, $language)
    {
        $conf = $this->getConfig();
        $vendors = array();
        $conf->setShopId($shopId);
        $sVenTable = getViewName('oxmanufacturers', $language, $shopId);

        $sql = "SELECT * FROM $sVenTable " . $this->getLimitSQL($offset, $limit);
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            /** @var oxManufacturer $vendor */
            $vendor = oxNew('oxmanufacturer');
            $vendor->load($id);
            if ($vendor->exists()) {
                $vendors[] = array(
                    'id' => $id,
                    'name' => $val['OXTITLE'],
                );
            }
        }

        return $vendors;
    }

    /**
     * Returns category list
     *
     * @param array $catIds
     * @param integer $langId
     * @return array
     */
    protected function getCategoryList($catIds, $langId)
    {
        $categories = array();
        foreach ($catIds as $catId) {
            if (!array_key_exists($catId, $this->loadedCategories)) {
                $this->buildCategoryPath($catId, $langId);
            }

            $categories[] = $this->loadedCategories[$catId];
        }

        return $categories;
    }

    /**
     * @param $categoryId
     * @param $langId
     * @return string
     */
    private function buildCategoryPath($categoryId, $langId)
    {
        if (array_key_exists($categoryId, $this->loadedCategories)) {
            return $this->loadedCategories[$categoryId];
        }

        /* @var $category oxCategory */
        $category = oxNew('oxcategory');
        $category->loadInLang($langId, $categoryId);
        $categoryPath = $category->getTitle();
        $parentId = $category->oxcategories__oxparentid->value;
        if ($parentId !== 'oxrootid') {
            $categoryPath = $this->buildCategoryPath($parentId, $langId) . '/' . $categoryPath;
        }

        $this->loadedCategories[$categoryId] = $categoryPath;

        return $categoryPath;
    }

    /**
     * Returns tags
     *
     * @param integer $id
     * @return mixed
     */
    protected function getTags($id)
    {
        $oArticleTagList = oxNew('oxarticletaglist');
        $oArticleTagList->load($id);
        $tagsString = $oArticleTagList->get()->formString();

        return $tagsString ? explode(',', $tagsString) : array();
    }

    /**
     * Returns category path
     *
     * @param oxCategory $category
     * @param integer $langId
     * @return mixed
     */
    protected function getCategoryPath($category, $langId)
    {
        $lang = oxNew('oxlang');
        $temp = str_replace($this->getConfig()->getShopMainUrl(), '', $category->getLink($langId));
        foreach ($lang->getLanguageArray() as $val) {
            $temp = str_replace($val->abbr . '/', '', $temp);
        }

        return $temp;
    }

    /**
     * Returns category level
     * @param oxCategory $category
     * @return int
     */
    protected function getCategoryLevel($category)
    {
        $level = 0;
        while (!$category->isTopCategory()) {
            $level++;
            $category = $category->getParentCategory();
        }

        return $level;
    }

    /**
     * Returns sql limit string or empty string if limit parameters are not set
     *
     * @param integer $offset
     * @param integer $limit
     * @return string
     */
    protected function getLimitSQL($offset, $limit)
    {
        if ($limit && is_numeric($limit)) {
            $offset = $offset && is_numeric($offset) ? $offset : 0;

            return " LIMIT {$offset}, {$limit} ";
        }

        return '';
    }


    /**
     * Returns language id based on language tag
     *
     * @param string $lang
     * @return integer
     */
    private function getLanguageId($lang)
    {
        $langId = null;
        $conf = $this->getConfig();
        $langIds = $conf->getShopConfVar('aLanguageParams');
        foreach ($langIds as $key => $value) {
            if ($key == $lang) {
                $langId = $value['baseId'];
            }
        }

        return $langId;
    }

    /**
     * Returns manufacturer name based on language tag and manufacturer id
     *
     * @param string $manId
     * @param string $lang
     * @return mixed
     */
    private function getManufacturer($manId, $lang)
    {
        $sql = " SELECT * FROM oxv_oxmanufacturers_$lang AS man WHERE man.OXID='$manId' AND man.OXACTIVE=1 ";
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sql);

        return $title = $result[0]['OXTITLE'];
    }

}