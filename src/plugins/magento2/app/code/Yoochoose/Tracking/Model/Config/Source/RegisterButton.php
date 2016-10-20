<?php

namespace Yoochoose\Tracking\Model\Config\Source;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class RegisterButton extends Field
{

    const YOOCHOOSE_ADMIN_URL = "//admin.yoochoose.net/login.html?product=magento_Direct&lang=";

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $language = $this->_scopeConfig->getValue('general/locale/code', 'store');

        $url = self::YOOCHOOSE_ADMIN_URL . substr($language, 0, strpos($language, '_'));
        $data = $this->getYoochooseRegistration();
        $element->setType('button');
        $element->setData('value', 'Register new Yoochoose account');
        $element->setData('onclick', "yc_register('$url', $data);");

        return $element->getElementHtml();
    }

    public function getYoochooseRegistration()
    {
        $data = [];

        return json_encode($data);
    }
}
