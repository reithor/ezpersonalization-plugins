<?php

namespace Shopware\Components\Api\Resource;

/**
 * Class YoochooseArticles
 * @package Shopware\Components\Api\Resource
 */
class YoochooseArticles extends Resource
{

    /**
     * Retrieves Article Model Repository
     *
     * @return \Shopware\Components\Model\ModelRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Article\Article');
    }

    /**
     * Retrieves the list of articles
     *
     * @param $offset
     * @param $limit
     * @param $language
     * @return array
     * @throws \Exception
     * @throws \Shopware\Components\Api\Exception\PrivilegeException
     */
    public function getList($offset, $limit, $language)
    {
        $this->checkPrivilege('read');
        $base = Shopware()->Modules()->Core()->sRewriteLink();
        $imagePath = Shopware()->Modules()->System()->sPathArticleImg;

        $builder = $this->getRepository()->createQueryBuilder('article');
        $builder->select(array(
                'article',
                'mainDetail',
                'mainDetailPrices',
            ))
            ->leftJoin('article.mainDetail', 'mainDetail')
            ->leftJoin('mainDetail.prices', 'mainDetailPrices')
            ->where('article.active = 1');

        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        $totalResult = $paginator->count();
        $articles = $paginator->getIterator()->getArrayCopy();

        if (!empty($language)) {
            $locale = $this->findEntityByConditions('Shopware\Models\Shop\Locale', array(
                array('id' => $language),
                array('locale' => $language)
            ));
            if (!$locale) {
                throw new \Exception("Language code '$language' is not recognized, use locales in format as e.g. en_US.");
            }

            foreach ($articles as &$article) {
                $article = $this->translateArticle(
                    $article,
                    $locale
                );
            }
        }

        $db = Shopware()->Db();
        $sql = 'SELECT path FROM s_core_rewrite_urls where org_path =?';
        $result = array();
        foreach ($articles as $article) {
            $articleId = $article['id'];
            $item = array(
                'id' => $articleId,
                'name' => $article['name'],
                'description' => $article['description'],
                'active' => $article['active'],
                'categories' => array(),
                'tags' => !empty($article['keywords']) ? explode(',', $article['keywords']) : array(),
                'price' => null,
                'url' => $base . $db->fetchOne($sql, array('sViewport=detail&sArticle=' . $articleId)),
            );

            //Search for minimum price
            foreach ($article['mainDetail']['prices'] as $artPrice) {
                if ($item['price'] === null || $item['price'] > $artPrice['price']) {
                    $item['price'] = $artPrice['price'];
                }
            }

            $categories = $this->getArticleCategories($articleId);
            foreach ($categories as $category) {
                $item['categories'][] = $db->fetchOne($sql, array('sViewport=cat&sCategory=' . $category['id']));
            }

            $images = $this->getArticleImages($articleId);
            if (!empty($images)) {
                $item['image'] = $imagePath . $images[0]['path'] . '.' . $images[0]['extension'];
                $imageInfo = getimagesize($item['image']);
                if (is_array($imageInfo)) {
                    $item['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
                }
            }

            $result[] = $item;
        }

        return array('data' => $result, 'total' => $totalResult);
    }

    /**
     * Selects all images of the main variant of the passed article id.
     * The images are sorted by their position value.
     *
     * @param $articleId
     * @return array
     */
    protected function getArticleImages($articleId)
    {
        $builder = $this->getManager()->createQueryBuilder();
        $builder->select(array('images'))
            ->from('Shopware\Models\Article\Image', 'images')
            ->innerJoin('images.article', 'article')
            ->where('article.id = :articleId')
            ->andWhere('images.main = 1')
            ->orderBy('images.position', 'ASC')
            ->andWhere('images.parentId IS NULL')
            ->setParameters(array('articleId' => $articleId));

        return $this->getFullResult($builder);
    }

    /**
     * Helper function which selects all categories of the passed
     * article id.
     * This function returns only the directly assigned categories.
     * To prevent a big data, this function selects only the category name and id.
     *
     * @param $articleId
     * @return array
     */
    protected function getArticleCategories($articleId)
    {
        $builder = $this->getManager()->createQueryBuilder();
        $builder->select(array('categories'))
            ->from('Shopware\Models\Category\Category', 'categories', 'categories.id')
            ->innerJoin('categories.articles', 'articles')
            ->where('articles.id = :articleId')
            ->setParameter('articleId', $articleId);

        return $this->getFullResult($builder);
    }

    /**
     * Helper function to prevent duplicate source code
     * to get the full query builder result for the current resource result mode
     * using the query paginator.
     *
     * @param QueryBuilder $builder
     * @return array
     */
    private function getFullResult(QueryBuilder $builder)
    {
        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        return $paginator->getIterator()->getArrayCopy();
    }

    /**
     * Translate the whole article array.
     *
     * @param array $data
     * @param Locale $locale
     * @return array
     */
    protected function translateArticle(array $data, Locale $locale)
    {
        $this->getTranslationResource()->setResultMode(
            self::HYDRATE_ARRAY
        );
        $translation = $this->getSingleTranslation(
            'article',
            $locale->getId(),
            $data['id']
        );

        if (!empty($translation)) {
            $data = $this->mergeTranslation($data, $translation['data']);

            if ($data['mainDetail']) {
                $data['mainDetail'] = $this->mergeTranslation($data['mainDetail'], $translation['data']);
            }
        }

        return $data;
    }

    /**
     * @return Translation
     */
    protected function getTranslationResource()
    {
        return $this->getResource('Translation');
    }

    /**
     * Helper function which merges the translated data into the already
     * existing data object. This function merges only values, which already
     * exist in the original data array.
     *
     * @param $data
     * @param $translation
     * @return array
     */
    protected function mergeTranslation($data, $translation)
    {
        $data = array_merge(
            $data,
            array_intersect_key($translation, $data)
        );

        return $data;
    }

    /**
     * Helper function to get a single translation.
     * @param $type
     * @param $localeId
     * @param $key
     * @return array
     */
    protected function getSingleTranslation($type, $localeId, $key)
    {
        $translation = $this->getTranslationResource()->getList(0, 1, array(
            array('property' => 'translation.type', 'value' => $type),
            array('property' => 'translation.key', 'value' => $key),
            array('property' => 'translation.localeId', 'value' => $localeId),
        ));

        return $translation['data'][0];
    }

}
