<?php

namespace Shopware\Components\Api\Resource;

class YoochooseSubscribers extends Resource
{

    public function getList($offset, $limit)
    {
        $this->checkPrivilege('read');

        $builder = $this->getRepository()->createQueryBuilder('o1')
                ->leftJoin('o1.customer', 'o2')
                ->addSelect('o2');

        $builder->where('o1.isCustomer = true');
        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        $totalResult = $paginator->count();
        $subscribers = $paginator->getIterator()->getArrayCopy();

        $result = array();
        foreach ($subscribers as $subscriber) {
            $result[] = array(
                'id' => $subscriber['id'],
                'customerId' => $subscriber['customer']['id'],
                'newsletterGroupId' => $subscriber['groupId'],
            );
        }

        return array('data' => $result, 'total' => $totalResult);
    }

    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Newsletter\Address');
    }
}