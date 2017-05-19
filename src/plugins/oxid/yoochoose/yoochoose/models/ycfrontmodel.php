<?php

class Ycfrontmodel extends Ycfrontmodel_parent
{

    const YOOCHOOSE_CDN_SCRIPT = '//event.yoochoose.net/cdn';
    const AMAZON_CDN_SCRIPT = '//cdn.yoochoose.net';
    const YOOCHOOSE_ADMIN_URL = '//admin.yoochoose.net/';

    public function render()
    {
        return $this;
    }

    public function t()
    {
        $language = $this->getActLanguageAbbr();
        $currentPage = $this->getCurrentPage($this->getActiveClassName());
        /** @var oxConfig $conf */
        $conf = $this->getConfig();
        $itemType = $conf->getConfigParam('ycItemType');

        /* @var oxSuperCfg $cfg */
        $cfg = oxNew('oxsupercfg');
        $user = $cfg->getUser();

        $json = array(
            'trackid' => $user ? $user->oxuser__oxid->value : 0,
            'url' => $cfg->getConfig()->getShopUrl(),
            'language' => $language,
            'currentPage' => $currentPage,
            'itemType' => $itemType ? $itemType : 1,
            'products' => $this->getPageProducts($currentPage),
        );

        return '<script type="text/javascript">var yc_config_object=' . json_encode($json) . '</script>';
    }

    public function renderScripts()
    {
        $result = '';

        $conf = $this->getConfig();
        $mandator = $conf->getConfigParam('ycCustomerId');
        $plugin = $conf->getConfigParam('ycPluginId');
        $plugin = $plugin ? '/' . $plugin : '';
        $scriptOverwrite = $conf->getConfigParam('ycOverwrite');

        if ($scriptOverwrite) {
            $scriptOverwrite = (!preg_match('/^(http|\/\/)/', $scriptOverwrite) ? '//' : '') . $scriptOverwrite;
            $scriptUrl = preg_replace('(^https?:)', '', $scriptOverwrite);
        } else {
            $scriptUrl = $conf->getConfigParam('ycPerformance') ? self::AMAZON_CDN_SCRIPT : self::YOOCHOOSE_CDN_SCRIPT;
        }

        $scriptUrl = rtrim($scriptUrl, '/') . "/v1/{$mandator}{$plugin}/tracking.";
        $result .= sprintf('<script type="text/javascript" src="%s"></script>', $scriptUrl . 'js');
        $result .= sprintf('<link rel="stylesheet" type="text/css" href="%s" />', $scriptUrl . 'css');
        $result .= $this->t();

        return $result;
    }

    private function getBasketItems()
    {
        $session = oxRegistry::getSession();
        $basketItems = $session->getBasket()->getContents();
        $items = array();
        foreach ($basketItems as $val) {
            $items[] = $this->getParentArticleId($val->getProductId());
        }

        return implode(',', $items);
    }

    private function getCurrentPage($page)
    {
        switch ($page) {
            case 'start':
                return 'home';
            case 'details':
                return 'product';
            case 'alist':
            case 'manufacturerlist':
                return 'category';
            case 'basket':
                return 'cart';
            case 'thankyou':
                return 'buyout';
        }

        return null;
    }

    private function getPageProducts($page)
    {
        switch ($page) {
            case 'product':
                return $this->getParentArticleId($this->getActArticleId());
            case 'cart':
                return $this->getBasketItems();
            default:
                return null;
        }
    }

    /**
     * Returns parent article id.
     *
     * @param string $articleId
     * @return string
     */
    private function getParentArticleId($articleId)
    {
        /** @var oxArticle $oArticle */
        $oArticle = oxNew('oxarticle');
        if ($oArticle->load($articleId)) {
            $parentId = $oArticle->getParentId();
            $articleId = empty($parentId) ? $articleId : $parentId;
        }

        return $articleId;
    }

}
