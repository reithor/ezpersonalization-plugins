<?php

namespace Yoochoose\Tracking\Model\Api;

use Yoochoose\Tracking\Api\Data\ResponseInterface;

class Response implements ResponseInterface
{

    /** @var string  */
    private $shop;
    /** @var string  */
    private $shopVersion;
    /** @var string  */
    private $pluginVersion;
    /** @var string  */
    private $mandator;
    /** @var string  */
    private $licenceKey;
    /** @var string  */
    private $pluginId;
    /** @var string  */
    private $endpoint;
    /** @var string  */
    private $design;
    /** @var string  */
    private $itemType;
    /** @var array  */
    private $scriptUris;
    /** @var string  */
    private $overwriteEndpoint;
    /** @var bool  */
    private $searchEnabled;
    /** @var string  */
    private $authorizationToken;
    /** @var string  */
    private $phpVersion;
    /** @var string  */
    private $os;

    /**
     * @api
     * @return string
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @api
     * @param string $shop
     * @return null|void
     */
    public function setShop($shop)
    {
        $this->shop = $shop;
    }

    /**
     * @api
     * @return string
     */
    public function getShopVersion()
    {
        return $this->shopVersion;
    }

    /**
     * @api
     * @param string $shopVersion
     * @return null|void
     */
    public function setShopVersion($shopVersion)
    {
        $this->shopVersion = $shopVersion;
    }

    /**
     * @api
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->pluginVersion;
    }

    /**
     * @api
     * @param string $pluginVersion
     * @return null|void
     */
    public function setPluginVersion($pluginVersion)
    {
        $this->pluginVersion = $pluginVersion;
    }

    /**
     * @api
     * @return string
     */
    public function getMandator()
    {
        return $this->mandator;
    }

    /**
     * @api
     * @param string $mandator
     * @return null|void
     */
    public function setMandator($mandator)
    {
        $this->mandator = $mandator;
    }

    /**
     * @api
     * @return string
     */
    public function getLicenceKey()
    {
        return $this->licenceKey;
    }

    /**
     * @api
     * @param string $licenceKey
     * @return null|void
     */
    public function setLicenceKey($licenceKey)
    {
        $this->licenceKey = $licenceKey;
    }

    /**
     * @api
     * @return string
     */
    public function getPluginId()
    {
        return $this->pluginId;
    }

    /**
     * @api
     * @param string $pluginId
     * @return null|void
     */
    public function setPluginId($pluginId)
    {
        $this->pluginId = $pluginId;
    }

    /**
     * @api
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @api
     * @param string $endpoint
     * @return null|void
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @api
     * @return string
     */
    public function getDesign()
    {
        return $this->design;
    }

    /**
     * @api
     * @param string $design
     * @return null|void
     */
    public function setDesign($design)
    {
        $this->design = $design;
    }

    /**
     * @api
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @api
     * @param string $itemType
     * @return null|void
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     * @api
     * @return array
     */
    public function getScriptUris()
    {
        return $this->scriptUris;
    }

    /**
     * @api
     * @param array $scriptUris
     * @return null|void
     */
    public function setScriptUris($scriptUris)
    {
        $this->scriptUris = $scriptUris;
    }

    /**
     * @api
     * @return string
     */
    public function getOverwriteEndpoint()
    {
        return $this->overwriteEndpoint;
    }

    /**
     * @api
     * @param string $overwriteEndpoint
     * @return null|void
     */
    public function setOverwriteEndpoint($overwriteEndpoint)
    {
        $this->overwriteEndpoint = $overwriteEndpoint;
    }

    /**
     * @api
     * @return boolean
     */
    public function isSearchEnabled()
    {
        return $this->searchEnabled;
    }

    /**
     * @api
     * @param boolean $searchEnabled
     * @return null|void
     */
    public function setSearchEnabled($searchEnabled)
    {
        $this->searchEnabled = $searchEnabled;
    }

    /**
     * @api
     * @return string
     */
    public function getAuthorizationToken()
    {
        return $this->authorizationToken;
    }

    /**
     * @api
     * @param string $authorizationToken
     * @return null|void
     */
    public function setAuthorizationToken($authorizationToken)
    {
        $this->authorizationToken = $authorizationToken;
    }

    /**
     * @api
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }

    /**
     * @api
     * @param string $phpVersion
     * @return null|void
     */
    public function setPhpVersion($phpVersion)
    {
        $this->phpVersion = $phpVersion;
    }

    /**
     * @api
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @api
     * @param string $os
     * @return null|void
     */
    public function setOs($os)
    {
        $this->os = $os;
    }

}
