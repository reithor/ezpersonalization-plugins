<?php

require_once dirname(__FILE__) . '/../../../../core/smarty/plugins/function.oxprice.php';

class Ycproductexport extends oxUBase
{

    public function init()
    {
        /** @var oxConfig $conf */
        $conf = $this->getConfig();
        $result = array();
        /** @var oxViewConfig $viewConf */
        $viewConf = oxNew('oxViewConfig');
        /** @var oxUBase $oxBase */
        $oxBase = oxNew('oxUBase');
        /** @var oxwArticleBox $articleBox */
        $articleBox = oxNew('oxwArticleBox');

        /** @var oxUtilsView $utilsView */
        $utilsView = oxRegistry::get('oxUtilsView');
        $smarty = $utilsView->getSmarty();
        $products = $conf->getRequestParameter('products');
        $action = $viewConf->getSelfActionLink();
        $token = $viewConf->getHiddenSid();
        $fnc = $articleBox->getToBasketFunction() ? $articleBox->getToBasketFunction() : "tobasket";
        $actPage = $oxBase->getActPage();
        $owishId = $smarty->_tpl_vars['owishid'] ? $smarty->_tpl_vars['owishid'] : null;
        $currency = $conf->getActShopCurrencyObject();

        /** @var oxArticleList $list */
        $list = oxNew('oxArticleList');
        $list->loadIds(explode(',', $products));

        /** @var oxArticle $oArticle */
        foreach ($list->getArray() as $oArticle) {
            $oArticle->enablePriceLoad();
            // if article has parent it's variation, so load parent instead
            $parentId = $oArticle->getParentId();
            if (!empty($parentId)) {
                $oArticle = oxNew('oxarticle');
                $oArticle->load($parentId);
            }

            if (!$oArticle->isVisible()) {
                continue;
            }

            $variants = $oArticle->getVariantIds();
            $result[$oArticle->getID()] = array(
                'id' => $oArticle->getID(),
                'title' => $oArticle->oxarticles__oxtitle->value,
                'image' => $oArticle->getPictureUrl(),
                'link' => $oArticle->getLink(),
                'action' => $action,
                'cnid' => $oArticle->getCategoryIds(),
                'anid' => $oArticle->oxarticles__oxnid->value ? $oArticle->oxarticles__oxnid->value :
                    $oArticle->oxarticles__oxid->value,
                'am' => 1,
                'stoken' => $token,
                'fnc' => $fnc,
                'pgNr' => $actPage,
                'owishid' => $owishId,
                'newPrice' => smarty_function_oxprice(
                    array('price' => $oArticle->getVarMinPrice()->getBruttoPrice(), 'currency' => $currency),
                    $smarty
                ),
                'oldPrice' => smarty_function_oxprice(
                    array('price' => $oArticle->getTPrice(), 'currency' => $currency),
                    $smarty
                ),
                'showCartButton' => empty($variants) ? true : false,
            );
        }

        header('Content-Type: application/json');
        exit(json_encode(array_values($result)));
    }
}
