<?php

namespace Yoochoose\Tracking\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface YoochooseInterface
{

    /**
     * Returns list of store views with language codes
     *
     * @api
     * @return mixed
     */
    public function getStoreViews();

    /**
     * Returns list of subscribers
     *
     * @api
     * @return mixed
     */
    public function getSubscribers();

    /**
     * Returns list of categories that are visible on frontend
     *
     * @api
     * @return mixed
     */
    public function getCategories();

    /**
     * Returns list of products that are visible on frontend
     *
     * @api
     * @return mixed
     */
    public function getProducts();

    /**
     * Returns list of manufacturers that are visible on frontend
     *
     * @api
     * @return mixed
     */
    public function getVendors();
    
}
