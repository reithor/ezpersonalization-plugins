<?php

namespace Yoochoose\Tracking\Api;


interface YoochooseInfoInterface
{

    /**
     * Returns if export have started
     *
     * @api
     * @return \Yoochoose\Tracking\Api\Data\ResponseInterface
     */
    public function getInfo();

}