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
                try {
                    $article = $helper->sGetArticleById($id);
                    if (!$article) {
                        continue;
                    }

                    $image = $article['image']['source'];
                    $result[] = array(
                        'id' => $article['articleID'],
                        'title' => $article['articleName'],
                        'link' => $article['linkDetailsRewrited'],
                        'image' => $image ? $image : $article['image']['src']['original'],
                        'price' => smarty_modifier_currency($article['price']),
                    );
                } catch (\Exception $exc) {
                    error_log($exc->getMessage() . " ($id)\n", 3, Shopware()->DocPath() . '/logs/yoochoose.log');
                }
            }
        }

        header('Content-Type: application/json');
        exit(json_encode($result));
    }

}
