<?php

class Shopware_Controllers_Api_Ycarticles extends Shopware_Controllers_Api_Rest
{

    /**
     * @var Shopware\Components\Api\Resource\YoochooseArticles
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('YoochooseArticles');
    }

    public function indexAction()
    {
        try {
            $limit = $this->Request()->getParam('limit', 1000);
            $offset = $this->Request()->getParam('start', 0);
            $language = $this->Request()->getParam('language');

            $result = $this->resource->getList($offset, $limit, $language);

            $this->View()->assign($result);
            $this->View()->assign('success', true);
        } catch (Exception $e) {
            $this->View()->assign(array('message' => $e->getMessage()));
            $this->View()->assign('success', false);
        }
    }

}