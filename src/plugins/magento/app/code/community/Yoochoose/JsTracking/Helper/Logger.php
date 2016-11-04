<?php

class Yoochoose_JsTracking_Helper_Logger extends Mage_Core_Helper_Abstract
{

    /**
     * Name of the log file
     */
    const YOOCHOOSE_LOG_FILE = 'yoochoose.log';

    const YOOCHOOSE_INFO_FORMAT = "URL: %s\nCODE: %s";

    const YOOCHOOSE_DEBUG_FORMAT = "URL: %s\nCODE: %s\nRESPONSE BODY: %s\nREQUEST HEADERS: %s";

    /**
     * @param string $url
     * @param int $code
     * @param string $body
     * @param string $headers
     */
    public function log($url, $code, $body, $headers)
    {
        $severity = $this->getLogSeverity();
        $message = sprintf(
            $severity == Zend_Log::DEBUG ? self::YOOCHOOSE_DEBUG_FORMAT : self::YOOCHOOSE_INFO_FORMAT
            , $url, $code, $body, $headers);

        Mage::log($message, $severity, self::YOOCHOOSE_LOG_FILE);
    }

    /**
     * @return int
     */
    protected function getLogSeverity()
    {
        if (Mage::getStoreConfig('yoochoose/general/log_severity') == 2) {
            return Zend_Log::DEBUG;
        }

        return Zend_Log::INFO;
    }

}
