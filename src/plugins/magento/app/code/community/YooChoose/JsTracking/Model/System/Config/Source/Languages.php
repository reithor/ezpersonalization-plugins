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
        return Mage::app()->getLocale()->getOptionLocales();
    }

}