<?php

use Shopware\Components\YoochooseHelper;

/**
 * Class Shopware_Controllers_Api_Ycinfo
 */
class Shopware_Controllers_Api_Ycinfo extends Shopware_Controllers_Api_Rest
{

    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $em;

    private $helper;

    public function __construct(
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_Response $response
    )
    {
        parent::__construct($request, $response);
        $this->em = Shopware()->Models();
        $this->helper = new YoochooseHelper();
    }

    public function indexAction()
    {
        /** @var \Shopware\Models\Shop\Repository $shopRepository */
        $shopRepository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');

        try {
            $currentShopId = Shopware()->Shop()->getId();
        } catch (Exception $e) {
            // Do nothing, just checking if this is default shop request
        }

        $shop = isset($currentShopId) ?
            $shopRepository->getActiveById($currentShopId) :
            $shopRepository->getActiveDefault();

        $plugin = $this->em->getRepository('Shopware\Models\Plugin\Plugin')->findOneBy(array('name' => 'YoochooseJsTracking'));

        $result = [
            'shop' => $shop->getName(),
            'shop_version' => (int)Shopware()->Config()->version,
            'plugin_version' => $plugin->getVersion(),
            'mandator' => $this->helper->getConfigParam('customerId', $shop->getId()),
            'license_key' => $this->helper->getConfigParam('licenseKey', $shop->getId()),
            'plugin_id' => $this->helper->getConfigParam('pluginId', $shop->getId()),
            'endpoint' => $this->helper->getConfigParam('endpoint', $shop->getId()),
            'design' => $this->helper->getConfigParam('design', $shop->getId()),
            'script_uris' => [
                YoochooseHelper::getTrackingScript('.js', $shop),
                YoochooseHelper::getTrackingScript('.css', $shop)
            ],
            'overwrite_endpoint' => $this->helper->getConfigParam('scriptUrl', $shop->getId()),
            'username' => $this->helper->getConfigParam('username', $shop->getId()),
            'api_key' => $this->helper->getConfigParam('apiKey', $shop->getId()),
            'php_version' => PHP_VERSION,
            'os' => PHP_OS
        ];

        $this->View()->assign($result);
    }
}
