<?php

namespace Shopware\Components\Api\Resource;

/**
 * Class YoochooseStorelocals
 * @package Shopware\Components\Api\Resource
 */
class YoochooseStorelocals extends Resource
{

    /**
     * Retrieves the list of store locals
     *
     * @param $offset
     * @param $limit
     * @return array
     * @throws \Shopware\Components\Api\Exception\PrivilegeException
     */
    public function getList($offset, $limit)
    {
        $this->checkPrivilege('read');

        $builder = $this->getRepository()->createQueryBuilder('o1')
                ->leftJoin('o1.locale', 'o2')
                ->addSelect('o2');

        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        $totalResult = $paginator->count();
        $shops = $paginator->getIterator()->getArrayCopy();

        foreach ($shops as &$shop) {
            $shop['language'] = $shop['locale']['language'];
            $shop['localeCode'] = $shop['locale']['locale'];
            unset($shop['locale']);
        }

        return array('data' => $shops, 'total' => $totalResult);
    }

    /**
     * Retrieves Shop Model Repository
     *
     * @return \Shopware\Components\Model\ModelRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Shop\Shop');
    }
}