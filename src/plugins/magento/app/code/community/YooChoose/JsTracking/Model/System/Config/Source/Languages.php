<?php

class YooChoose_JsTracking_Model_System_Config_Source_Languages
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getStoreConfig("yoochoose/general/language_country") ? Mage::app()->getLocale()->getOptionLocales() : 
            Zend_Locale::getTranslationList('language', Mage::app()->getLocale()->getLocaleCode());
    }

}