<?php

class Yoochoosehelper extends oxUBase
{
    const YC_MAX_FILE_SIZE = 52428800; // max file size size in bytes 50Mb

    const YC_DIRECTORY_NAME = 'yoochoose';
    /**
     * export to files
     *
     * @param int $limit
     * @param array $storeData
     * @param int $transaction
     * @param int $mandatorId
     * @return string postData
     */
    static function export($storeData, $transaction, $limit, $mandatorId)
    {
        $conf = oxNew('oxConfig');
        $storeIds = array();
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
            if (!empty($storeData)) {
                foreach ($storeData as $language => $storeId) {
                    $postData['events'][] = [
                        'action' => 'FULL',
                        'format' => $format,
                        'contentTypeId' => $conf->getShopConfVar('ycItemType', $storeId),
                        'storeViewId' => $storeId,
                        'lang' => $language,
                        'credentials' => [
                            'login' => null,
                            'password' => null,
                        ],
                        'uri' => [],
                    ];
                    $storeIds [$method][] = $storeId;
                }
            }
        }
        $file = oxNew('oxUtilsFile');
        $basePath =  getShopBasePath();
        $basePath = str_replace('\\', '/', $basePath);
        $directory = $basePath . 'out/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';
        $file->deleteDir($directory);
        mkdir($directory, 0775, true);
        $i = 0;

        foreach ($postData['events'] as $event) {
            $method = $formatsMap[$event['format']] ? $formatsMap[$event['format']] : null;
            if ($method) {
                $postData = self::exportData($method, $postData, $directory, $limit, $i, $event['storeViewId'], $mandatorId, $event['lang']);
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
     * @return array $postData
     */
    private function exportData($method, $postData, $directory, $limit = 0, $exportIndex = 0, $storeId, $mandatorId, $lang)
    {

        /* @var Ycexportmodel $model */
        $model = oxNew('ycexportmodel');
        $oxConfig = oxNew('oxConfig');

        $baseUrl = $oxConfig->getShopMainUrl();
        $fileUrl = $baseUrl . 'out/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';

        $method = 'get' . $method;
        $offset = 0;
        $logNames = '';

        do {
            $results = $model->$method($storeId, $offset, $limit, $lang);
            if (!empty($results)) {
                $filename = self::generateRandomString() . '.json';
                $file = $directory . $filename;
                file_put_contents($file, json_encode(array_values($results)));
                $fileSize = filesize($file);
                if ($fileSize >= self::YC_MAX_FILE_SIZE) {
                    delete($file);
                    $limit = --$limit;
                } else {
                    $postData['events'][$exportIndex]['uri'][] = $fileUrl . $filename;
                    $offset = $offset + $limit;
                    $logNames .= $filename . ', ';
                }
            }
        } while (!empty($results));

        $logNames = $logNames ?: 'there are no files';
        oxNew('yoochoosemodel')->log('Export has finished for ' . $method . ' with file names : ' . $logNames);

        return $postData;
    }
}