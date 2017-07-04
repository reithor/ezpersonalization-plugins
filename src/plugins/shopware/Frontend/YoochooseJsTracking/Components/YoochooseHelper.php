<?php

namespace Shopware\Components;

use Shopware\Models\Shop\Shop;
use  Shopware\Models\Yoochoose\Yoochoose;
use Shopware\Components\Api\Manager;

class YoochooseHelper
{

    const YC_MAX_FILE_SIZE = 52428800; // max file size size in bytes 50Mb
    const YC_DIRECTORY_NAME = 'yoochoose';
    const YOOCHOOSE_CDN_SCRIPT = '//event.yoochoose.net/cdn';
    const AMAZON_CDN_SCRIPT = '//cdn.yoochoose.net';
    const YOOCHOOSE_ADMIN_URL = '//admin.yoochoose.net/';

    /**
     * Fetches yoochoose config data
     *
     * @param string $name
     * @param mixed $default
     * @param int $shopId
     * @return mixed|string
     */
    public static function getYoochooseConfig($name, $default = '', $shopId = 1)
    {
        $element = Shopware()->Models()->getRepository('Shopware\Models\Yoochoose\Yoochoose')
            ->findOneBy(array('name' => $name, 'shop' => $shopId));

        return $element ? $element->getValue() : $default;
    }

    /**
     * Returns url of tracking js, css files
     *
     * @param string $type
     * @param Shop $shop
     * @return string
     */
    public static function getTrackingScript($type = '.js', $shop)
    {
        $customerId = self::getYoochooseConfig('customerId', '', $shop->getId());
        $licenceKey = self::getYoochooseConfig('licenseKey', '', $shop->getId());
        if (empty($customerId) || empty($licenceKey)) {
            return '';
        }

        $scriptOverwrite = self::getYoochooseConfig('scriptUrl', '', $shop->getId());
        $pluginId = self::getYoochooseConfig('pluginId', '', $shop->getId());
        $plugin = $pluginId ? '/' . $pluginId : '';
        $suffix = "/v1/{$customerId}{$plugin}/tracking";

        if ($scriptOverwrite) {
            $scriptOverwrite = (!preg_match('/^(http|\/\/)/', $scriptOverwrite) ? '//' : '') . $scriptOverwrite;
            $scriptUrl = preg_replace('(^https?:)', '', $scriptOverwrite);
        } else {
            $scriptUrl = self::getYoochooseConfig('performance', '', $shop->getId()) == 2 ?
                self::AMAZON_CDN_SCRIPT : self::YOOCHOOSE_CDN_SCRIPT;
        }

        return rtrim($scriptUrl, '/') . $suffix . $type;
    }

    /**
     * Generates random string with $length characters
     *
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 20)
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
     * Saves new value to newsletter2go table or updates existing one
     *
     * @param string $name
     * @param string $value
     * @param integer $shopId
     */
    public function saveConfigParam($name, $value, $shopId)
    {
        $em = Shopware()->Models();
        $element = $em->getRepository('Shopware\Models\Yoochoose\Yoochoose')
            ->findOneBy(array('name' => $name, 'shop' => $shopId));
        if (!$element) {
            $element = new Yoochoose();
            $element->setName($name);
        }

        $element->setValue($value);
        $shop = Shopware()->Models()->find('Shopware\Models\Shop\Shop', $shopId);
        $element->setShop($shop);
        $em->persist($element);
        $em->flush();
    }

    /**
     * Returns config value for $name, returns string if $name value exists,
     * otherwise it returns $default value.
     *
     * @param string $name
     * @param mixed $default
     * @param integer $shopId
     * @return null | string
     */
    public function getConfigParam($name, $shopId, $default = null)
    {
        $em = Shopware()->Models();
        $value = $em->getRepository('Shopware\Models\Yoochoose\Yoochoose')
            ->findOneBy(array('name' => $name, 'shop' => $shopId));

        return $value ? $value->getValue() : $default;
    }

    /**
     * Returns the id of the default shop.
     *
     * @return string
     */
    public function getDefaultShopId()
    {
        return Shopware()->Db()->fetchOne(
            'SELECT id FROM s_core_shops WHERE `default` = 1'
        );
    }

