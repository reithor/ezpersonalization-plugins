<?php

namespace Yoochoose\Tracking\Model\Api;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Model\ResourceModel\Config;
use Yoochoose\Tracking\Api\YoochooseExportInterface;
use Yoochoose\Tracking\Logger\Logger;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;


class YoochooseExport implements YoochooseExportInterface
{

    /** @var  \Magento\Framework\Controller\Result\Json */
    protected $resultJsonFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scope;

    /**
     * @var StoreManagerInterface
     */
    private $store;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * YoochooseExport constructor.
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scope
     * @param StoreManagerInterface $store
     * @param Config $config
     * @param Logger $logger
     * @param RequestInterface $request
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scope,
        StoreManagerInterface $store,
        Config $config,
        Logger $logger,
        RequestInterface $request
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scope = $scope;
        $this->store = $store;
        $this->config = $config;
        $this->logger = $logger;
        $this->request = $request;
        $this->om = ObjectManager::getInstance();
    }

    /**
     * @param array $post
     * @return mixed
     */

    /**
     * Dispatch request
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function startExport()
    {
        $this->om->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();
        $enable = $this->scope->getValue('yoochoose/export/enable_flag');
        $size = $this->request->getParam('size');

        /** Checks if size is set */
        if (!isset($size) || empty($size)) {
            throw new LocalizedException(__('Size must be set'));
        } else {
            if ($enable != 1) {
                $requestUri = $this->request->getRequestUri();
                $queryString = substr($requestUri, strpos($requestUri, '?') + 1);
                $this->logger->info('Export has started, with this query string : ' . $queryString);

                $post = [];
                $post['limit'] = $this->request->getParam('size');
                $post['webHookUrl'] = $this->request->getParam('webHook');
                $post['storeView'] = $this->request->getParam('storeView');
                $post['password'] = $this->generateRandomString();

                $this->config->saveConfig('yoochoose/export/password', $post['password'], 'default', 0);

                $baseUrl = $this->store->getStore()->getBaseUrl();

                $this->triggerExport($baseUrl . '/yoochoose/trigger', $post);
            } else {
                throw new LocalizedException(__('Job not sent'));
            }
        }

        return true;
    }

    /**
     * Generates random string with $length characters
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }


    /**
     * triggerExport
     *
     * @param string @url
     * @param array $post
     * @return string execute
     */
    private function triggerExport($url, $post = array()) {

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cURL, CURLOPT_HEADER, false);
        curl_setopt($cURL, CURLOPT_NOBODY, true);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $post);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);

        curl_setopt($cURL, CURLOPT_TIMEOUT, 1);

        return curl_exec($cURL);
    }

}
