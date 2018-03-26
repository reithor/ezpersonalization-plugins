<?php

namespace Yoochoose\Tracking\Model\Config\Source;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ReadOnly extends Field
{

    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setReadonly('true');
        switch ($element->getId()) {
            case 'yoochoose_general_endpoint':
                if (!$element->getValue()) {
                    $element->setValue($this->_storeManager->getStore()->getBaseUrl());
                }

                break;
            case 'yoochoose_general_design':
                if (!$element->getValue()) {
                    $themeId = $this->_design->getConfigurationDesignTheme('frontend');
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    /** @var \Magento\Theme\Model\Theme $theme */
                    $theme = $objectManager->get('Magento\Theme\Model\Theme')->load($themeId);
                    $element->setValue($theme->getThemeTitle());
                }

                break;
        }

        return parent::_getElementHtml($element);
    }
}
