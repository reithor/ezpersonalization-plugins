<?php

namespace Shopware\Components;

class YoochooseLogger
{
    /**
     * Name of the log file
     */
    const YOOCHOOSE_LOG_FILE = 'yoochoose.log';

    const YOOCHOOSE_INFO_FORMAT = "%s [%s]:\nURL: %s\nCODE: %s\n\n";

    const YOOCHOOSE_DEBUG_FORMAT = "%s [%s]:\nURL: %s\nCODE: %s\nRESPONSE BODY: %s\nREQUEST HEADERS: %s\n";

    /**
     * @param string $severity
     * @param string $url
     * @param int $code
     * @param string $body
     * @param string $headers
     */
    public static function log($severity, $url, $code, $body, $headers)
    {
        $ts = date(DATE_ISO8601);
        $type = ($severity == 2 ? 'DEBUG' : 'INFO');
        $format = ($severity == 2 ? self::YOOCHOOSE_DEBUG_FORMAT : self::YOOCHOOSE_INFO_FORMAT);
        $message = sprintf($format, $ts, $type , $url, $code, $body, $headers);

        error_log($message, 3, Shopware()->DocPath() . '/var/log/' . self::YOOCHOOSE_LOG_FILE);
    }
}
