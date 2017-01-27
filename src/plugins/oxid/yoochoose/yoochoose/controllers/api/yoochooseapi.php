<?php

class Yoochooseapi extends oxUBase
{

    /**
     * @var string
     */
    protected $language;
    /**
     * @var int
     */
    protected $limit;
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var string
     */
    protected $shopId;

    /**
     * @var string
     */
    protected $mandator;

    /**
     * @var string
     */
    protected $webHook;

    /**
     * @var string
     */
    protected $transaction;

    /**
     * Retrieves all request params and authenticates user
     */
    public function init()
    {
        $conf = $this->getConfig();

        $licenceKey = $conf->getConfigParam('ycLicenseKey');
        $test = apache_request_headers();
        $appSecret = $test['Authentication'];

        if (md5($licenceKey) == $appSecret) {
            $this->limit = $conf->getRequestParameter('limit');
            $this->offset = $conf->getRequestParameter('offset');
            $this->language = $conf->getRequestParameter('lang');
            $this->shopId = $conf->getRequestParameter('shop');
            $this->mandator = $conf->getRequestParameter('mandator');
            $this->webHook = $conf->getRequestParameter('webHookUrl');
            $this->transaction = $conf->getRequestParameter('transaction');

            if ($_GET['cl'] == 'yoochooseexport') {
                if (!isset($this->limit) && !isset($this->mandator) && !isset($this->webHook)) {
                    $this->sendResponse(array(), "Limit, mandator and webHook parameters must be set.", 400);
                }
            }

            if ($this->shopId && !in_array($this->shopId, oxRegistry::getConfig()->getShopIds())) {
                $this->sendResponse(array(), "Shop with id ($this->shopId) not found.", 400);
            } else if (!$this->shopId) {
                $this->shopId = $conf->getBaseShopId();
            }

            $conf->setShopId($this->shopId);

            /** @var oxLang $lang */
            $lang = oxnew('oxlang');
            $verifiedLang = $lang->validateLanguage($this->language);
            if ($this->language != -1 && $verifiedLang != $this->language) {
                $this->sendResponse(array(), "Language with id ($this->language) not found.", 400);
            }

            $lang->setBaseLanguage($verifiedLang);
        } else {
            $this->sendResponse(array(), 'Authentication failed', 401);
        }
    }

    /**
     * Helper method for sending API response
     *
     * @param $data
     * @param string $message
     * @param int $code
     */
    protected function sendResponse($data = array(), $message = '', $code = 200)
    {
        $result = array();
        header('Content-Type: application/json');

        if ($code === 200 && empty($data)) {
            $result['success'] = true;
            http_response_code(204);
        } else if ($code === 200) {
            $result['success'] = true;
            $result['data'] = $data;
        } else {
            http_response_code($code);
            $result['success'] = false;
            $result['message'] = $message;
        }

        echo json_encode($result);
        die;
    }

    /**
     * Returns sql limit string or empty string if limit parameters are not set
     *
     * @return string
     */
    protected function getLimitSQL()
    {
        if ($this->limit && is_numeric($this->limit)) {
            $this->offset = $this->offset && is_numeric($this->offset) ? $this->offset : 0;
            return " LIMIT {$this->offset}, {$this->limit} ";
        }

        return '';
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param string $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return string
     */
    public function getMandator()
    {
        return $this->mandator;
    }

    /**
     * @param $mandator
     */
    public function setMandator($mandator)
    {
        $this->mandator = $mandator;
    }

    /**
     * @return string
     */
    public function getWebHook()
    {
        return $this->webHook;
    }

    /**
     * @param $webHook
     */
    public function setWebHook($webHook)
    {
        $this->webHook = $webHook;
    }

    /**
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

}
