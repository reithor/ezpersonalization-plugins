<?php

namespace Yoochoose\Oxid\Controllers\Api;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Language;

/**
 * Class Categories
 * @package Yoochoose\Oxid\Controllers\Api
 */
class Categories extends BaseApi
{
    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        parent::init();

        try {
            $articles = $this->getCategories();
            $this->sendResponse($articles);
        } catch (\Exception $exc) {
            $this->sendResponse([], $exc->getMessage(), 400);
        }
    }

    /**
     * @return array
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    protected function getCategories()
    {
        $categories = [];
        $shopId = $this->getShopId();
        $sql = "SELECT OXID FROM oxcategories WHERE OXACTIVE=1 AND OXSHOPID='$shopId' " . $this->getLimitSQL();
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            $oCategory = new Category();
            $oCategory->load($id);
            $parent = $oCategory->getParentCategory();
            $categories[] = [
                'id' => $id,
                'url' => $oCategory->getLink(),
                'name' => $oCategory->getTitle(),
                'level' => $this->getCategoryLevel($oCategory),
                'parentId' => $parent ? $parent->getId() : null,
                'path' => $this->getCategoryList($oCategory),
            ];
        }

        return $categories;
    }

    /**
     * Returns category list
     *
     * @param Category $category
     *
     * @return mixed
     */
    protected function getCategoryList(Category $category)
    {
        $lang = new Language();
        $temp = str_replace($this->getConfig()->getShopMainUrl(), '', $category->getLink());
        foreach ($lang->getLanguageArray() as $val) {
            $temp = str_replace($val->abbr . '/', '', $temp);
        }

        return $temp;
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
}
