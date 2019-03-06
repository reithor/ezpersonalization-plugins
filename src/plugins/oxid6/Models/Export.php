<?php

namespace Yoochoose\Oxid\Models;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;

/**
 * Class Export
 * @package Yoochoose\Oxid\Models
 */
class Export extends Base
{
    /**
     * @var array
     */
    private $loadedCategories = [];

    /**
     * Returns list of categories that are visible on frontend
     *
     * @param string $shopId
     * @param int $offset
     * @param int $limit
     * @param string $language
     *
     * @return array
     * @throws DatabaseException
     */
    public function getCategories($shopId, $offset, $limit, $language)
    {
        $conf = $this->getConfig();
        $categories = [];
        $langId = $this->getLanguageId($language);
        $tableViewNameGenerator = new TableViewNameGenerator();
        $sCatTable = $tableViewNameGenerator->getViewName('oxcategories', $language, $shopId);
        $conf->setShopId($shopId);

        $sql = "SELECT OXID, OXTITLE FROM $sCatTable WHERE OXACTIVE=1 " . $this->getLimitSQL($offset, $limit);
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            $category = new Category;
            $category->load($id);
            $parent = $category->getParentCategory();
            $categories[] = [
                'id' => $id,
                'url' => strtok($category->getLink($langId), '?'),
                'name' => $val['OXTITLE'],
                'level' => $this->getCategoryLevel($category),
                'parentId' => $parent ? $parent->getId() : null,
                'path' => $this->getCategoryPath($category, $langId),
                'shopId' => $this->getConfig()->getShopId(),
            ];
        }

        return $categories;
    }

    /**
     * Returns list of products that are visible on frontend
     *
     * @param string $shopId
     * @param int $offset
     * @param int $limit
     * @param int $language
     *
     * @return array
     * @throws DatabaseException
     */
    public function getProducts($shopId, $offset, $limit, $language)
    {
        $conf = $this->getConfig();
        $langId = $this->getLanguageId($language);
        $tableViewNameGenerator = new TableViewNameGenerator();
        $sArtTable = $tableViewNameGenerator->getViewName('oxarticles', $language, $shopId);
        $conf->setShopId($shopId);

        $sql = "SELECT * FROM $sArtTable AS art 
        LEFT JOIN oxv_oxartextends_$language artExt ON artExt.OXID = art.OXID 
        WHERE OXPARENTID='' AND OXACTIVE='1' " . $this->getLimitSQL($offset, $limit);

        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql);
        $articles = [];

        foreach ($result as $val) {
            $id = $val['OXID'];
            $oArticle = new Article();
            $oArticle->load($id);

            $oxManufacturer = new Manufacturer();
            $manufacturerLoaded = $oxManufacturer->load($oArticle->getManufacturerId());
            $categoryIds = $oArticle->getCategoryIds();
            $gallery = $oArticle->getPictureGallery();
            $coverPicture = $gallery['ActPic'];
            $imageSize = '';
            $oArticle->enablePriceLoad();

            if ($oArticle->isVisible()) {
                $articles[] = [
                    'id' => $val['OXID'],
                    'name' => $val['OXTITLE'],
                    'description' => $val['OXLONGDESC'],
                    'price' => $oArticle->getVarMinPrice()->getBruttoPrice(),
                    'url' => strtok($oArticle->getLink($langId), '?'),
                    'image' => $coverPicture,
                    'image_size' => $imageSize,
                    'icon_image' => !empty($gallery['Icons']) ? $gallery['Icons'][1] : $coverPicture,
                    'manufacturer' => $manufacturerLoaded ? $this->getManufacturer($val['OXMANUFACTURERID'],
                        $language) : null,
                    'categories' => $this->getCategoryList($categoryIds, $langId),
                    'tags' => $this->formatSearchKeys($oArticle->oxarticles__oxsearchkeys->value),
                    'shopId' => $this->getConfig()->getShopId(),
                ];
            }

            $oArticle->resetLoadedParents();
        }

        return $articles;
    }

    /**
     * Returns list of manufacturers that are visible on frontend
     *
     * @param string $shopId
     * @param int $offset
     * @param int $limit
     * @param string $language
     *
     * @return array
     * @throws DatabaseException
     */
    public function getVendors($shopId, $offset, $limit, $language)
    {
        $conf = $this->getConfig();
        $vendors = [];
        $conf->setShopId($shopId);
        $tableViewNameGenerator = new TableViewNameGenerator();
        $sVenTable = $tableViewNameGenerator->getViewName('oxmanufacturers', $language, $shopId);

        $sql = "SELECT * FROM $sVenTable " . $this->getLimitSQL($offset, $limit);
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            $vendor = new Manufacturer();
            $vendor->load($id);
            if ($vendor->exists()) {
                $vendors[] = [
                    'id' => $id,
                    'name' => $val['OXTITLE'],
                ];
            }
        }

        return $vendors;
    }

    /**
     * Returns category list
     *
     * @param array $catIds
     * @param int   $langId
     *
     * @return array
     */
    protected function getCategoryList(array $catIds, $langId)
    {
        $categories = [];
        foreach ($catIds as $catId) {
            if (!array_key_exists($catId, $this->loadedCategories)) {
                $this->buildCategoryPath($catId, $langId);
            }

            $categories[] = $this->loadedCategories[$catId];
        }

        return $categories;
    }

    /**
     * Returns full category path in specified language
     *
     * @param string $categoryId
     * @param int    $langId
     *
     * @return string
     */
    private function buildCategoryPath($categoryId, $langId)
    {
        if (array_key_exists($categoryId, $this->loadedCategories)) {
            return $this->loadedCategories[$categoryId];
        }

        $category = new Category();
        $category->loadInLang($langId, $categoryId);
        $categoryPath = $category->getTitle();
        $parentId = $category->oxcategories__oxparentid->value;
        if ($parentId !== 'oxrootid') {
            $categoryPath = $this->buildCategoryPath($parentId, $langId) . '/' . $categoryPath;
        }

        $categoryPath = htmlspecialchars_decode($categoryPath);
        $this->loadedCategories[$categoryId] = $categoryPath;

        return $categoryPath;
    }

    /**
     * Returns category path
     *
     * @param Category $category
     * @param int      $langId
     *
     * @return string
     */
    protected function getCategoryPath(Category $category, $langId)
    {
        $lang = new Language();
        $temp = str_replace($this->getConfig()->getShopMainUrl(), '', $category->getLink($langId));
        foreach ($lang->getLanguageArray() as $val) {
            $temp = str_replace($val->abbr . '/', '', $temp);
        }

        return strtok($temp, '?');
    }

    /**
     * Returns category level
     *
     * @param Category $category
     *
     * @return int
     */
    protected function getCategoryLevel(Category $category)
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
     * @param int $offset
     * @param int $limit
     *
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
     *
     * @return string
     * @throws DatabaseException
     */
    private function getManufacturer($manId, $lang)
    {
        $sql = " SELECT * FROM oxv_oxmanufacturers_$lang AS man WHERE man.OXID='$manId' AND man.OXACTIVE=1 ";
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql);

        return isset($result[0]) ? $result[0]['OXTITLE'] : null;
    }

    /**
     * Formats string of search keys into array
     *
     * @param string $value
     *
     * @return array
     */
    private function formatSearchKeys($value)
    {
        $result = explode(',', $value);
        foreach ($result as &$item) {
            $item = trim($item);
        }

        return $result;
    }
}
