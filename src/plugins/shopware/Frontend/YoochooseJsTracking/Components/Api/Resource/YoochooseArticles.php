<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\YoochooseHelper;
/**
 * Class YoochooseArticles
 * @package Shopware\Components\Api\Resource
 */
class YoochooseArticles extends Resource
{

    private $version = 4;

    private $loadedCategories = array();

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
     * Retrieves Article Model Repository
     *
     * @return \Shopware\Models\Category\Repository
     */
    public function getCategoryRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Category\Category');
    }

    /**
     * @return array
     */
    public function getArticleThumbnailSizes()
    {
        /**
         * @var \Shopware\Models\Media\Album $album
         */
        $album = $this->getManager()->getRepository('Shopware\Models\Media\Album')->find(-1);

        return $album->getSettings()->getThumbnailSize();
    }

    /**
     * Retrieves the list of articles
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $category
     * @param integer $storeId
     * @param string $language
     * @return array
     * @throws \Exception
     */
    public function getList($offset, $limit, $category, $storeId, $language)
    {
        $helper = new YoochooseHelper();
        $this->version = (int)Shopware()->Config()->version;
        $base = $helper->getShopUrl($storeId) . '/';
        $mediaPath = Shopware()->Modules()->System()->sPathArticleImg;
        $thumbnailSizes = $this->getArticleThumbnailSizes();

        $builder = $this->getRepository()->createQueryBuilder('article');
        $builder->select(array(
            'article',
            'mainDetail',
            'mainDetailPrices',
            'PARTIAL supplier.{id, name}',
            'PARTIAL categories.{id, path}',
        ))
            ->leftJoin('article.supplier', 'supplier')
            ->leftJoin('article.mainDetail', 'mainDetail')
            ->leftJoin('article.categories', 'categories')
            ->leftJoin('mainDetail.prices', 'mainDetailPrices')
            ->where('article.active = 1')
            ->setParameter(':path', '%|' . $category . '|%')
            ->andWhere('categories.path LIKE :path')
            ->orWhere('categories.id = ' . $category);

        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());
        $paginator = $this->getManager()->createPaginator($query);
        $totalResult = $paginator->count();
        $articles = $paginator->getIterator()->getArrayCopy();

        if (!empty($language)) {
            $locale = $this->findEntityByConditions('Shopware\Models\Shop\Locale', array(array('locale' => $language)));
            // for shopware 5 locale needs to be mapped to shop ID
            if ($this->version === 5) {
                $locale = $this->findEntityByConditions('Shopware\Models\Shop\Shop', array(array('locale' => $locale)));
            }

            if (!$locale) {
                throw new \Exception("Language code '$language' is not recognized, use locales in format as e.g. en_US.");
            }

            foreach ($articles as &$article) {
                $article = $this->translateArticle($article, $locale->getId());
            }
        }

        $db = Shopware()->Db();
        $sql = 'SELECT path FROM s_core_rewrite_urls WHERE org_path =? AND main=? AND subshopID=?';
        $result = array();
        foreach ($articles as $art) {
            $articleId = $art['id'];
            $path = $db->fetchOne($sql, array('sViewport=detail&sArticle=' . $articleId, 1,  $storeId));
            $path = strtolower($path);
            $categories = $this->getArticleCategories($articleId);
            $item = array(
                'id' => $articleId,
                'supplierId' => $art['supplierId'],
                'name' => $art['name'],
                'description' => $art['description'],
                'active' => $art['active'],
                'categories' => $this->getCategoryList(array_column($categories, 'id')),
                'tags' => !empty($art['keywords']) ? explode(',', $art['keywords']) : array(),
                'price' => null,
                'url' => $base . $path,
                'manufacturer' => $art['supplier']['name'] ? $art['supplier']['name'] : null,
                'storeViewId' => $storeId,
            );

            //Search for minimum price
            foreach ($art['mainDetail']['prices'] as $artPrice) {
                if ($item['price'] === null || $item['price'] > $artPrice['price']) {
                    $item['price'] = $artPrice['price'];
                }
            }

            $item['price'] = round($item['price'], 2);

            $images = $this->getArticleImages($articleId);
            if (!empty($images)) {
                $imagePath = $images[0]['path'];
                $imageExt = $images[0]['extension'];
                $item['image'] = $mediaPath . $imagePath . '.' . $imageExt;
                $imageInfo = getimagesize($item['image']);
                if (is_array($imageInfo)) {
                    $item['image_size'] = $imageInfo[0] . 'x' . $imageInfo[1];
                }

                $item['thumbnails'] = array();
                foreach ($thumbnailSizes as $ts) {
                    $item['thumbnails'][] = array(
                        'image' => $mediaPath . 'thumbnail/' . $imagePath . "_$ts." . $imageExt,
                        'image_size' => $ts,
                    );
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
     * @param integer $articleId
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
     * @param integer $articleId
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
     * @param $builder
     * @return array
     */
    private function getFullResult(\Shopware\Components\Model\QueryBuilder $builder)
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
     * @param integer $localeId
     * @return array
     */
    protected function translateArticle(array $data, $localeId)
    {
        $this->getTranslationResource()->setResultMode(self::HYDRATE_ARRAY);
        $translation = $this->getSingleTranslation('article', $localeId, $data['id']);

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
     * @param string $type
     * @param integer $localeId
     * @param string $key
     * @return array
     */
    protected function getSingleTranslation($type, $localeId, $key)
    {
        $property = 'translation.' . ($this->version === 4 ? 'localeId' : 'shopId');
        $translation = $this->getTranslationResource()->getList(0, 1, array(
            array('property' => 'translation.type', 'value' => $type),
            array('property' => 'translation.key', 'value' => $key),
            array('property' => $property, 'value' => $localeId),
        ));

        return $translation['data'][0];
    }

    /**
     * Returns category list
     *
     * @param array $catIds
     * @return array
     */
    protected function getCategoryList($catIds)
    {
        $categories = array();
        foreach ($catIds as $catId) {
            if (!array_key_exists($catId, $this->loadedCategories)) {
                $paths = $this->getCategoryRepository()->getPathById($catId, 'name');
                $this->loadedCategories[$catId] = implode('/', array_slice($paths, 1));
            }

            $categories[] = $this->loadedCategories[$catId];
        }

        return $categories;
    }
}
