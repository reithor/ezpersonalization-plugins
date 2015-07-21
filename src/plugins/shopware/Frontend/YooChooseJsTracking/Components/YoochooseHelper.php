<?php

namespace Shopware\Components;

class YoochooseHelper
{

    const YOOCHOOSE_CDN_SCRIPT = '//event.yoochoose.net/cdn';
    const AMAZON_CDN_SCRIPT = '//cdn.yoochoose.net';

    /**
     * Fetches yoochoose config data
     *
     * @param string $name
     * @param mixed $default
     */
    public static function getYoochooseConfig($name, $default = '')
    {
        $element = Shopware()->Models()->getRepository('Shopware\Models\Yoochoose\Yoochoose')
                ->findOneBy(array('name' => $name));

        return $element ? $element->getValue() : $default;
    }

    public static function getTrackingScript($type = '.js')
    {
        $customerId = self::getYoochooseConfig('customerId');
        $scriptOverwrite = self::getYoochooseConfig('scriptUrl');
        $pluginId = self::getYoochooseConfig('pluginId');
        $plugin = $pluginId ? '/' . $pluginId : '';
        $suffix = "/v1/{$customerId}{$plugin}/tracking";

        if ($scriptOverwrite) {
            $scriptOverwrite = (!preg_match('/^(http|\/\/)/', $scriptOverwrite) ? '//' : '') . $scriptOverwrite;
            $scriptUrl = preg_replace('(^https?:)', '', $scriptOverwrite);
        } else {
            $scriptUrl = self::getYoochooseConfig('performance') == 1 ? self::AMAZON_CDN_SCRIPT : self::YOOCHOOSE_CDN_SCRIPT;
        }

        return rtrim($scriptUrl, '/') . $suffix . $type;
    }
}
