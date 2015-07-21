<?php

require_once 'Enlight/Template/Plugins/modifier.currency.php';

class Shopware_Controllers_Frontend_Yoochoose extends Enlight_Controller_Action
{

    public function indexAction()
    {
        $result = array();
        $productIds = $this->Request()->getParam('productIds', false);

        if ($productIds) {
            /* @var $helper sArticles */
            $helper = Shopware()->Modules()->Articles();

            foreach (explode(',', $productIds) as $id) {
                $articleNoImages = $helper->sGetArticleById($id);
                if (!$articleNoImages) {
                    continue;
                }

                $article = $helper->sGetConfiguratorImage($articleNoImages);

                $result[] = array(
                    'id' => $article['articleID'],
                    'title' => $article['articleName'],
                    'link' => $article['linkDetailsRewrited'],
                    'image' => $article['image']['src']['original'],
                    'price' => smarty_modifier_currency($article['price']),
                );
            }
        }

        header('Content-Type: application/json');
        exit(json_encode($result));
    }

}
