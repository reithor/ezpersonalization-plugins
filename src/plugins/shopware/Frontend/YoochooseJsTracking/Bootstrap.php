<?php

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Shopware\Components\YoochooseHelper;

/**
 * Class Shopware_Plugins_Frontend_YoochooseJsTracking_Bootstrap
 *
 * SEE SWAG MANUAL FOR MORE INFO TO FORMAT OF THIS FILE:
 * http://community.shopware.com/Shopware-4-Grundlagen-der-Plugin-Entwicklung_detail_971_867.html
 *
 * @package Yoochoose Plugin
 * @version 2.1.0
 *
 */
class Shopware_Plugins_Frontend_YoochooseJsTracking_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    /**
     * This derived method is executed each time if this plugin will will be installed
     *
     * @return array
     */
    public function install()
    {
        $this->createDatabase();
        $this->createMenu();
        $this->registerControllers();
        $this->registerEvents();

        return array(
            'success' => true,
            'invalidateCache' => array('frontend', 'backend', 'config'),
        );
    }

    public function update($version)
    {
        // Register namespace and annotations for custom model
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\Models\Yoochoose\Yoochoose'),
        );

        try {
            $sm = $em->getConnection()->getSchemaManager();

            $tables = array($sm->listTableDetails('s_yoochoose_config'));
            $fromSchema = new Schema($tables, array(), $sm->createSchemaConfig(), array());
            $toSchema = $tool->getSchemaFromMetadata($classes);

            $comparator = new Comparator();
            $schemaDiff = $comparator->compare($fromSchema, $toSchema);

            $changes = $schemaDiff->toSaveSql($em->getConnection()->getDatabasePlatform());
            $changes[] = 'UPDATE s_yoochoose_config SET shop_id = 1 WHERE shop_id IS NULL';
            foreach ($changes as $sql) {
                $em->getConnection()->executeQuery($sql);
            }
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            //ignore
        }

        $this->registerControllers();

        return true;
    }

    /**
     * Remove attributes from table
     *
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function uninstall()
    {
        $this->removeDatabase();

        /* @var $rootNode  \Shopware\Models\Menu\Menu */
        $menuItem = $this->Menu()->findOneBy(array('label' => 'Yoochoose'));
        Shopware()->Models()->remove($menuItem);
        Shopware()->Models()->flush();

        return true;
    }

    /**
     * Returns the version of plugin as string.
     *
     * @return string
     */
    public function getVersion()
    {
        return '2.5.3';
    }

    /**
     * Returns the label of the plugin as string
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Yoochooose Recommendations and Search';
    }

    /**
     * Returns name of our company
     *
     * @return string
     */
    public function getAuthor()
    {
        return 'Yoochoose GmbH';
    }

    /**
     * Information about this plugin
     *
     * @return array
     */
    public function getInfo()
    {
        $img = base64_encode(file_get_contents(dirname(__FILE__) . '/logo.png'));

        return array(
            'version' => $this->getVersion(),
            'author' => $this->getAuthor(),
            'copyright' => $this->getAuthor(),
            'label' => $this->getLabel(),
            'support' => 'support@yoochoose.com',
            'link' => 'http://www.yoochoose.com/',
            'description' =>
                '<p><img style="float: right" src="data:image/png;base64,' . $img . '" />With our integrated personalization solution consisting of product recommendations,' .
                ' an intelligent store search, and personalized communication marketing, we help you to understand your customers and to respond to preferences, interests, ' .
                'and needs in real time.<br>' .
                '-	Create a personalized shopping experience for your customers across all channels<br>' .
                '-	Optimize conversion rates<br>' .
                '-	Improve customer retention<br>' .
                '-	Increase your sales<br>' .
                '-	Make visitors into buyers and buyers into regular customers</p>',
        );
    }

    /**
     * Called when the FrontendPostDispatch Event is triggered
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onFrontendPostDispatch(Enlight_Event_EventArgs $args)
    {
        $json = array('trackLogout' => false);

        /* @var $view Enlight_View_Default */
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->Path() . 'Views/');

        /* @var $request Enlight_Controller_Request_RequestHttp */
        $request = $args->getRequest();
        $actionName = $request->getActionName();
        $controllerName = $request->getControllerName();

        $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/header.tpl');

        if ($controllerName === 'account') {
            if ($actionName === 'logout' || $actionName === 'ajax_logout') {
                $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/ajax_logout.tpl');
                $json['trackLogout'] = true;
            }
        } else if ($controllerName === 'checkout' && $actionName === 'finish') {
            // needed for buy event
            $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/finish.tpl');
        } else if ($controllerName === 'listing') {
            // needed for basket event
            $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/box_article.tpl');
        }

        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $shop = $this->getCurrentShop();
        $json['lang'] = $this->getShopLanguage($shop);
        $json['trackid'] = isset($userData['additional']['user']) ? $userData['additional']['user']['id'] : 0;
        $json['currentPage'] = $this->getCurrentPage($controllerName, $actionName);
        $json['boxes'] = $this->getRecommendationBoxes($json['currentPage']);

        $view->assign('ycTrackingScriptUrl', preg_replace('(^https?:)', '', YoochooseHelper::getTrackingScript('.js', $shop)));
        $view->assign('ycTrackingCssUrl', preg_replace('(^https?:)', '', YoochooseHelper::getTrackingScript('.css', $shop)));
        $view->assign('ycConfigObject', json_encode($json));
    }

    /**
     * Add template path
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function onGetControllerPathBackendYoochoose(Enlight_Event_EventArgs $args)
    {
        $this->Application()->Template()->addTemplateDir($this->Path() . 'Views/', '');
    }

    /**
     * Add template path
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function onBackendPostDispatch(Enlight_Event_EventArgs $args)
    {
        $this->registerCustomModels();
        /**@var $view Enlight_View_Default */
        $view = $args->getSubject()->View();
        // Add template directory
        $view->addTemplateDir($this->Path() . 'Views/');
        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/plugin/base/header.tpl');
            $view->assign('ycAdminUrl', YoochooseHelper::YOOCHOOSE_ADMIN_URL);
        }
    }

    public function onGetFrontendController()
    {
        $this->registerCustomModels();
    }

    /**
     * Registration of custom controller
     */
    private function registerControllers()
    {
        $this->registerController('Frontend', 'Yoochoose');
        $this->registerController('Frontend', 'Yctrigger');
        $this->registerController('Backend', 'Yoochoose');
        $this->registerController('Frontend', 'Ycexport');
        $this->registerController('Frontend', 'Ycinfo');
    }

    private function createDatabase()
    {
        // Register namespace and annotations for custom model
        $this->registerCustomModels();
        // Create schema for custom model
        $this->executeSchemaAction('createSchema');
    }

    private function removeDatabase()
    {
        // Unregister namespace and annotations for custom model
        $this->registerCustomModels();
        // Remove schema for custom model
        $this->executeSchemaAction('dropSchema');
    }

    private function registerEvents()
    {
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Backend_Index', 'onBackendPostDispatch');

        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend', 'onFrontendPostDispatch');
        $this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Backend_Yoochoose', 'onGetControllerPathBackendYoochoose');
        $this->subscribeEvent('Enlight_Controller_Action_PreDispatch_Frontend', 'onGetFrontendController');
    }

    /**
     * Create a back-end menu item
     */
    private function createMenu()
    {
        $this->createMenuItem(array(
            'label' => 'Yoochoose',
            'class' => 'yoochoose_image',
            'active' => 1,
            'parent' => null,
            'controller' => 'Yoochoose',
            'action' => 'index',
        ));
    }

    /**
     * Decides what page is being loaded
     *
     * @param string $controller
     * @param string $action
     * @return boolean|string
     */
    private function getCurrentPage($controller, $action)
    {
        if ($action === 'index') {
            switch ($controller) {
                case 'index':
                    return 'home';
                case 'detail':
                    return 'product';
                case 'listing':
                    return 'category';
            }
        } else if ($controller === 'checkout') {
            switch ($action) {
                case 'cart':
                    return 'cart';
                case 'finish':
                    return 'buyout';
                default:
                    return false;
            }
        }

        return false;
    }

    /**
     * For given page name returns array of recommendation boxes that should be
     * rendered on it.
     *
     * @param string $page - page name
     * @return array - returns array of strings
     */
    private function getRecommendationBoxes($page)
    {
        $result = array();

        switch ($page) {
            case 'home':
                $result[] = array('id' => 'personal');
                $result[] = array('id' => 'bestseller');
                break;
            case 'category':
                $result[] = array('id' => 'category_page');
                break;
            case 'product':
                $result[] = array('id' => 'related');
                $result[] = array('id' => 'upselling');
                break;
            case 'cart':
                $result[] = array('id' => 'crossselling');
                break;
        }

        return $result;
    }

    protected function registerCustomModels()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\Models\Yoochoose', $this->Path() . 'Models/'
        );
        $this->Application()->Loader()->registerNamespace(
            'Shopware\Components', $this->Path() . 'Components/'
        );
        $this->Application()->ModelAnnotations()->addPaths(
            array(
                $this->Path() . 'Models/',
            )
        );
    }

    private function executeSchemaAction($action)
    {
        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\Models\Yoochoose\Yoochoose'),
        );

        try {
            $tool->$action($classes);
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            //ignore
        }
    }

    /**
     * Returns current shop
     *
     * @return null|object|\Shopware\Models\Shop\Shop
     */
    private function getCurrentShop()
    {
        $currentShopId = Shopware()->Shop()->getId();
        /** @var \Shopware\Models\Shop\Repository $shopRepository */
        $shopRepository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');

        return $currentShopId ? $shopRepository->find($currentShopId) : $shopRepository->getDefault();
    }

    /**
     * Returns current shop's language in a format specified in plugin configuration
     *
     * @param \Shopware\Models\Shop\Shop $shop
     * @return string - language
     */
    private function getShopLanguage($shop)
    {
        $language = $shop->getLocale()->getLocale();

        return str_replace('_', '-', $language);
    }
}
