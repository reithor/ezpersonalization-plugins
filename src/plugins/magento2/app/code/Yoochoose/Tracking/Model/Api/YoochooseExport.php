<?php

namespace Yoochoose\Tracking\Model\Api;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Yoochoose\Tracking\Api\YoochooseExportInterface;
use Yoochoose\Tracking\Logger\Logger;

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
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var Yoochoose
     */
    private $api;

    /**
     * YoochooseExport constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scope
     * @param StoreManagerInterface $store
     * @param Config $config
     * @param Logger $logger
     * @param RequestInterface $request
     * @param Yoochoose $api
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scope,
        StoreManagerInterface $store,
        Config $config,
        Logger $logger,
        RequestInterface $request,
        Yoochoose $api
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scope = $scope;
        $this->store = $store;
        $this->config = $config;
        $this->logger = $logger;
        $this->request = $request;
        $this->om = ObjectManager::getInstance();
        $this->api = $api;
    }

    /**
     * @param array $post
     *
     * @return mixed
     */

    /**
     * Dispatch request
     *
     * @return bool
     * @throws AuthorizationException
     * @throws LocalizedException
     */
    public function startExport()
    {
        $this->authenticate();

        $post = [];
        $this->om->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();
        $enable = $this->scope->getValue('yoochoose/export/enable_flag');

        $post['limit'] = $this->request->getParam('size');
        $post['mandator'] = $this->request->getParam('mandator');
        $post['webHookUrl'] = $this->request->getParam('webHook');

        /** Checks if size, mandator, web hook is set */
        if (!isset($post['limit']) || empty($post['limit']) || !isset($post['webHookUrl'])
            || empty($post['webHookUrl']) || !isset($post['mandator']) || empty($post['mandator'])) {
            throw new LocalizedException(__('Size, mandator and webHook parameters must be set.'));
        } else {
            if ($enable != 1) {
                $requestUri = $this->request->getRequestUri();
                $queryString = substr($requestUri, strpos($requestUri, '?') + 1);
                $this->logger->info('Export has started, with this query string : ' . $queryString);

                $post['transaction'] = $this->request->getParam('transaction');
                $post['password'] = $this->generateRandomString();
                $post['storeData'] = $this->getStoreData($post['mandator']);

                if (empty($post['storeData'])) {
                    throw new LocalizedException(__('Mandator is not correct!'));
                } else {
                    $post['storeData'] = json_encode($post['storeData']);
                }

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
     *
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
     *
     * @return string execute
     */
    private function triggerExport($url, $post = array())
    {
        $this->logger->info('Trigger action called with URL: ' . $url . ' and parameters: ' . json_encode($post));
        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cURL, CURLOPT_HEADER, ['Content-Type: application/json',]);
        curl_setopt($cURL, CURLOPT_NOBODY, true);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $post);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);

        curl_setopt($cURL, CURLOPT_TIMEOUT, 1);

        return curl_exec($cURL);
    }

    /**
     * Returns store ids as key and language as value based on madator id.
     *
     * @param array $mandator
     *
     * @return array
     */
    private function getStoreData($mandator)
    {
        $result = array();
        $storeViews = $this->api->getStoreViews();

        foreach ($storeViews as $storeView) {
            $baseMandator = $this->scope->getValue('yoochoose/general/customer_id', 'stores', $storeView['id']);

            if ($baseMandator == $mandator) {
                $result[$storeView['id']] = str_replace('_', '-', $storeView['language']);
            }
        }

        return $result;
    }

    /**
     * Check if sent token is correct
     *
     * @throws AuthorizationException
     */
    private function authenticate()
    {
        $authFallback = [];
        $authFallback[] = str_replace('Bearer ', '', $this->request->getHeader('Authorization', ''));
        $authFallback[] = str_replace('Bearer ', '', $this->request->getHeader('YCAuth', ''));
        $authFallback[] = urldecode($this->request->getParam('ycauth', ''));
        $token = $this->scope->getValue('yoochoose/auth/auth_token');

        if (!in_array($token, $authFallback, true)) {
            throw new AuthorizationException(new Phrase('Invalid authorization token.'));
        }
    }
}