    /**
     * export to file
     *
     * @param $storeData
     * @param $transaction
     * @param $limit
     * @param $mandatorId
     * @return array
     */
    public function export($storeData, $transaction, $limit, $mandatorId)
    {
        $ycResponseFormats = array(
            'SHOPWARE' => 'Products',
            'SHOPWARE_CATEGORIES' => 'Categories',
            'SHOPWARE_VENDORS' => 'Vendors',
        );

        $postData = array(
            'transaction' => $transaction,
            'events' => [],
        );

        foreach ($ycResponseFormats as $format => $method) {
            if (!empty($storeData)) {
                foreach ($storeData as $storeId => $language) {
                    $postData['events'][] = [
                        'action' => 'FULL',
                        'format' => $format,
                        'contentTypeId' => 1,
                        'storeViewId' => $storeId,
                        'lang' => $language,
                        'credentials' => [
                            'login' => null,
                            'password' => null,
                        ],
                        'uri' => [],
                    ];
                    $storeIds[$method][] = $storeId;
                }
            }
        }

        $directory = Shopware()->DocPath() . 'media' . DS . self::YC_DIRECTORY_NAME . DS . $mandatorId . DS;
        array_map('unlink', glob($directory . DS . '*.*'));
        rmdir($directory);
        mkdir($directory, 0775, true);
        $i = 0;

        foreach ($postData['events'] as $event) {
            $method = $ycResponseFormats[$event['format']] ?: null;
            if ($method) {
                $postData = $this->exportData($method, $postData, $directory, $limit, $i, $event['storeViewId'],
                    $mandatorId, $event['lang']);
                $i++;
            }
        }

        return $postData;
    }

    /**
     * Returns shop base url based on shop id
     *
     * @param integer $shopId
     * @return string
     */
    public function getShopUrl($shopId)
    {
        /* @var $shop \Shopware\Models\Shop\Shop */
        $shop = Shopware()->Models()->find("Shopware\\Models\\Shop\\Shop", $shopId );
        $host = $shop->getMain() !== null ? $shop->getMain()->getHost() : $shop->getHost();
        $basePath = $shop->getMain() !== null ? $shop->getMain()->getBasePath() : $shop->getBasePath();
        $baseUrl = $shop->getBaseUrl();

        $shopUrl = 'http://' . $host;
        if(!empty($baseUrl)){
            $shopUrl .= $baseUrl;
        }elseif(!empty($basePath)){
            $shopUrl .= $basePath;
        }

        return $shopUrl;

    }

    /**
     * Exports data to file and returns $postData parameter
     *   with URLs to files
     *
     * @param string $method
     * @param array $postData
     * @param string $directory
     * @param integer $limit
     * @param integer $exportIndex
     * @param integer $storeId
     * @param string $mandatorId
     * @param lang
     * @return array $postData
     */
    private function exportData($method, $postData, $directory, $limit = 0, $exportIndex = 0, $storeId,
                                $mandatorId, $lang)
    {
        $baseUrl = Shopware()->Modules()->System()->sPathBanner;
        $fileUrl = $baseUrl . 'media/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';

        $offset = 0;
        $logNames = '';

        if ($method == 'Products') {
            $model = Manager::getResource('YoochooseArticles');
        } else if ($method == 'Categories') {
            $model = Manager::getResource('YoochooseCategories');
        } else {
            $model = Manager::getResource('YoochooseVendors');
        }

        $em = Shopware()->Models();
        $storeObject = $em->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('id' => $storeId));
        $categoryId = $storeObject->getCategory()->getId();
        $language = str_replace('-', '_', $lang);
        do {
            $results = $model->getList($offset, $limit, $categoryId, $storeId, $language);
            if (!empty($results['data'])) {
                $filename = $this->generateRandomString() . '.json';
                $file = $directory . $filename;
                file_put_contents($file, json_encode(array_values($results)));
                $fileSize = filesize($file);
                if ($fileSize >= self::YC_MAX_FILE_SIZE) {
                    @unlink($file);
                    $limit = --$limit;
                } else {
                    $postData['events'][$exportIndex]['uri'][] = $fileUrl . $filename;
                    $offset = $offset + $limit;
                    $logNames .= $filename . ', ';
                }
            }
        } while (!empty($results['data']));

        $logNames = $logNames ?: 'there are no files';
        YoochooseLogger::log(1, 'Export has finished for ' . $method . ' with file names : ' . $logNames, '', '', '');

        return $postData;
    }
}
