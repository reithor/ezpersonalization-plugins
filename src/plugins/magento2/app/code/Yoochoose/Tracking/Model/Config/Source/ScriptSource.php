<?php

namespace Yoochoose\Tracking\Model\Config\Source;

class ScriptSource
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Load scripts from the Amazon content delivery network (CDN)')],
            ['value' => 0, 'label' => __('Load scripts directly from Yoochoose server')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            1 => __('Load scripts from the Amazon content delivery network (CDN)'),
            0 => __('Load scripts directly from Yoochoose server'),
        ];
    }
}
