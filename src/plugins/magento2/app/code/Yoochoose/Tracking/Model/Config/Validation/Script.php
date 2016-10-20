<?php

namespace Yoochoose\Tracking\Model\Config\Validation;

use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\Exception;

class Script extends Value
{
    const SCRIPT_URL_REGEX = "/^(https:\/\/|http:\/\/|\/\/)?([a-zA-Z][\w\-]*)((\.[a-zA-Z][\w\-]*)*)(:\d+)?((\/[a-zA-Z][\w\-]*){0,2})(\/)?$/";

    public function save()
    {
        if ($this->getValue() && !preg_match(self::SCRIPT_URL_REGEX, $this->getValue())) {
            throw new Exception(new Phrase('Unsupported URL type: (' . $this->getValue() . ')'));
        }

        parent::save();
    }
}
