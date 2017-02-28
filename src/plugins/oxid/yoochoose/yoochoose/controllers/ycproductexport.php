<?php

require_once dirname(__FILE__) . '/../../../../core/smarty/plugins/function.oxprice.php';

class Ycproductexport extends oxUBase
{

    public function init()
    {
        /** @var oxConfig $conf */
        $conf = $this->getConfig();
        $result = array();
        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxarticle');
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

        foreach (explode(',', $products) as $id) {
            if ($oArticle->load($id)) {
                $variants = $oArticle->getVariantIds();
                $result[] = array(
                    'id' => $id,
                    'title' => $oArticle->oxarticles__oxtitle->value,
                    'image' => $oArticle->getPictureUrl(),
                    'link' => $oArticle->getLink(),
                    'action' => $viewConf->getSelfActionLink(),
                    'cnid' => $oArticle->getCategoryIds(),
                    'anid' => $oArticle->oxarticles__oxnid->value ? $oArticle->oxarticles__oxnid->value :
                        $oArticle->oxarticles__oxid->value,
                    'am' => 1,
                    'stoken' => $viewConf->getHiddenSid(),
                    'fnc' => $articleBox->getToBasketFunction() ? $articleBox->getToBasketFunction() : "tobasket",
                    'pgNr' => $oxBase->getActPage(),
                    'owishid' => $smarty->_tpl_vars['owishid'] ? $smarty->_tpl_vars['owishid'] : null,
                    'newPrice' => smarty_function_oxprice(
                        array('price' => $oArticle->getPrice(), 'currency' => $conf->getActShopCurrencyObject()),
                        $smarty
                    ),
                    'oldPrice' => smarty_function_oxprice(
                        array('price' => $oArticle->getTPrice(), 'currency' => $conf->getActShopCurrencyObject()),
                        $smarty
                    ),
                    'showCartButton' => empty($variants) ? true : false,
                );
            }

            $oArticle = oxNew('oxarticle');
        }

        header('Content-Type: application/json');
        exit(json_encode($result));
    }
}
