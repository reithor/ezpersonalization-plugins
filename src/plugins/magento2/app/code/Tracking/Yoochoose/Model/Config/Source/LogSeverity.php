<?php

namespace Yoochoose\Tracking\Model\Config\Source;

class LogSeverity
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Debug')],
            ['value' => 0, 'label' => __('Info')],
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
            0 => __('Info'),
            1 => __('Debug'),
        ];
    }
}
