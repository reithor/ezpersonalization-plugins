<?php

use Shopware\Components\YoochooseHelper;
use Shopware\Components\YoochooseLogger;

/**
 * Class Shopware_Controllers_Api_Ycexport
 */
class Shopware_Controllers_Api_Ycexport extends Shopware_Controllers_Api_Rest
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
        $post['limit'] = $this->Request()->getParam('size');
        $post['mandator'] = $this->Request()->getParam('mandator');
        $post['webHook'] = $this->Request()->getParam('webHook');

        /** Checks if size, mandator, web hook is set */
        if (!isset($post['limit']) || empty($post['limit']) || !isset($post['webHook'])
            || empty($post['webHook']) || !isset($post['mandator']) || empty($post['mandator'])
        ) {
            throw new Exception('Size, mandator and webHook parameters must be set.');
        } else {
            $shopId = $this->helper->getDefaultShopId();
            if ($this->Request()->getParam('forceStart', false)) {
                $this->helper->saveConfigParam('enable_flag', 0, $shopId);
            }

            $enable = $this->helper->getConfigParam('enable_flag', $shopId);
            if ($enable != 1) {
                $requestUri = $this->request->getRequestUri();
                $queryString = substr($requestUri, strpos($requestUri, '?') + 1);
                YoochooseLogger::log(1, 'Export has started, with this query string : ' . $queryString, '', '', '');

                $post['transaction'] = $this->Request()->getParam('transaction');
                $post['password'] = $this->helper->generateRandomString();
                $this->helper->saveConfigParam('password', $post['password'], $shopId);
                $post['storeData'] = $this->getStoreData($post['mandator']);

                if (empty($post['storeData'])) {
                    throw new Exception('Mandator is not correct!');
                } else {
                    $post['storeData'] = json_encode($post['storeData']);
                }

                $baseUrl = Shopware()->Modules()->Core()->sRewriteLink();
                $this->triggerExport($baseUrl . 'Yctrigger?' . http_build_query($post));
            } else {
                throw new Exception('Job not sent');
            }
        }

        $this->View()->assign('success', true);
    }

    /**
     * triggerExport
     *
     * @param string $url
     * @return string execute
     */
    private function triggerExport($url)
    {
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cURL, CURLOPT_HEADER, ['Content-Type: application/json',]);
        curl_setopt($cURL, CURLOPT_NOBODY, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($cURL, CURLOPT_TIMEOUT, 1);

        return curl_exec($cURL);
    }

    /**
     * Returns store ids as key and language as value based on madator id.
     *
     * @param array $mandator
     * @return array
     */
    private function getStoreData($mandator)
    {
        $result = array();
        $storeIds = $this->em->getRepository('Shopware\Models\Shop\Shop')->findAll();

        foreach ($storeIds as $storeId) {
            $language = $storeId->getLocale()->getLocale();
            $baseMandator = $this->em->getRepository('Shopware\Models\Yoochoose\Yoochoose')
                ->findOneBy(array('name' => 'customerId', 'shop' => $storeId->getId()));

            if (isset($baseMandator) && $baseMandator->getValue() == $mandator) {
                $result[$storeId->getId()] = str_replace('_', '-', $language);
            }
        }

        return $result;
    }

}
