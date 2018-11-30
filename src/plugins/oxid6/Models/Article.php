<?php

namespace Yoochoose\Oxid\Models;

use OxidEsales\Eshop\Application\Model\Article as ArticleBase;

/**
 * Class Article
 * @package Yoochoose\Oxid\Models
 */
class Article extends ArticleBase
{

    /**
     * Resets loaded cache of parent articles
     * This is added to reduce memory load while exporting articles
     */
    public function resetLoadedParents()
    {
        static::$_aLoadedParents = [];
    }
}
