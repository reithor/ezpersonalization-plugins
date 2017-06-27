<?php

namespace Yoochoose\Tracking\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Validator\Exception;
use Magento\Framework\Phrase;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\App\Emulation;
use Yoochoose\Tracking\Logger\Logger;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{

    const YC_MAX_FILE_SIZE = 52428800; // max file size size in bytes 50Mb

    const YC_DIRECTORY_NAME = 'yoochoose';

    /**
     * @var ObjectManagerInterface
     */
    private $om;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var File
     */
    private $io;

    /**
     * @var StoreManagerInterface
     */
    private $store;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @param Context $context
     * @param ObjectManagerInterface $om
     * @param Logger $logger
     * @param File $io
     * @param StoreManagerInterface $store
     * @param Request $request
     * @param Emulation $emulation
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $om,
        Logger $logger,
        File $io,
        StoreManagerInterface $store,
        Request $request,
        Emulation $emulation
    ) {
        parent::__construct($context);
        $this->om = $om;
        $this->logger = $logger;
        $this->io = $io;
        $this->store = $store;
        $this->request = $request;
        $this->config = $context->getScopeConfig();
        $this->emulation = $emulation;
    }

    public function getHttpPage($url, $body, $customerId, $licenceKey)
    {
        $bodyString = json_encode($body);
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ],
            CURLOPT_POSTFIELDS => $bodyString,
        ];

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        $headers = curl_getinfo($cURL, CURLINFO_HEADER_OUT);
        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        $this->logger->ycLog($url, $status, $response, $headers);

        $eno = curl_errno($cURL);

        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            curl_close($cURL);
            throw new Exception(new Phrase($msg));
        }

        curl_close($cURL);
        switch ($status) {
            case 200:
                break;
            case 409:
                if ($result['faultCode'] === 'pluginAlreadyExistsFault') {
                    break;
                }
            //it will will continue (fall-through) to the default intentionally
            default:
                $msg = $result['faultMessage'] . ' With status code: ' . $status;
                throw new Exception(new Phrase($msg));
        }

        return $result;
    }

    /**
     * export to files
     *
     * @param int $limit
     * @param array $storeData
     * @param int $transaction
     * @param int $mandatorId
     * @return array postData
     */
    public function export($storeData, $transaction, $limit, $mandatorId)
    {
        $this->logger->info('Export has started for all resources.');
        $storeIds = array();
        $formatsMap = [
            'MAGENTO2' => 'Products',
            'MAGENTO2_CATEGORIES' => 'Categories',
            'MAGENTO2_VENDORS' => 'Vendors',
        ];

        $postData = [
            'transaction' => $transaction,
            'events' => [],
        ];

        foreach ($formatsMap as $format => $method) {
            if (!empty($storeData)) {
                foreach ($storeData as $storeId => $language) {
                    $postData['events'][] = [
                        'action' => 'FULL',
                        'format' => $format,
                        'contentTypeId' => $this->config->getValue('yoochoose/general/item_type', 'store', $storeId),
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

        $dir = $this->om->get('\Magento\Framework\App\Filesystem\DirectoryList');
        $directory = $dir->getPath('pub') . '/media/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';
        $this->io->rmdirRecursive($directory);
        $this->io->mkdir($directory, 0775);
        $i = 0;

        foreach ($postData['events'] as $event) {
            $method = $formatsMap[$event['format']] ?: null;
            if ($method) {
                $postData = $this->exportData($method, $postData, $directory, $limit, $i++, $event['storeViewId'], $mandatorId);
            }
        }

        $this->logger->info('Export has finished for all resources.');
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
    private function exportData($method, $postData, $directory, $limit = 0, $exportIndex = 0, $storeId, $mandatorId)
    {
        $this->logger->info('Export has started for resource ' . $method . ' and store view ' . $storeId);
        $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $model = $this->om->get('\Yoochoose\Tracking\Model\Api\Yoochoose');

        $baseUrl = $this->store->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $fileUrl = $baseUrl . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';

        $method = 'get' . $method;
        $this->request->setParam('limit', $limit);
        $this->request->setParam('storeViewId', $storeId);
        $offset = 0;

        do {
            $this->request->setParam('offset', $offset);
            $this->logger->info('Bulk has started for ' . $method . ' and store view ' . $storeId .
                ' with limit ' . $limit . ' and offset ' . $offset);
            $results = $model->$method();
            if (!empty($results)) {
                $filename = $this->generateRandomString() . '.json';
                $file = $directory . $filename;
                $this->io->write($file, json_encode(array_values($results)));
                $fileSize = filesize($file);
                if ($fileSize >= self::YC_MAX_FILE_SIZE) {
                    $this->logger->info('Bulk has failed for ' . $method . ' and store view ' . $storeId .
                        'due to file size limit. Limit for export will be reduced.');
                    $this->io->rm($file);
                    $this->request->setParam('limit', --$limit);
                } else {
                    $postData['events'][$exportIndex]['uri'][] = $fileUrl . $filename;
                    $this->logger->info('Bulk has finished for ' . $method . ' and store view ' . $storeId .
                        ' with limit ' . $limit . ' and offset ' . $offset . 'and saved into file ' . $filename);
                    $offset = $offset + $limit;
                }
            }
        } while (!empty($results));

        $this->emulation->stopEnvironmentEmulation();
        $this->logger->info('Export has finished for resource ' . $method . ' and store view ' . $storeId);

        return $postData;
    }

}
