<?php

namespace Yoochoose\Tracking\Model\Config\Source;

class SearchOption
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('No')],
            ['value' => 0, 'label' => __('Yes')],
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
            1 => __('No'),
            0 => __('Yes'),
        ];
    }
}