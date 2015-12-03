<?php

class Yoochoose extends Module_Config
{

    const SCRIPT_URL_REGEX = "/^(https:\/\/|http:\/\/|\/\/)?([a-zA-Z][\w\-]*)((\.[a-zA-Z][\w\-]*)*)(:\d+)?((\/[a-zA-Z][\w\-]*){0,2})(\/)?$/";

    public function __construct()
    {

        parent::__construct();
    }

    public function render()
    {

        $this->getParams();

        return "yoochoose.tpl";
    }

    public function getParams()
    {
        $conf = $this->getConfig();
        $view = oxNew('oxViewConfig');


        $this->_aViewData["ycLink"] = $this->getYoochooseLink();
        $this->_aViewData["obj"] = $this->getYoochooseRegistration();

        $performanceOptions = array(
            'YOOCHOOSE_SCRIPT_CDN' => 1,
            'YOOCHOOSE_SCRIPT_SERVER' => 0,
        );

        $this->_aViewData['performanceOptions'] = $performanceOptions;
        $vars = array(
            'performance' => 'ycPerformance',
            'customerId' => 'ycCustomerId',
            'licenseKey' => 'ycLicenseKey',
            'pluginId' => 'ycPluginId',
            'endpoint' => 'ycEndpoint',
            'design' => 'ycDesign',
            'itemType' => 'ycItemType',
            'overwrite' => 'ycOverwrite',
            'logSeverity' => 'ycLogSeverity',
        );

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

    public function validation()
    {
        $result = array();

        $param = $this->getConfig()->getRequestParameter('confstrs');
        if (!is_numeric($param['ycItemType']) || $param['ycItemType'] < 0) {
            $result['ycItemType'] = 'Item type must be number and greater then 0';
        }

        if (trim($param['ycOverwrite']) && !preg_match(self::SCRIPT_URL_REGEX, $param['ycOverwrite'])) {
            $result['ycOverwrite'] = 'Overwrite endpoint url is not valid!';
        }

        return $result;
    }

    public function saveConfigForm()
    {
        $config = $this->getConfig();
        $this->_sModuleId = $this->getEditObjectId();
        $sModuleId = $this->_getModuleForConfigVars();
        /** @var Yoochoosemodel $model */
        $model = oxNew('yoochoosemodel');
        $validation = $this->validation();
        $parameters = $config->getRequestParameter('confstrs');

        if (sizeof($validation) > 0) {
            foreach ($validation as $key => $value) {
                $this->_aViewData['errors'][$key] = $value;
            }

            return;
        } else {
            foreach ($parameters as $key => $val) {
                if ($key != 'ycCustomerId' && $key != 'ycLicenseKey') {
                    $config->saveShopConfVar('str', $key, $this->_serializeConfVar('str', $key, trim($val)), $config->getShopId(), $sModuleId);
                }
            }
        }

        if ($model->adminSystemConfigChangedSectionYoochoose()) {
            foreach (oxRegistry::getConfig()->getShopIds() as $shopId) {
                $lk = 'ycLicenseKey';
                $cid = 'ycCustomerId';
                $config->saveShopConfVar('str', $lk, $this->_serializeConfVar('str', $lk, trim($parameters[$lk])), $shopId, $sModuleId);
                $config->saveShopConfVar('str', $cid, $this->_serializeConfVar('str', $cid, trim($parameters[$cid])), $shopId, $sModuleId);
            }

            $this->_aViewData['ycSuccessMessage'] = true;
        } else {
            $this->_aViewData['ycErrorMessage'] = true;
        }
    }

    public function getYoochooseRegistration()
    {
        $data = array();
        $user = $this->getUser();
        $conf = $this->getConfig();
        $oLangObj = oxNew('oxLang');

        $model = oxNew('yoochoosemodel');

        $data['account.firstName'] = $data['billing.firstName'] = $user->oxuser__oxfname->value;
        $data['account.lastName'] = $data['billing.lastName'] = $user->oxuser__oxlname->value;
        $data['account.email'] = $data['billing.email'] = $user->oxuser__oxusername->value;
        $data['booking.website'] = $conf->getShopUrl();
        $data['booking.lang'] = $oLangObj->getLanguageArray()[$conf->getConfigParam('ycLanguage')]->abbr;
        $data['billing.countryCode'] = $model->getCountryCode()->fields['OXISOALPHA2'];
        $data['billing.company'] = $user->oxuser__oxcompany->value;
        $data['billing.zip'] = $user->oxuser__oxzip->value;
        $data['billing.city'] = $user->oxuser__oxcity->value;
        return json_encode($data);
    }

    public function getYoochooseLink()
    {
        $oLangObj = oxNew('oxviewconfig');
        return Ycfrontmodel::YOOCHOOSE_ADMIN_URL . 'login.html?product=oxid_Direct&lang=' . $oLangObj->getActLanguageAbbr();
    }

}
