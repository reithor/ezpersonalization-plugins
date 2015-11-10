<?php

class Yoochoosecategories extends Yoochooseapi
{

    public function init()
    {
        parent::init();

        try {
            $articles = $this->getCategories();
            $this->sendResponse($articles);
        } catch (Exception $exc) {
            $this->sendResponse(array(), $exc->getMessage(), 400);
        }
    }

    protected function getCategories()
    {
        $categories = array();
        $sql = 'SELECT OXID FROM oxcategories WHERE OXACTIVE=1' . $this->getLimitSQL() ;
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            /* @var oxCategory $oCategory */
            $oCategory = oxNew('oxcategory');
            $oCategory->load($id);
            $parent = $oCategory->getParentCategory();
            $categories[] = array(
                'id' => $id,
                'url' => $oCategory->getLink(),
                'name' => $oCategory->getTitle(),
                'level' => $this->getCategoryLevel($oCategory),
                'parentId' => $parent ? $parent->getId() : null,
                'path' => $this->getCategoryList($oCategory),
            );
        }

        return $categories;
    }

    /**
     * Returns category list
     * @param oxCategory $category
     * @return mixed
     */
    protected function getCategoryList($category)
    {
        $lang = oxNew('oxlang');
        $temp = str_replace($this->getConfig()->getShopMainUrl(), '', $category->getLink());
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

}
