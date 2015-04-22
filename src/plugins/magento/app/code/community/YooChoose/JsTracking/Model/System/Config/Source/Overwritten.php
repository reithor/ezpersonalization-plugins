<?php

class Yoochoose_JsTracking_Model_System_Config_Source_Overwritten {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 1, 'label'=>'Yoochoose recommendation only'),
            array('value' => 2, 'label'=>'Manually entered and yoochoose recommendation'),
            array('value' => 3, 'label'=>'Manually entered recommendation only'),
            array('value' => 0, 'label'=>'Disabled')
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(){
        return array(
            1 => 'Yoochoose recommendation only',
            2 => 'Manually entered and yoochoose recommendation',
            3 => 'Manually entered recommendation only',
            0 => 'Disabled'
        );
    }

}
