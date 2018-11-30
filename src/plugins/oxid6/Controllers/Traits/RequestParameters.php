<?php

namespace Yoochoose\Oxid\Controllers\Traits;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class RequestParameters
 * @package Yoochoose\Oxid\Controllers\Traits
 */
trait RequestParameters
{
    /**
     * Returns value of parameter stored in POST,GET.
     * For security reasons performed Config->checkParamSpecialChars().
     * use $raw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $name Name of parameter.
     * @param bool   $raw  Get unescaped parameter.
     *
     * @return mixed
     */
    public function getRequestParameter($name, $raw = false)
    {
        $request = Registry::get(\OxidEsales\Eshop\Core\Request::class);
        return $raw ? $request->getRequestParameter($name) : $request->getRequestEscapedParameter($name);
    }
}
