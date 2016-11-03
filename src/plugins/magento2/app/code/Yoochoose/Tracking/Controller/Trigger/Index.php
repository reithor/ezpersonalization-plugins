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
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scope
     * @param Config $config
     * @param Data $dataHelper
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, ScopeConfigInterface $scope, Config $config, Data $dataHelper)
    {
        parent::__construct($context);
        $this->scope = $scope;
        $this->config = $config;
        $this->helper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $response = ['success' => false];

        $this->_objectManager->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();

        $limit = $this->getRequest()->getParam('limit');
        $callbackUrl = $this->getRequest()->getParam('webHookUrl');
        $postPassword = $this->getRequest()->getParam('password');
        $password = $this->scope->getValue('yoochoose/export/password');

        if ($password === $postPassword) {
            //lock
            $this->config->saveConfig('yoochoose/export/enable_flag', 1, 'default', 0);

            try {
                $postData = $this->helper->export($limit);
                $this->setCallback($callbackUrl, $postData);
                $response['success'] = true;
            } catch (\Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            } finally {
                $this->config->saveConfig('yoochoose/export/enable_flag', 0, 'default', 0);
            }
        } else {
            $response['message'] = 'Passwords do not match!';
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);

        return $result;
    }

    /**
     * Creates request and returns response
     *
     * @param string @url
     * @param array @post
     * @return array
     * @internal param mixed $params
     */
    private function setCallback($url, $post)
    {
        $postString = json_encode($post);

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($cURL);
        curl_close($cURL);

        return json_decode($response, true);
    }

}
