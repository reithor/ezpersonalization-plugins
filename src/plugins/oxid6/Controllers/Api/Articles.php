<?php

namespace Yoochoose\Oxid\Controllers\Api;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Language;
use Yoochoose\Oxid\Models\Article;

/**
 * Class Articles
 * @package Yoochoose\Oxid\Controllers\Api
 */
class Articles extends BaseApi
{
    /**
     * @var array
     */
    private $loadedCategories = [];
    /**
     * @var string
     */
    private $shopUrl;
    /**
     * @var array
     */
    private $languageArray;

    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        parent::init();

        try {
            $articles = $this->getArticles();
            $this->sendResponse($articles);
        } catch (\Exception $exc) {
            $this->sendResponse([], $exc->getMessage(), 400);
        }
    }

    /**
     * Returns list of articles
     *
     * @return array
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    protected function getArticles()
    {
        $this->shopUrl = $this->getConfig()->getShopMainUrl();
        $lang = new Language();
        $this->languageArray = $lang->getLanguageArray();
        $abbr = $lang->getLanguageAbbr();
        $shopId = $this->getShopId();
        $sql = "SELECT * FROM oxv_oxarticles_$abbr WHERE OXPARENTID='' AND OXSHOPID='$shopId' " . $this->getLimitSQL();

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
            $imageInfo = getimagesize($coverPicture);
            if (is_array($imageInfo)) {
                $imageSize = $imageInfo[0] . 'x' . $imageInfo[1];
            }

            if ($oArticle->isVisible()) {
                $articles[] = [
                    'id' => $val['OXID'],
                    'name' => $val['OXTITLE'],
                    'description' => $oArticle->getLongDesc(),
                    'price' => $oArticle->getBasePrice(),
                    'url' => $oArticle->getLink(),
                    'image' => $coverPicture,
                    'image_size' => $imageSize,
                    'icon_image' => !empty($gallery['Icons']) ? $gallery['Icons'][1] : $coverPicture,
                    'manufacturer' => $manufacturerLoaded ? $oxManufacturer->getTitle() : null,
                    'categories' => $this->getCategoryList($categoryIds),
                    'tags' => $this->formatSearchKeys($oArticle->oxarticles__oxsearchkeys->value),
                ];
            }
        }

        return $articles;
    }

    /**
     * Returns array of category paths
     *
     * @param array $catIds
     *
     * @return array
     */
    protected function getCategoryList(array $catIds)
    {
        $categories = [];
        foreach ($catIds as $catId) {
            if (!array_key_exists($catId, $this->loadedCategories)) {
                $this->buildCategoryPath($catId);
            }

            $categories[] = $this->loadedCategories[$catId];
        }

        return $categories;
    }

    /**
     * Returns category path for given category id
     *
     * @param string $categoryId
     *
     * @return string
     */
    private function buildCategoryPath($categoryId)
    {
        if (array_key_exists($categoryId, $this->loadedCategories)) {
            return $this->loadedCategories[$categoryId];
        }

        $category = new Category();
        $category->load($categoryId);
        $categoryPath = $category->getTitle();
        $parentId = $category->oxcategories__oxparentid->value;
        if ($parentId !== 'oxrootid') {
            $categoryPath = $this->buildCategoryPath($parentId) . '/' . $categoryPath;
        }

        $this->loadedCategories[$categoryId] = $categoryPath;

        return $categoryPath;
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
