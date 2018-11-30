<?php

namespace Yoochoose\Oxid\Controllers\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ModuleConfiguration;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;
use Yoochoose\Oxid\Controllers\Traits\RequestParameters;
use Yoochoose\Oxid\Models\Front;
use Yoochoose\Oxid\Models\Yoochoose as YoochooseModel;

/**
 * Class Yoochoose
 * @package Yoochoose\Oxid\Controllers\Admin
 */
class Yoochoose extends ModuleConfiguration
{
    use RequestParameters;

    const SCRIPT_URL_REGEX = "/^(https:\/\/|http:\/\/|\/\/)?([a-zA-Z][\w\-]*)((\.[a-zA-Z][\w\-]*)*)(:\d+)?((\/[a-zA-Z][\w\-]*){0,2})(\/)?$/";

    /**
     * @return string
     * @throws \OxidEsales\EshopCommunity\Core\Exception\DatabaseException
     */
    public function render()
    {
        $this->getParams();

        return 'yoochoose.tpl';
    }

    /**
     * @throws \OxidEsales\EshopCommunity\Core\Exception\DatabaseException
     */
    public function getParams()
    {
        $conf = $this->getConfig();
        $view = new ViewConfig();


        $this->_aViewData['ycLink'] = $this->getYoochooseLink();
        $this->_aViewData['obj'] = $this->getYoochooseRegistration();

        $performanceOptions = [
            'YOOCHOOSE_SCRIPT_CDN'    => 0,
            'YOOCHOOSE_SCRIPT_SERVER' => 1,
        ];

        $this->_aViewData['performanceOptions'] = $performanceOptions;
        $vars = [
            'performance' => 'ycPerformance',
            'customerId'  => 'ycCustomerId',
            'licenseKey'  => 'ycLicenseKey',
            'pluginId'    => 'ycPluginId',
            'endpoint'    => 'ycEndpoint',
            'design'      => 'ycDesign',
            'itemType'    => 'ycItemType',
            'overwrite'   => 'ycOverwrite',
            'logSeverity' => 'ycLogSeverity',
        ];

        foreach ($vars as $key => $val) {
            $param = $conf->getConfigParam($val);
            if ($key == 'endpoint' && empty($param)) {
                $this->_aViewData[$key] = $conf->getShopUrl();
            } else if ($key == 'itemType' && empty($param)) {
                $this->_aViewData[$key] = 1;
            } else if ($key == 'design' && empty($param)) {
                $this->_aViewData[$key] = $view->getActiveTheme();
            } else {
                $this->_aViewData[$key] = $param;
            }
        }
    }

    /**
     * Performs validation of request parameters
     *
     * @return array
     */
    public function validation()
    {
        $result = [];

        $param = $this->getRequestParameter('confstrs');
        if (!is_numeric($param['ycItemType']) || $param['ycItemType'] < 0) {
            $result['ycItemType'] = 'Item type must be number and greater then 0';
        }

        if (trim($param['ycOverwrite']) && !preg_match(self::SCRIPT_URL_REGEX, $param['ycOverwrite'])) {
            $result['ycOverwrite'] = 'Overwrite endpoint url is not valid!';
        }

        return $result;
    }

    /**
     * Handles saving of form credentials
     */
    public function saveConfigForm()
    {
        try {
            $config = $this->getConfig();
            $this->_sModuleId = $this->getEditObjectId();
            $sModuleId = $this->_getModuleForConfigVars();
            $model = new YoochooseModel();
            $validation = $this->validation();
            $parameters = $this->getRequestParameter('confstrs');

            if (sizeof($validation) > 0) {
                foreach ($validation as $key => $value) {
                    $this->_aViewData['errors'][$key] = $value;
                }

                return;
            }

            foreach ($parameters as $key => $val) {
                $serializedValue = $this->_serializeConfVar('str', $key, trim($val));
                if ($key != 'ycCustomerId' && $key != 'ycLicenseKey') {
                    $config->saveShopConfVar('str', $key, $serializedValue, $config->getShopId(), $sModuleId);
                } else {
                    $shopId = Registry::getConfig()->getShopId();
                    $config->saveShopConfVar('str', $key, $serializedValue, $shopId, $sModuleId);
                }
            }


            $param = $this->getRequestParameter('confstrs') ?: [];
            if ($model->adminSystemConfigChangedSectionYoochoose($param)) {
                $this->_aViewData['ycSuccessMessage'] = true;
            } else {
                $this->_aViewData['ycErrorMessage'] = true;
            }
        } catch (\Exception $exc) {
            $this->_aViewData['ycErrorMessage'] = true;
            $this->_aViewData['ycErrorMessageText'] = $exc->getMessage();

        }
    }

    /**
     * @return false|string
     * @throws \OxidEsales\EshopCommunity\Core\Exception\DatabaseException
     */
    public function getYoochooseRegistration()
    {
        $data = [];
        $user = $this->getUser();
        $conf = $this->getConfig();
        $oLangObj = new Language();
        $languagesArray = $oLangObj->getLanguageArray();

        $model = new YoochooseModel();

        $data['account.firstName'] = $data['billing.firstName'] = $user->oxuser__oxfname->value;
        $data['account.lastName'] = $data['billing.lastName'] = $user->oxuser__oxlname->value;
        $data['account.email'] = $data['billing.email'] = $user->oxuser__oxusername->value;
        $data['booking.website'] = $conf->getShopUrl();
        $data['booking.lang'] = $languagesArray[$conf->getConfigParam('ycLanguage')]->abbr;
        $data['billing.countryCode'] = $model->getCountryCode()->fields['OXISOALPHA2'];
        $data['billing.company'] = $user->oxuser__oxcompany->value;
        $data['billing.zip'] = $user->oxuser__oxzip->value;
        $data['billing.city'] = $user->oxuser__oxcity->value;

        return json_encode($data);
    }

    public function getYoochooseLink()
    {
        $viewConfig = new ViewConfig();

        return Front::YOOCHOOSE_ADMIN_URL . 'login.html?product=oxid_Direct&lang=' . $viewConfig->getActLanguageAbbr();
    }
}
