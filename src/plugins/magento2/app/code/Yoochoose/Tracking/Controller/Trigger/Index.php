<?php

namespace Yoochoose\Tracking\Controller\Trigger;

ignore_user_abort(true);
set_time_limit(0);

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Controller\Result\JsonFactory;
use Yoochoose\Tracking\Helper\Data;
use Yoochoose\Tracking\Logger\Logger;

class Index extends Action
{

    /**
     * @var ScopeConfigInterface
     */
    private $scope;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scope
     * @param Config $config
     * @param Data $dataHelper
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scope,
        Config $config,
        Data $dataHelper,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->scope = $scope;
        $this->config = $config;
        $this->helper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
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
        $this->logger->info('Trigger export action started.');
        $response = ['success' => false];

        $this->_objectManager->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();

        $limit = $this->getRequest()->getParam('limit');
        $callbackUrl = $this->getRequest()->getParam('webHookUrl');
        $postPassword = $this->getRequest()->getParam('password');
        $transaction = $this->getRequest()->getParam('transaction');
        $storeData = json_decode($this->getRequest()->getParam('storeData', '[]'), true);
        $customerId = $this->getRequest()->getParam('mandator');

        $password = $this->scope->getValue('yoochoose/export/password');
        $storeId = key($storeData);
        $licenceKey = $this->scope->getValue('yoochoose/general/license_key', 'stores', $storeId);

        if ($password === $postPassword) {
            //lock
            $this->config->saveConfig('yoochoose/export/enable_flag', 1, 'default', 0);
            try {
                $postData = $this->helper->export($storeData, $transaction, $limit, $customerId);
                $this->setCallback($callbackUrl, $postData, $customerId, $licenceKey);
                $response['success'] = true;
            } catch (\Exception $e) {
                $this->logger->info('Export failed. ' . $e->getMessage());
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            } finally {
                $this->config->saveConfig('yoochoose/export/enable_flag', 0, 'default', 0);
            }
        } else {
            $response['message'] = 'Passwords do not match!';
            $this->logger->info('Trigger export action failed with message: ' . $response['message']);
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);

        return $result;
    }

    /**
     * Creates request and returns response
     *
     * @param string $url
     * @param string $post
     * @param string $customerId
     * @param string $licenceKey
     * @return array
     * @internal param mixed $params
     */
    private function setCallback($url, $post, $customerId, $licenceKey)
    {
        $postString = json_encode($post);
        $this->logger->info('Callback sent to URL ' . $url . ' with post ' . $postString);

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cURL, CURLOPT_USERPWD, $customerId . ":" . $licenceKey);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_HEADER, true);

        $response = curl_exec($cURL);

        $header_size = curl_getinfo($cURL, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $header = str_replace("\r\n", '', $header);
        $body = substr($response, $header_size);
        $this->logger->info('Callback header : ' . $header . ' Callback body : ' . $body);
        curl_close($cURL);

        return json_decode($response, true);
    }

}
