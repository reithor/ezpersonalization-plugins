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
        $categories = array();
        $langId = $this->getLanguageId($language);
        $sql = "SELECT OXID, OXTITLE FROM oxv_oxcategories_$language 
        WHERE OXACTIVE=1 AND OXSHOPID='$shopId' " . $this->getLimitSQL($offset, $limit);
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
                'storeId' => $this->getConfig()->getShopId(),
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
        $langId = $this->getLanguageId($language);
        $this->shopUrl = $this->getConfig()->getShopMainUrl();

        $sql = "SELECT * FROM oxv_oxarticles_$language AS art 
        LEFT JOIN oxv_oxartextends_$language artExt ON artExt.OXID = art.OXID 
        WHERE OXPARENTID='' AND art.OXSHOPID='$shopId' " . $this->getLimitSQL($offset, $limit);

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
            $imageInfo = getimagesize($coverPicture);
            if (is_array($imageInfo)) {
                $imageSize = $imageInfo[0] . 'x' . $imageInfo[1];
            }

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
                    'categories' => $this->getCategoryList($categoryIds, $langId, $language),
                    'tags' => $this->getTags($id),
                    'storeId' => $this->getConfig()->getShopId(),
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
        $vendors = array();
        $sql = "SELECT * FROM oxv_oxmanufacturers_$language WHERE OXSHOPID='$shopId' " . $this->getLimitSQL($offset,
                $limit);
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
     * @param string $abbr
     * @return array
     */
    protected function getCategoryList($catIds, $langId, $abbr)
    {
        $categories = array();
        foreach ($catIds as $catId) {
            if (!array_key_exists($catId, $this->loadedCategories)) {
                /* @var $category oxCategory */
                $category = oxNew('oxcategory');
                $category->load($catId);
                $temp = str_replace($this->shopUrl, "", $category->getLink($langId));
                $temp = str_replace($abbr . '/', '', $temp);


                $this->loadedCategories[$catId] = $temp;
            }

            $categories[] = $this->loadedCategories[$catId];
        }

        return $categories;
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