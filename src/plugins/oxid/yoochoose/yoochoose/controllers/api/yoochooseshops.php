<?php

class Yoochooseshops extends Yoochooseapi
{

    public function init()
    {
        parent::init();

        try {
            $storeViews = $this->getStoreViews();
            $this->sendResponse($storeViews);
        } catch (Exception $exc) {
            $this->sendResponse(array(), $exc->getMessage(), 400);
        }
    }

    protected function getStoreViews()
    {
        $result = array();
        /** @var oxShoplist $store */
        $store = oxNew('oxshoplist');
        $store->getAll();

        /** @var oxLang $oLangObj */
        $oLangObj = oxNew('oxlang');
        $allLanguages = array();
        foreach ($oLangObj->getLanguageArray() as $language) {
            $allLanguages[$language->oxid] = array(
                'id' => $language->id,
                'name' => $language->name,
                'active' => $language->active,
            );
        }

        foreach ($store as $val) {
            $shopId = $val->getShopId();
            $shopLanguageIds = $oLangObj->getLanguageIds($shopId);
            $shopLanguages = array();
            foreach ($shopLanguageIds as $langId) {
                $shopLanguages[$langId] = $allLanguages[$langId];
            }

            $result[] = array(
                'id' => $shopId,
                'name' => $val->oxshops__oxname->value,
                'languages' => $shopLanguages,
            );
        }

        return $result;
    }

}