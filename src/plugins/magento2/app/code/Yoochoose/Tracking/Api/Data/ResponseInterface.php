<?php

namespace Yoochoose\Tracking\Api\Data;

interface ResponseInterface
{
    /**
     * @api
     * @return string
     */
    public function getShop();

    /**
     * @api
     * @param string $shop
     * @return null
     */
    public function setShop($shop);

    /**
     * @api
     * @return string
     */
    public function getShopVersion();

    /**
     * @api
     * @param string $shopVersion
     * @return null
     */
    public function setShopVersion($shopVersion);

    /**
     * @api
     * @return string
     */
    public function getPluginVersion();

    /**
     * @api
     * @param string $pluginVersion
     * @return null
     */
    public function setPluginVersion($pluginVersion);

    /**
     * @api
     * @return string
     */
    public function getMandator();

    /**
     * @api
     * @param string $mandator
     * @return null
     */
    public function setMandator($mandator);

    /**
     * @api
     * @return string
     */
    public function getLicenceKey();

    /**
     * @api
     * @param string $licenceKey
     * @return null
     */
    public function setLicenceKey($licenceKey);

    /**
     * @api
     * @return string
     */
    public function getPluginId();

    /**
     * @api
     * @param string $pluginId
     * @return null
     */
    public function setPluginId($pluginId);

    /**
     * @api
     * @return string
     */
    public function getEndpoint();

    /**
     * @api
     * @param string $endpoint
     * @return null
     */
    public function setEndpoint($endpoint);

    /**
     * @api
     * @return string
     */
    public function getDesign();

    /**
     * @api
     * @param string $design
     * @return null
     */
    public function setDesign($design);

    /**
     * @api
     * @return string
     */
    public function getItemType();

    /**
     * @api
     * @param string $itemType
     * @return null
     */
    public function setItemType($itemType);

    /**
     * @api
     * @return array
     */
    public function getScriptUris();

    /**
     * @api
     * @param $sctiptUris
     * @return null
     */
    public function setScriptUris($scriptUris);

    /**
     * @api
     * @return string
     */
    public function getOverwriteEndpoint();

    /**
     * @api
     * @param string $overwriteEndpoint
     * @return null
     */
    public function setOverwriteEndpoint($overwriteEndpoint);

    /**
     * @api
     * @return boolean
     */
    public function isSearchEnabled();

    /**
     * @api
     * @param boolean $searchEnabled
     * @return null
     */
    public function setSearchEnabled($searchEnabled);

    /**
     * @api
     * @return string
     */
    public function getAuthorizationToken();

    /**
     * @api
     * @param string $authorizationToken
     * @return null
     */
    public function setAuthorizationToken($authorizationToken);

    /**
     * @api
     * @return string
     */
    public function getPhpVersion();

    /**
     * @api
     * @param string $phpVersion
     * @return null
     */
    public function setPhpVersion($phpVersion);

    /**
     * @api
     * @return string
     */
    public function getOs();

    /**
     * @api
     * @param string $os
     * @return null
     */
    public function setOs($os);
}