<?php

/**
 * Class Shopware_Plugins_Frontend_YooChooseJsTracking_Bootstrap
 *
 * @package YooChoose Plugin
 * @version 1.0.0
 *
 */
class Shopware_Plugins_Frontend_YooChooseJsTracking_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    /**
     * This derived method is executed each time if this plugin will will be installed
     *
     * @return bool
     */
    public function install()
    {
        $this->createPluginForm();
        $this->createEvents();
        $this->createDatabase();

        return true;
    }

    /**
     * Remove attributes from table
     *
     * @return bool
     */
    public function uninstall()
    {
        $this->removeDatabase();

        return true;
    }

    /**
     * Returns the version of plugin as string.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Returns the label of the plugin as string
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Yoochooose JS Tracking';
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
        return [
            'version' => $this->getVersion(),
            'author' => $this->getAuthor(),
            'copyright' => $this->getAuthor(),
            'label' => $this->getLabel(),
            'support' => 'support@yoochoose.com',
            'link' => 'http://www.yoochoose.com/',
            'description' => '<p><img src="data:image/png;base64,' . $img . '" /></p>'
        ];
    }

    /**
     * Called when the FrontendPostDispatch Event is triggered
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onFrontendPostDispatch(Enlight_Event_EventArgs $args)
    {
        /* @var Enlight_View_Default $view */
        $view = $args->getSubject()->View();
        
        /* @var Enlight_Controller_Request_RequestHttp $request */
        $request = $args->getRequest();

        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/header.tpl');
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $view->assign('ycTrackingId', isset($userData['additional']['user']) ? $userData['additional']['user']['id'] : '');
        $view->assign('ycTrackingScriptUrl', preg_replace('(^https?:)', '', Shopware()->Config()->get('yoochoose_script_url')));
        
        $actionName = $request->getActionName();
        $controllerName = $request->getControllerName();
        if ($controllerName === 'account') {
            if ($actionName === 'logout' || $actionName === 'ajax_logout') {
                $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/ajax_logout.tpl');
                $view->assign('ycTrackLogout', true);
            }
        } else if ($controllerName === 'checkout' && $actionName === 'finish') {
            // needed for buy event
            $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/finish.tpl');
        } else if ($controllerName === 'listing') {
            // needed for basket event
            $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/box_article.tpl');
        }
    }

    private function createPluginForm()
    {
        $form = $this->Form();

        $form->setElement('text', 'yoochoose_script_url',
            [
                'label' => 'Script URL',
                'required' => true,
            ]);
    }

    private function createDatabase()
    {
        // database tables/fields should be created here if necessary.
    }

    private function removeDatabase()
    {
        
    }

    private function createEvents()
    {
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend', 'onFrontendPostDispatch');
    }
}
