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
     * export to files
     *
     * @param int $limit
     * @return string postData
     */
    public function export($limit)
    {
        $ycResponseFormats = [
            'MAGENTO2', 'MAGENTO2_CATEGORIES', 'MAGENTO2_VENDORS'
        ];

        $postData = [
            'transaction' => null,
            'events' => []
        ];

        foreach ($ycResponseFormats as $format){
            $postData['events'][] = [
                'action' => 'FULL',
                'format' => $format,
                'contentType' => '1',
                'credentials' => [
                    'login' => null,
                    'password' => null
                ],
                'uri' => []
            ];
        }

        $directory = Mage::getBaseDir('media') . '/' . self::YC_DIRECTORY_NAME . '/';

        $file = new Varien_Io_File();

        $file->rmdirRecursive($directory);
        $file->mkdir($directory, 0775);

        $postData = $this->exportData('Products', $postData, $directory, $limit, 0);
        $postData = $this->exportData('Categories', $postData, $directory, $limit, 1);
        $postData = $this->exportData('Vendors', $postData, $directory, $limit, 2);

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
     * @return array $postData
     */
    private function exportData($method, $postData, $directory, $limit = 0, $exportIndex = 0){

        $io = new Varien_Io_File();
        $baseUrl =  Mage::getBaseUrl('media');
        $fileUrl = $baseUrl . self::YC_DIRECTORY_NAME . '/';

        $method = 'get' . $method;
        Mage::app()->getRequest()->setParam('limit', $limit);
        $offset = 0;

        do {
            Mage::app()->getRequest()->setParam('offset', $offset);
            $results = Mage::getModel('yoochoose_jstracking/YoochooseExport')->$method();
            if (!empty($results)){
                $filename = $this->generateRandomString();
                $file = $directory . $filename;
                $io->write($file, json_encode($results));
                $fileSize = filesize($file);
                if ($fileSize >= self::YC_MAX_FILE_SIZE){
                    $io->rm($file);
                    $this->getRequest()->setPost('limit', --$limit);
                } else {
                    $postData['events'][$exportIndex]['uri'][] = $fileUrl . $filename;
                    $offset = $offset + $limit;
                }
            }
        } while (!empty($results));

        return $postData;
    }

}
