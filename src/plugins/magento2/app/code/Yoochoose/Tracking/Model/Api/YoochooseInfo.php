<?php

namespace Yoochoose\Tracking\Model\Api;



use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Yoochoose\Tracking\Api\Data\ResponseInterface;
use Yoochoose\Tracking\Api\YoochooseInfoInterface;
use Magento\Setup\Model\PhpReadinessCheck;
use Yoochoose\Tracking\Api\Data\ResponseInterfaceFactory;


class YoochooseInfo implements YoochooseInfoInterface
{

    const YOOCHOOSE_CDN_SCRIPT = '//event.yoochoose.net/cdn';
    const AMAZON_CDN_SCRIPT = '//cdn.yoochoose.net';


    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var PhpReadinessCheck
     */
    private $phpReadinessCheck;


    /**
     * YoochooseInfo constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param PhpReadinessCheck $phpReadinessCheck
     * @param ResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        PhpReadinessCheck $phpReadinessCheck,
        ResponseInterfaceFactory $responseFactory
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->phpReadinessCheck = $phpReadinessCheck;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Returns array of basic information
     * @return ResponseInterface
     */
    public function getInfo()
    {
        $shopName = $this->storeManager->getStore()->getName();
        $shopVersion = $this->productMetadata->getVersion();
        $moduleCode = 'Yoochoose_Tracking';
        $pluginVersion = $this->moduleList->getOne($moduleCode);

        $mandator = $this->config->getValue('yoochoose/general/customer_id', 'stores');
        $licenseKey = $this->config->getValue('yoochoose/general/license_key', 'stores');
        $pluginId = $this->config->getValue('yoochoose/general/plugin_id', 'stores');
        $endpoint = $this->config->getValue('yoochoose/general/endpoint','stores');
        $design= $this->config->getValue('yoochoose/general/design', 'stores');
        $itemType = $this->config->getValue('yoochoose/general/item_type', 'stores');
        $overwriteEndpoint = $this->config->getValue('yoochoose/script/script_id', 'stores');
        $searchEnabled = $this->config->getValue('yoochoose/search/search_enable', 'stores');
        $authorizationToken = $this->config->getValue('yoochoose/auth/auth_token', 'stores');

        $plugin = $pluginId ? '/' . $pluginId : '';
        $scriptOverwrite = $this->config->getValue('yoochoose/script/script_id', 'stores');

        if ($scriptOverwrite) {
            $scriptOverwrite = (!preg_match('/^(http|\/\/)/', $scriptOverwrite) ? '//' : '') . $scriptOverwrite;
            $scriptUrl = preg_replace('(^https?:)', '', $scriptOverwrite);
        } else {
            $scriptUrl = $this->config->getValue('yoochoose/script/cdn_source', 'stores') ?
                self::AMAZON_CDN_SCRIPT : self::YOOCHOOSE_CDN_SCRIPT;
        }

        if (empty($mandator) || empty($plugin)) {
            $jsScriptUrl = null;
            $cssScriptUrl = null;
        } else {
            $jsScriptUrl = rtrim($scriptUrl, '/') . "/v1/{$mandator}{$plugin}/tracking.js";
            $cssScriptUrl = rtrim($scriptUrl, '/') . "/v1/{$mandator}{$plugin}/tracking.css";
        }

        $phpVersionCheckResult = $this->phpReadinessCheck->checkPhpVersion();
        $phpVersion = $phpVersionCheckResult['data']['current'];

        /** @var ResponseInterface $response */
        $response = $this->responseFactory->create();
        $response->setShop($shopName);
        $response->setShopVersion($shopVersion);
        $response->setPluginVersion($pluginVersion['setup_version']);
        $response->setMandator($mandator);
        $response->setLicenceKey($licenseKey);
        $response->setPluginId($pluginId);
        $response->setEndpoint($endpoint);
        $response->setDesign($design);
        $response->setItemType($itemType);
        $response->setScriptUris([
            $jsScriptUrl,
            $cssScriptUrl
        ]);
        $response->setOverwriteEndpoint($overwriteEndpoint);
        $response->setSearchEnabled($searchEnabled);
        $response->setSearchEnabled($searchEnabled);
        $response->setAuthorizationToken($authorizationToken);
        $response->setPhpVersion($phpVersion);
        $response->setOs(PHP_OS);

        return $response;
    }

}




