<?php

namespace Yoochoose\Tracking\Model\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Yoochoose\Tracking\Helper\Data;
use Magento\Framework\Validator\Exception;
use Magento\Framework\Phrase;

class ConfigUpdate implements ObserverInterface
{
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    /** @var ScopeConfigInterface */
    private $config;

    /** @var Data */
    private $helper;

    /** @var ObjectManagerInterface */
    private $om;

    public function __construct(ScopeConfigInterface $scope, Data $dataHelper, ObjectManagerInterface $om)
    {
        $this->config = $scope;
        $this->helper = $dataHelper;
        $this->om = $om;
    }

    /**
     * @param Observer| $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $customerId = $this->config->getValue('yoochoose/general/customer_id');
        $licenseKey = $this->config->getValue('yoochoose/general/license_key');

        if (!$customerId && !$licenseKey) {
            return;
        }

        $token = $this->getAdminToken();

        $design = $this->config->getValue('yoochoose/general/design');
        $body = [
            'base' => [
                'type' => 'MAGENTO2',
                'pluginId' => $this->config->getValue('yoochoose/general/plugin_id'),
                'endpoint' => $this->config->getValue('yoochoose/general/endpoint'),
                'appKey' => '',
                'appSecret' => $token,
            ],
            'frontend' => [
                'design' => $design,
            ],
            'search' => [
                'design' => $design,
            ],
        ];

        $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/update?createIfNeeded=true&fallbackDesign=true';
        $this->helper->getHttpPage($url, $body, $customerId, $licenseKey);
    }

    /**
     * Returns admin token that is used for api authentication.
     * Token is either fetched if it exists and is not revoked or new token is created.
     *
     * @return string
     */
    protected function getAdminToken()
    {
        /** @var \Magento\Backend\Model\Auth\Session $adminSession */
        /** @var \Magento\Integration\Model\Oauth\Token $tokenModel */
        $adminSession = $this->om->get('Magento\Backend\Model\Auth\Session');
        $tokenModel = $this->om->get('Magento\Integration\Model\Oauth\Token');
        $adminId = $adminSession->getUser()->getData('user_id');
        $tokenModel->loadByAdminId($adminId);

        // if token doesn't exist or token is revoked, create new token
        if (!$tokenModel->getToken() || $tokenModel->getRevoked()) {
            $tokenModel->createAdminToken($adminId);
        }

        return $tokenModel->getToken();
    }
}
