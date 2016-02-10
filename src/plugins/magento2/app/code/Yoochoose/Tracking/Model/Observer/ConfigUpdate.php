<?php

namespace Yoochoose\Tracking\Model\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
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

    public function __construct(ScopeConfigInterface $scope, Data $dataHelper)
    {
        $this->config = $scope;
        $this->helper = $dataHelper;
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

        $design = $this->config->getValue('yoochoose/general/design');
        $body = [
            'base' => [
                'type' => 'MAGENTO',
                'pluginId' => $this->config->getValue('yoochoose/general/plugin_id'),
                'endpoint' => $this->config->getValue('yoochoose/general/endpoint'),
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
}