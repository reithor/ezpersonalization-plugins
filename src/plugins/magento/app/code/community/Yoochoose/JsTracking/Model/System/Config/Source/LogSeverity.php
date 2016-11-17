<?php

class Yoochoose_JsTracking_Model_System_Config_Source_LogSeverity
{

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => 'Info',
                'value' => 1,
            ),
            array(
                'label' => 'Debug',
                'value' => 2,
            ),
        );
    }
}

