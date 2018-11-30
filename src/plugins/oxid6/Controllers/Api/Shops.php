<?php

namespace Yoochoose\Oxid\Controllers\Api;

use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Application\Model\ShopList;
use OxidEsales\Eshop\Core\Language;

/**
 * Class Shops
 * @package Yoochoose\Oxid\Controllers\Api
 */
class Shops extends BaseApi
{
    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        parent::init();

        try {
            $storeViews = $this->getStoreViews();
            $this->sendResponse($storeViews);
        } catch (\Exception $exc) {
            $this->sendResponse([], $exc->getMessage(), 400);
        }
    }

    protected function getStoreViews()
    {
        $result = [];
        $store = new ShopList();
        $store->getAll();

        $oLangObj = new Language();
        $allLanguages = [];
        foreach ($oLangObj->getLanguageArray() as $language) {
            $allLanguages[$language->oxid] = [
                'id' => $language->id,
                'name' => $language->name,
                'active' => $language->active,
            ];
        }

        /** @var Shop $val */
        foreach ($store as $val) {
            $shopId = $val->getShopId();
            $shopLanguageIds = $oLangObj->getLanguageIds($shopId);
            $shopLanguages = [];
            foreach ($shopLanguageIds as $langId) {
                $shopLanguages[$langId] = $allLanguages[$langId];
            }

            $result[] = [
                'id' => $shopId,
                'name' => $val->oxshops__oxname->value,
                'languages' => $shopLanguages,
            ];
        }

        return $result;
    }
}
