<?php

namespace Yoochoose\Oxid\Controllers\Api;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use Yoochoose\Oxid\Controllers\Traits\RequestParameters;

/**
 * Class BaseApi
 * @package Yoochoose\Oxid\Controllers\Api
 */
class BaseApi extends FrontendController
{
    use RequestParameters;

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

        if ($_GET['cl'] == 'yoochooseexport') {
            $licenceKey = null;
            $shopIds = $conf->getShopIds();
            $mandator = $this->getRequestParameter('mandator');
            $limit = $this->getRequestParameter('limit');
            $webHook = $this->getRequestParameter('webHook');
            if (isset($mandator) && isset($limit) && isset($webHook)) {
                foreach ($shopIds as $shopId) {
                    $this->setShopId((string)$shopId);
                    if ($mandator == $conf->getShopConfVar('ycCustomerId', $shopId, 'module:yoochoose')) {
                        $licenceKey = $conf->getShopConfVar('ycLicenseKey', $shopId, 'module:yoochoose');
                        break;
                    }
                }
            } else {
                $this->sendResponse([], "Limit, mandator and webHook parameters must be set.", 400);
            }
        } else {
            $licenceKey = $conf->getShopConfVar('ycLicenseKey');
        }

        $headers = apache_request_headers();
        $appSecret = [];
        $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        $appSecret[] = str_replace('Bearer ', '', $authorization);

        $ycAuth = isset($headers['YCAuth']) ? $headers['YCAuth'] : '';
        $appSecret[] = str_replace('Bearer ', '', $ycAuth);

        $appSecret[] = $this->getRequestParameter('ycauth');

        if (in_array(md5($licenceKey), $appSecret, true)) {
            $this->limit = $this->getRequestParameter('limit');
            $this->offset = $this->getRequestParameter('offset');
            $this->language = $this->getRequestParameter('lang');
            $this->shopId = $this->getRequestParameter('shop');
            $this->mandator = $this->getRequestParameter('mandator');
            $this->webHook = $this->getRequestParameter('webHook');
            $this->transaction = $this->getRequestParameter('transaction');

            if ($this->shopId && !in_array($this->shopId, Registry::getConfig()->getShopIds())) {
                $this->sendResponse([], "Shop with id ($this->shopId) not found.", 400);
            } if (!$this->shopId) {
                $this->shopId = $conf->getBaseShopId();
            }

            $conf->setShopId($this->shopId);

            $lang = new Language();
            $verifiedLang = $lang->validateLanguage($this->language);
            if ($this->language != -1 && $verifiedLang != $this->language) {
                $this->sendResponse([], "Language with id ($this->language) not found.", 400);
            }

            $lang->setBaseLanguage($verifiedLang);
        } else {
            $this->sendResponse([], 'Authentication failed', 401);
        }
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
        return $this->limit ? $this->limit : 500;
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

    /**
     * Helper method for sending API response
     *
     * @param array  $data
     * @param string $message
     * @param int    $code
     */
    protected function sendResponse(array $data = [], $message = '', $code = 200)
    {
        $result = [];
        header('Content-Type: application/json');

        if ($code === 200 && empty($data)) {
            $result['success'] = true;
            http_response_code(204);
        } else {
            if ($code === 200) {
                $result['success'] = true;
                $result['data'] = $data;
            } else {
                http_response_code($code);
                $result['success'] = false;
                $result['message'] = $message;
            }
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
}
