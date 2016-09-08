<?php

namespace Yoochoose\Tracking\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Validator\Exception;
use Magento\Framework\Phrase;
use Yoochoose\Tracking\Logger\Logger;

class Data extends AbstractHelper
{

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Context $context, Logger $logger)
    {
        parent::__construct($context);
        $this->logger = $logger;
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
}
