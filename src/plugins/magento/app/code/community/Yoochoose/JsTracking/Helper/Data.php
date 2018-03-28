<?php


class Yoochoose_JsTracking_Helper_Data extends Mage_Core_Helper_Abstract
{

    const YC_MAX_FILE_SIZE = 52428800; // max file size size in bytes 50Mb

    const YC_DIRECTORY_NAME = 'yoochoose';


    public function _getHttpPage($url, $body, $customerId, $licenceKey)
    {
        $bodyString = json_encode($body);
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => 0,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ),
            CURLOPT_POSTFIELDS     => $bodyString,
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);

        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        $headers = curl_getinfo($cURL, CURLINFO_HEADER_OUT);
        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        Mage::helper('yoochoose_jstracking/logger')->log($url, $status, $response, $headers);

        $eno = curl_errno($cURL);
        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            throw new Exception($msg);
        }

        switch ($status) {
            case 200:
                break;
            case 409:
                if ($result['faultCode'] === 'pluginAlreadyExistsFault') {
                    break;
                }
            default:
                $msg = $result['faultMessage'] . ' With status code: ' . $status;
                throw new Exception($msg);
        }

        curl_close($cURL);

        return $result;
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
        $ycResponseFormats = [
            'MAGENTO' => 'Products',
            'MAGENTO_CATEGORIES' => 'Categories',
            'MAGENTO_VENDORS' => 'Vendors'
        ];

        $postData = [
            'transaction' => $transaction,
            'events' => []
        ];

        foreach ($ycResponseFormats as $format => $method){
            if (!empty($storeData)) {
                foreach ($storeData as $storeId => $language) {
                    $postData['events'][] = [
                        'action' => 'FULL',
                        'format' => $format,
                        'contentTypeId' => Mage::getStoreConfig('yoochoose/general/itemtypeid', $storeId),
                        'storeViewId' => $storeId,
                        'lang' => $language,
                        'credentials' => [
                            'login' => null,
                            'password' => null
                        ],
                        'uri' => []
                    ];
                    $storeIds [$method][] = $storeId;
                }
            }
        }

        $directory = Mage::getBaseDir('media') . '/' . self::YC_DIRECTORY_NAME . '/' . $mandatorId . '/';

        $file = new Varien_Io_File();

        $file->rmdirRecursive($directory);
        $file->mkdir($directory, 0775);
        $i = 0;

        foreach ($postData['events'] as $event) {
            $method = $ycResponseFormats[$event['format']] ?: null;
            if ($method) {
                $postData = $this->exportData($method, $postData, $directory, $limit, $i, $event['storeViewId'], $mandatorId);
                $i++;
            }
        }

        return $postData;

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
     * Exports data to file and returns $postData parameter
     *   with URLs to files
     *
     * @param string $method
     * @param array $postData
     * @param string $directory
     * @param integer $limit
     * @param integer $exportIndex
     * @param integer $storeId
     * @return array $postData
     */
    private function exportData($method, $postData, $directory, $limit = 0, $exportIndex = 0, $storeId, $mandatorId){

        $io = new Varien_Io_File();

        $baseUrl =  Mage::app()->getStore($storeId)->getBaseUrl('media');
        $fileUrl = $baseUrl . self::YC_DIRECTORY_NAME. '/' . $mandatorId . '/';

        $method = 'get' . $method;
        Mage::app()->getRequest()->setParam('limit', $limit);
        Mage::app()->getRequest()->setParam('storeViewId', $storeId);
        $offset = 0;
        $logNames = '';
        $emulationModel = Mage::getSingleton('core/app_emulation');

        do {
            Mage::app()->getRequest()->setParam('offset', $offset);

            // start emulation for store view
            $initialEnvironmentInfo = $emulationModel->startEnvironmentEmulation($storeId);

            // execute export action
            $results = Mage::getModel('yoochoose_jstracking/YoochooseExport')->$method();

            // stop emulation for store view
            $emulationModel->stopEnvironmentEmulation($initialEnvironmentInfo);

            if (!empty($results)){
                $filename = $this->generateRandomString() . '.json';
                $file = $directory . $filename;
                $io->write($file, json_encode($results));
                $fileSize = filesize($file);
                if ($fileSize >= self::YC_MAX_FILE_SIZE){
                    $io->rm($file);
                    $this->getRequest()->setPost('limit', --$limit);
                } else {
                    $postData['events'][$exportIndex]['uri'][] = $fileUrl . $filename;
                    $offset = $offset + $limit;
                    $logNames .= $filename . ', ';
                }
            }
        } while (!empty($results));

        $logNames = $logNames ?: 'there are no files';
        Mage::log('Export has finished for ' . $method . ' with file names : ' . $logNames, Zend_Log::INFO, 'yoochoose.log');

        return $postData;
    }

}
