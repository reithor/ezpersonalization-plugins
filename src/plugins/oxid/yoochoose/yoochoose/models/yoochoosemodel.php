<?php

class Yoochoosemodel extends oxUBase
{

    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    public function getCountryCode()
    {

        $user = $this->getUser();
        $countryId = $user->oxuser__oxcountryid->value;
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $rs = $oDb->execute("SELECT OXISOALPHA2 FROM oxcountry where OXID='$countryId'");
        return $rs;
    }


    public function adminSystemConfigChangedSectionYoochoose()
    {
        $view = oxNew('oxViewConfig');
        $param = $this->getConfig()->getRequestParameter('confstrs');
        $customerId = trim($param['ycCustomerId']);
        $licenseKey = trim($param['ycLicenseKey']);


        if (!$customerId && !$licenseKey) {
            return false;
        }

        try {
            $body = array(
                'base' => array(
                    'type' => 'OXID',
                    'pluginId' => $customerId,
                    'endpoint' => $param['ycEndpoint'],
                    'appSecret' => md5($licenseKey),
                ),
                'frontend' => array(
                    'design' => $view->getActiveTheme(),
                ),
                'search' => array(
                    'design' => $view->getActiveTheme(),
                ),
            );

            $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/update?createIfNeeded=true&fallbackDesign=true';

            $this->getHttpPage($url, $body, $customerId, $licenseKey);
            oxRegistry::getUtils()->writeToLog("\n" . date('c') . 'Registration finished successfully!', 'yoochoose.log');

        } catch (Exception $ex) {
            $message = "\n" . date('c') . ' Plugin registration failed: ' . $ex->getMessage();
            oxRegistry::getUtils()->writeToLog($message, 'yoochoose.log');
            return false;
        }

        return true;
    }

    public function getHttpPage($url, $body, $customerId, $licenceKey)
    {
        $bodyString = json_encode($body);
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ),
            CURLOPT_POSTFIELDS => $bodyString
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        $eno = curl_errno($cURL);
        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            throw new Exception($msg);
        }

        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
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

}