<?php

class YooChoose_JsTracking_Block_Field_Readonly extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if ($element->getId() === 'yoochoose_general_endpoint') {
            $url = Mage::getStoreConfig("yoochoose/general/endpoint");
            if (!$url) {
                $url = Mage::helper('core/url')->getHomeUrl();
            }
            $element->setValue($url);
        }
        
        if (!Mage::getStoreConfig("yoochoose/general/endpoint_overwrite")) {
            $element->setDisabled('disabled');
        }
        
        return parent::_getElementHtml($element);
    }

}
