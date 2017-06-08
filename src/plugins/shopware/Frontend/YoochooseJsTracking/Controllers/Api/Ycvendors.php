<?php

/**
 * Class Shopware_Controllers_Api_Ycvendors
 */
class Shopware_Controllers_Api_Ycvendors extends Shopware_Controllers_Api_Rest
{

    /**
     * @var Shopware\Components\Api\Resource\YoochooseVendors
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('YoochooseVendors');
    }

    public function indexAction()
    {
        try {
            $limit = $this->Request()->getParam('limit', 1000);
            $offset = $this->Request()->getParam('start', 0);

            $result = $this->resource->getList($offset, $limit);

            if (empty($result['data'])) {
                $this->Response()->setHttpResponseCode(204);
            } else {
                $this->View()->assign($result);
                $this->View()->assign('success', true);
            }
        } catch (Exception $e) {
            $this->Response()->setHttpResponseCode(400);
            $this->View()->assign(array('message' => $e->getMessage()));
            $this->View()->assign('success', false);
        }
    }
}