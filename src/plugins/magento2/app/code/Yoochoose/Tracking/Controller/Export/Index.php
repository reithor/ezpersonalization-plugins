<?php

namespace Yoochoose\Tracking\Controller\Export;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Model\ResourceModel\Config;
use Yoochoose\Tracking\Logger\Logger;

class Index extends Action
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
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scope
     * @param StoreManagerInterface $store
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, ScopeConfigInterface $scope, StoreManagerInterface $store, Config $config, Logger $logger)
    {

        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
        $this->scope = $scope;
        $this->store = $store;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->_objectManager->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();
        $enable = $this->scope->getValue('yoochoose/export/enable_flag');

        if ($enable != 1) {

            $requestUri = $this->getRequest()->getRequestUri();
            $queryString = substr($requestUri, strpos($requestUri, '?') + 1);
            $this->logger->info('Export has started, with this query string : ' . $queryString);

            $post = [];
            $post['limit'] = $this->getRequest()->getParam('size');
            $post['webHookUrl'] = $this->getRequest()->getParam('webHook');
            $post['password'] = $this->generateRandomString();

            $this->config->saveConfig('yoochoose/export/password', $post['password'], 'default', 0);

            $baseUrl = $this->store->getStore()->getBaseUrl();

            $this->triggerExport($baseUrl . '/yoochoose/trigger', $post);

            $response = [
                'success' => true
            ];

        } else {
            $response = [
                'success' => false,
                'message' => 'Job not sent'
            ];
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);

        return $result;
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
     * @return cURL execute
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
