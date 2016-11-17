<?php

namespace Yoochoose\Tracking\Logger;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Logger extends \Monolog\Logger
{

    const YOOCHOOSE_INFO_FORMAT = "\nURL: %s\nCODE: %s";

    const YOOCHOOSE_DEBUG_FORMAT = "\nURL: %s\nCODE: %s\nRESPONSE BODY: %s\nREQUEST HEADERS: %s";

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct($name, $handlers, ScopeConfigInterface $config)
    {
        parent::__construct($name, $handlers, []);
        $this->config = $config;
    }

    public function ycLog($url, $status, $response, $headers)
    {
        $severity = $this->config->getValue('yoochoose/general/log_id') ? Logger::DEBUG : Logger::INFO;
        $format = $severity == Logger::DEBUG ? self::YOOCHOOSE_DEBUG_FORMAT : self::YOOCHOOSE_INFO_FORMAT;
        $message = trim(sprintf($format, $url, $status, $response, $headers)) . "\n";

        $this->addRecord($severity, $message);
    }
}
