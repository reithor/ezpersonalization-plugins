<?php

namespace Yoochoose\Oxid\Models;

use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\ResultSet;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;

/**
 * Class Yoochoose
 * @package Yoochoose\Oxid\Models
 */
class Yoochoose extends Base
{
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    /**
     * Name of the log file
     */
    const YOOCHOOSE_LOG_FILE = 'yoochoose.log';

    const YOOCHOOSE_INFO_FORMAT = "%s [%s]:\nURL: %s\nCODE: %s\n\n";

    const YOOCHOOSE_DEBUG_FORMAT = "%s [%s]:\nURL: %s\nCODE: %s\nRESPONSE BODY: %s\nREQUEST HEADERS: %s\n\n";

    /**
     * @param string $url
     * @param int    $code
     * @param string $body
     * @param string $headers
     */
    public static function log($url, $code, $body, $headers)
    {
        $severity = Registry::getConfig()->getConfigParam('ycLogSeverity');
        $ts = date(DATE_ISO8601);
        $type = ($severity == 2 ? 'DEBUG' : 'INFO');
        $format = ($severity == 2 ? self::YOOCHOOSE_DEBUG_FORMAT : self::YOOCHOOSE_INFO_FORMAT);
        $message = sprintf($format, $ts, $type, $url, $code, $body, $headers);

        $filename = Registry::getConfig()->getLogsDir() . self::YOOCHOOSE_LOG_FILE;
        $dirName = dirname($filename);
        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true);
        }

        if (($log = fopen($filename, 'a+')) !== false) {
            fwrite($log, $message);
            fclose($log);
        }
    }

    /**
     * Returns country code
     *
     * @return ResultSet
     * @throws DatabaseException
     */
    public function getCountryCode()
    {
        $user = $this->getUser();
        $countryId = $user->oxuser__oxcountryid->value;
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        /** @var ResultSet $dbRow */
        $dbRow = $oDb->select("SELECT OXISOALPHA2 FROM oxcountry where OXID='$countryId'");

        return $dbRow;
    }

    /**
     * @param array $param
     *
     * @return bool
     * @throws \Exception
     */
    public function adminSystemConfigChangedSectionYoochoose(array $param)
    {
        $view = oxNew('oxViewConfig');
        $customerId = trim($param['ycCustomerId']);
        $licenseKey = trim($param['ycLicenseKey']);


        if (!$customerId && !$licenseKey) {
            return false;
        }

        $body = [
            'base'     => [
                'type'      => 'OXID2',
                'pluginId'  => $param['ycPluginId'],
                'endpoint'  => $param['ycEndpoint'],
                'appKey'    => $customerId,
                'appSecret' => md5($licenseKey),
            ],
            'frontend' => [
                'design' => $view->getActiveTheme(),
            ],
            'search'   => [
                'design' => $view->getActiveTheme(),
            ],
        ];

        $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/update?createIfNeeded=true&fallbackDesign=true';
        $this->registerYoochooseMandator($url, $body, $customerId, $licenseKey);

        return true;
    }

    /**
     * Makes an API call to Yoochoose to register mandator
     *
     * @param string $url
     * @param array  $body
     * @param string $customerId
     * @param string $licenceKey
     *
     * @return mixed
     * @throws \Exception
     */
    public function registerYoochooseMandator($url, $body, $customerId, $licenceKey)
    {
        $bodyString = json_encode($body);
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => 0,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_FOLLOWLOCATION => true,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ],
            CURLOPT_POSTFIELDS     => $bodyString,
        ];

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        $headers = curl_getinfo($cURL, CURLINFO_HEADER_OUT);
        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        self::log($url,$status, $response, $headers);

        $eno = curl_errno($cURL);

        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            curl_close($cURL);
            throw new \Exception($msg);
        }

        curl_close($cURL);
        switch ($status) {
            case 200:
                break;
            case 409:
                if ($result['faultCode'] === 'pluginAlreadyExistsFault') {
                    break;
                }

                $msg = $result['faultMessage'] . ' With status code: ' . $status;
                throw new \Exception($msg);
                break;
            default:
                $msg = $result['faultMessage'] . ' With status code: ' . $status;
                throw new \Exception($msg);
        }

        return $result;
    }
}
