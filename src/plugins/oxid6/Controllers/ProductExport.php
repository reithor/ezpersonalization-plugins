<?php

namespace Yoochoose\Oxid\Controllers;

use OxidEsales\Eshop\Application\Component\Widget\ArticleBox;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Core\Registry;
use Yoochoose\Oxid\Controllers\Traits\RequestParameters;

require_once VENDOR_PATH . 'oxid-esales/oxideshop-ce/source/Core/Smarty/Plugin/function.oxprice.php';

/**
 * Class ProductExport
 * @package Yoochoose\Oxid\Controllers
 */
class ProductExport extends FrontendController
{

    use RequestParameters;

    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        $conf = $this->getConfig();
        $result = [];
        $viewConf = new ViewConfig();
        $articleBox = new ArticleBox();

        $utilsView = Registry::getUtilsView();

        $smarty = $utilsView->getSmarty();
        $products = $this->getRequestParameter('products');
        $action = $viewConf->getSelfActionLink();
        $token = $viewConf->getHiddenSid();
        $fnc = $articleBox->getToBasketFunction() ? $articleBox->getToBasketFunction() : 'tobasket';
        $actPage = $this->getActPage();
        $oWishId = $smarty->_tpl_vars['owishid'] ? $smarty->_tpl_vars['owishid'] : null;
        $currency = $conf->getActShopCurrencyObject();

        $list = new ArticleList();
        $list->loadIds(explode(',', $products));

        /** @var Article $oArticle */
        foreach ($list->getArray() as $oArticle) {
            $oArticle->enablePriceLoad();
            // if article has parent it's variation, so load parent instead
            $parentId = $oArticle->getParentId();
            if (!empty($parentId)) {
                $oArticle = new Article();
                $oArticle->load($parentId);
            }

            if (!$oArticle->isVisible()) {
                continue;
            }

            $variants = $oArticle->getVariantIds();
            $result[$oArticle->getID()] = [
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
                'owishid' => $oWishId,
                'newPrice' => smarty_function_oxprice(
                    ['price' => $oArticle->getVarMinPrice()->getBruttoPrice(), 'currency' => $currency],
                    $smarty
                ),
                'oldPrice' => smarty_function_oxprice(
                    ['price' => $oArticle->getTPrice(), 'currency' => $currency],
                    $smarty
                ),
                'showCartButton' => empty($variants),
            ];
        }

        header('Content-Type: application/json');
        exit(json_encode(array_values($result)));
    }
}
