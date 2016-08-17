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

        /** @var oxUtilsView $utilsView */
        $utilsView = oxRegistry::get('oxUtilsView');
        $smarty = $utilsView->getSmarty();

        $products = $conf->getRequestParameter('products');
        foreach (explode(',', $products) as $id) {
            if ($oArticle->load($id)) {
                $result[] = array(
                    'id' => $id,
                    'title' => $oArticle->oxarticles__oxtitle->value,
                    'image' => $oArticle->getPictureUrl(),
                    'link' => $oArticle->getLink(),
                    'price' => smarty_function_oxprice(
                        array('price' => $oArticle->getPrice(), 'currency' => $conf->getActShopCurrencyObject()),
                        $smarty
                    ),
                );
            }

            $oArticle = oxNew('oxarticle');
        }

        header('Content-Type: application/json');
        exit(json_encode($result));
    }

}
