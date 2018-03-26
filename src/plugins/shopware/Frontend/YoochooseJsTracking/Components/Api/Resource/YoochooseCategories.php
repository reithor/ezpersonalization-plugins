<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\YoochooseHelper;
/**
 * Class YoochooseCategories
 * @package Shopware\Components\Api\Resource
 */
class YoochooseCategories extends Resource
{

    /**
     * Retrieves Article Model Repository
     *
     * @return \Shopware\Components\Model\ModelRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Category\Category');
    }

    /**
     * Retrieves the list of categories
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $category
     * @param integer $storeId
     * @return array
     */
    public function getList($offset, $limit, $category, $storeId)
    {
        $helper = new YoochooseHelper();
        $db = Shopware()->Db();
        $base = $helper->getShopUrl($storeId) . '/';
        $builder = $this->getRepository()->createQueryBuilder('o1')
            ->setParameter(':path', '%|' . $category . '|%')
            ->where('o1.path LIKE :path')
            ->orWhere('o1.id = ' . $category);

        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        $totalResult = $paginator->count();
        $categories = $paginator->getIterator()->getArrayCopy();

        $result = array();
        foreach ($categories as $category) {
            $sql = 'SELECT path FROM s_core_rewrite_urls WHERE org_path =? AND main=? AND subshopID=?';
            $path = $db->fetchOne($sql, array('sViewport=cat&sCategory=' . $category['id'], 1,  $storeId));
            $path = strtolower($path);
            $result[] = array(
                'id' => $category['id'],
                'name' => $category['name'],
                'parentId' => $category['parentId'],
                'pathIds' => $category['path'],
                'path' => $path,
                'link' => $base . $path,
                'storeViewId' => $storeId,
            );
        }

        return array('data' => $result, 'total' => $totalResult);
    }
}
