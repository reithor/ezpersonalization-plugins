<?php

class Yoochoosearticles extends Yoochooseapi
{

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
        $lang = oxnew('oxlang');
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
                    'categories' => $this->getCategoryList($categoryIds[0]),
                    'tags' => $this->getTags($id),
                );
            }
        }

        return $articles;
    }

    protected function getCategoryList($catId)
    {
        /* @var $category oxCategory */
        $category = oxNew('oxcategory');
        $category->load($catId);
        $lang = oxNew('oxlang');
        $temp = str_replace($this->getConfig()->getShopMainUrl(), "", $category->getLink());
        foreach ($lang->getLanguageArray() as $val) {
            $temp = str_replace($val->abbr . '/', '', $temp);
        }

        return $temp;
    }

    protected function getTags($id)
    {
        $oArticleTagList = oxNew('oxarticletaglist');
        $oArticleTagList->load($id);
        return $oArticleTagList->get()->formString();
    }

}
