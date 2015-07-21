<?php

namespace Shopware\Components\Api\Resource;

class YoochooseCategories extends Resource
{

    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Category\Category');
    }

    public function getList($offset, $limit)
    {
        $this->checkPrivilege('read');
        $db = Shopware()->Db();
        $base = Shopware()->Modules()->Core()->sRewriteLink();
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
            $sql = 'SELECT path FROM s_core_rewrite_urls where org_path =?';
            $path = $db->fetchOne($sql, array('sViewport=cat&sCategory=' . $category['id']));
            $result[] = array(
                'id' => $category['id'],
                'name' => $category['name'],
                'parentId' => $category['parentId'],
                'pathIds' => $category['path'],
                'path' => $path,
                'link' => $base . '/' . $path,
            );
        }

        return array('data' => $result, 'total' => $totalResult);
    }

}
