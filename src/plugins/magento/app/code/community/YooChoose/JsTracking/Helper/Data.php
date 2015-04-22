<?php


class YooChoose_JsTracking_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function _getHttpPage($url, $customerId, $licenceKey)
    {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_FAILONERROR => TRUE
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $result = curl_exec($cURL);

        $eno = curl_errno($cURL);
        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            throw new Exception($msg);
        }

        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            $msg = 'Error requesting [' . $url . ']. Status: ' . $status . '.';
            throw new Exception($msg);
        }

        curl_close($cURL);

        return json_decode($result, true);
    }

}
