<?php

class Yoochoosearticles extends Yoochooseapi
{

    private $loadedCategories = array();
    private $shopUrl;
    private $languageArray;

    public function init()
    {
        parent::init();

        try {
            $articles = $this->getArticles();
            $this->sendResponse($articles);
        } catch (Exception $exc) {
            $this->sendResponse(array(), $exc->getMessage(), 400);
        }
    }

    protected function getArticles()
    {
        $this->shopUrl = $this->getConfig()->getShopMainUrl();
        $lang = oxNew('oxlang');
        $this->languageArray = $lang->getLanguageArray();
        $abbr = $lang->getLanguageAbbr();
        $shopId = $this->getShopId();
        $sql = "SELECT * FROM oxv_oxarticles_$abbr WHERE OXPARENTID='' AND OXSHOPID='$shopId' " . $this->getLimitSQL();

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
                    'description' => $oArticle->getLongDesc(),
                    'price' => $oArticle->getBasePrice(),
                    'url' => $oArticle->getLink(),
                    'image' => $coverPicture,
                    'image_size' => $imageSize,
                    'icon_image' => !empty($gallery['Icons']) ? $gallery['Icons'][1] : $coverPicture,
                    'manufacturer' => $manufacturerLoaded ? $oxManufacturer->getTitle() : null,
                    'categories' => $this->getCategoryList($categoryIds),
                    'tags' => $this->getTags($id),
                );
            }
        }

        return $articles;
    }

    protected function getCategoryList($catIds)
    {
        $categories = array();
        foreach ($catIds as $catId) {
            if (!array_key_exists($catId, $this->loadedCategories)) {
                /* @var $category oxCategory */
                $category = oxNew('oxcategory');
                $category->load($catId);
                $temp = str_replace($this->shopUrl, "", $category->getLink());
                foreach ($this->languageArray as $val) {
                    $temp = str_replace($val->abbr . '/', '', $temp);
                }

                $this->loadedCategories[$catId] = $temp;
            }

            $categories[] = $this->loadedCategories[$catId];
        }

        return $categories;
    }

    protected function getTags($id)
    {
        $oArticleTagList = oxNew('oxarticletaglist');
        $oArticleTagList->load($id);
        return $oArticleTagList->get()->formString();
    }

}
