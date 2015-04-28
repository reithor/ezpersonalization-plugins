<?php

class Yoochoose_JsTracking_Block_Field_Readonly extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        switch ($element->getId()) {
            case 'yoochoose_general_endpoint':
                if (!Mage::getStoreConfig("yoochoose/general/endpoint_overwrite")) {
                    $url = Mage::helper('core/url')->getHomeUrl();
                    $element->setValue($url);
                    $element->setReadonly('true');
                }
                
                break;
            case 'yoochoose_general_design':
                if (!Mage::getStoreConfig("yoochoose/general/endpoint_overwrite")) {
                    $theme = Mage::getSingleton('core/design_package')->getTheme('frontend');
                    $element->setValue($theme);
                    $element->setReadonly('true');
                }
                
                break;
            default:
                $element->setReadonly('true');
                break;
        }

        return parent::_getElementHtml($element);
    }

}
