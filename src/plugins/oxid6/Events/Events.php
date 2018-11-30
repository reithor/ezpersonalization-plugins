<?php

namespace Yoochoose\Oxid\Events;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;
use OxidEsales\EshopCommunity\Core\Registry;

/**
 * Class Events
 * @package Yoochoose\Oxid\Events
 */
class Events extends Module
{
    /**
     * Map of SEO Urls
     * @var array
     */
    public static $endpoints = [
        'Yoochoose/' => 'index.php?cl=ycproductexport',
        'Yoochoose/Articles/' => 'index.php?cl=yoochoosearticles',
        'Yoochoose/Shops/' => 'index.php?cl=yoochooseshops',
        'Yoochoose/Categories/' => 'index.php?cl=yoochoosecategories',
        'Yoochoose/Users/' => 'index.php?cl=yoochooseusers',
        'Yoochoose/Export/' => 'index.php?cl=yoochooseexport',
        'Yoochoose/Trigger/' => 'index.php?cl=yoochoosetrigger',
    ];

    /**
     * This method is executed when module is being activated, it will subscribe SEO Urls for export endpoints
     *
     * @throws DatabaseException
     */
    public static function onActivate()
    {
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $sQtedType = $oDb->quote('static');
        foreach (self::$endpoints as $seoUrl => $value) {
            foreach (Registry::getConfig()->getShopIds() as $iShopId) {
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

                DatabaseProvider::getDb()->execute($sSql);
            }
        }
    }

    /**
     * This method is executed when module is being deactivated, it removes Yoochoose SEO Urls from shop
     *
     * @throws DatabaseException
     */
    public static function onDeactivate()
    {
        DatabaseProvider::getDb()->execute("DELETE FROM oxseo WHERE OXSEOURL LIKE '%Yoochoose/%'");
    }
}
