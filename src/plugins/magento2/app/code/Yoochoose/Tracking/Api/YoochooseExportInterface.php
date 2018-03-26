<?php

namespace Yoochoose\Tracking\Api;


interface YoochooseExportInterface
{

    /**
     * Returns if export have started
     *
     * @api
     * @return \Yoochoose\Tracking\Api\Data\ResponseInterface
     */
    public function startExport();

}