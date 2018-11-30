<?php

namespace Yoochoose\Oxid\Helpers;

use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Core\UtilsFile;
use Yoochoose\Oxid\Models\Export;
use Yoochoose\Oxid\Models\Yoochoose;

/**
 * Class Helper
 * @package Yoochoose\Oxid\Helpers
 */
class Helper extends Base
{
    const YC_MAX_FILE_SIZE = 52428800; // max file size size in bytes 50Mb

    const YC_DIRECTORY_NAME = 'yoochoose';

    /**
     * Exports resources to files that are available for download
     *
     * @param int   $limit
     * @param array $shopData
     * @param int   $transaction
     * @param int   $mandatorId
     *
     * @return array postData
     */
    public function export($shopData, $transaction, $limit, $mandatorId)
    {
        $conf = new Config();
        $shopIds = [];
        $formatsMap = [
            'OXID2' => 'Products',
            'OXID2_CATEGORIES' => 'Categories',
            'OXID2_VENDORS' => 'Vendors',
        ];

        $postData = [
            'transaction' => $transaction,
            'events' => [],
        ];

        foreach ($formatsMap as $format => $method) {
            if (!empty($shopData)) {
                foreach ($shopData as $shop) {
                    $postData['events'][] = [
                        'action' => 'FULL',
                        'format' => $format,
                        'contentTypeId' => $conf->getShopConfVar('ycItemType', $shop['shopId']),
                        'shopViewId' => $shop['shopId'],
                        'lang' => $shop['language'],
                        'credentials' => [
                            'login' => null,
                            'password' => null,
                        ],
                        'uri' => [],
                    ];
                    $shopIds [$method][] = $shop['shopId'];

                }
            }
        }
        $file = new UtilsFile();
        $basePath =  getShopBasePath();
        $basePath = str_replace('\\', '/', $basePath);
        $directory = $basePath . 'out/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';
        $file->deleteDir($directory);
        mkdir($directory, 0775, true);
        $i = 0;

        foreach ($postData['events'] as $event) {
            $method = $formatsMap[$event['format']] ? $formatsMap[$event['format']] : null;
            if ($method) {
                $postData = $this->exportData($method, $postData, $directory, $limit, $i, $event['shopViewId'], $mandatorId, $event['lang']);
            }

            $i++;
        }

        return $postData;
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
     * Exports data to file and returns $postData parameter with URLs to files
     *
     * @param string $entity
     * @param array  $postData
     * @param string $directory
     * @param int    $limit
     * @param int    $exportIndex
     * @param int    $shopId
     * @param string $mandatorId
     * @param string $lang
     *
     * @return array $postData
     */
    private function exportData($entity, $postData, $directory, $limit, $exportIndex, $shopId, $mandatorId, $lang)
    {
        $model = new Export();
        $oxViewConfig = new ViewConfig();
        $baseUrl = $oxViewConfig->getBaseDir();
        $fileUrl = $baseUrl . 'out/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';

        $method = 'get' . $entity;
        $offset = 0;
        $logNames = '';

        $logger = new Yoochoose();
        do {
            $logger->log('Exporting ' . $entity, $shopId, "offset: $offset, limit: $limit", $lang);

            $results = $model->$method($shopId, $offset, $limit, $lang);
            if (!empty($results)) {
                $filename = $this->generateRandomString() . '.json';
                $file = $directory . $filename;
                file_put_contents($file, json_encode(array_values($results)));
                $fileSize = filesize($file);
                if ($fileSize >= self::YC_MAX_FILE_SIZE) {
                    unlink($file);
                    $limit = (int)($limit / 2);

                    $logger->log('Reducing limit size because of max file size.', $fileSize,
                        "offset: $offset, limit: $limit", self::YC_MAX_FILE_SIZE);
                } else {
                    $postData['events'][$exportIndex]['uri'][] = $fileUrl . $filename;
                    $offset = $offset + $limit;
                    $logNames .= $filename . ', ';
                }
            }
        } while(!empty($results));

        $logNames = $logNames ?: 'there are no files';
        $logger->log('Export has finished for ' . $entity . ' with file names : ' . $logNames, 500, '', '');

        return $postData;
    }
}
