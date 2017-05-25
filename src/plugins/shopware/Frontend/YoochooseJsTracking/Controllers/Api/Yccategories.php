<?php

/**
 * Class Shopware_Controllers_Api_Yccategories
 */
class Shopware_Controllers_Api_Yccategories extends Shopware_Controllers_Api_Rest
{

    /**
     * @var Shopware\Components\Api\Resource\YoochooseCategories
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('YoochooseCategories');
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
