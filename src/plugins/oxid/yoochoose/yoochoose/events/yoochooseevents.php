<?php

class yoochooseEvents
{

    /**
     * Map of SEO Urls
     * @var array
     */
    public static $endpoints = array(
        'Yoochoose/' => 'index.php?cl=ycproductexport',
        'Yoochoose/Articles/' => 'index.php?cl=yoochoosearticles',
        'Yoochoose/Stores/' => 'index.php?cl=yoochoosestoreview',
        'Yoochoose/Categories/' => 'index.php?cl=yoochoosecategories',
        'Yoochoose/Users/' => 'index.php?cl=yoochooseusers',
    );

    public static function onActivate()
    {
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sQtedType = $oDb->quote('static');
        foreach (self::$endpoints as $seoUrl => $value) {
            foreach (oxRegistry::getConfig()->getShopIds() as $iShopId) {
                $seoHash = md5(strtolower($seoUrl));
                $iQtedShopId = $oDb->quote($iShopId);
                $sQtedStdUrl = $oDb->quote($value);
                $sQtedSeoUrl = $oDb->quote($seoUrl);
                $sQtedIdent = $oDb->quote($seoHash);

                $sSql = "INSERT INTO oxseo
                    (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams)
                VALUES
                    ( {$sQtedIdent}, {$sQtedIdent}, {$iQtedShopId}, -1, {$sQtedStdUrl}, {$sQtedSeoUrl}, {$sQtedType}, '0', '0', '' )
                ON duplicate KEY UPDATE
                    oxident = {$sQtedIdent}, oxstdurl = {$sQtedStdUrl}, oxseourl = {$sQtedSeoUrl}, oxfixed = '', oxexpired = '0'";

                oxDb::getDb()->execute($sSql);
            }
        }
    }

    public static function onDeactivate()
    {
        oxDb::getDb()->execute("DELETE FROM oxseo WHERE OXSEOURL LIKE '%Yoochoose/%'");
    }

}
