<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Manager;
/**
 * Class YoochooseVendors
 * @package Shopware\Components\Api\Resource
 */
class YoochooseVendors extends Resource
{

    /**
     * Retrieves Article Model Repository
     *
     * @return \Shopware\Components\Model\ModelRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Article\Supplier');
    }

    /**
     * Retrieves the list of vendors
     *
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getList($offset, $limit)
    {
        $builder = $this->getRepository()->createQueryBuilder('o1');

        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        $totalResult = $paginator->count();
        $categories = $paginator->getIterator()->getArrayCopy();

        $result = array();
        foreach ($categories as $category) {
            $result[] = array(
                'id' => $category['id'],
                'name' => $category['name'],
            );
        }

        return array('data' => $result, 'total' => $totalResult);
    }
}