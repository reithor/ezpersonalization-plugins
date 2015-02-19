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
        return 'YooChooose JS Tracking';
    }

    /**
     * Returns name of our company
     *
     * @return string
     */
    public function getAuthor()
    {
        return 'YooChoose GmbH';
    }

    /**
     * Information about this plugin
     *
     * @return array
     */
    public function getInfo()
    {
        return [
            'version' => $this->getVersion(),
            'author' => $this->getAuthor(),
            'copyright' => $this->getAuthor(),
            'label' => $this->getLabel(),
            'support' => 'www.yoochoose.com',
            'link' => 'http://www.yoochoose.com/',
            'description' => ''
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
        $view->trackingId = isset($userData['additional']['user']) ? $userData['additional']['user']['id'] : '';
        
        if ($request->getControllerName() === 'account') {
            $actionName = $request->getActionName();
            if ($actionName === 'logout' || $actionName === 'ajax_logout') {
                $view->extendsTemplate('frontend/plugins/yoochoose_jstracking/ajax_logout.tpl');
                $view->trackLogout = true;
            }
        }
    }

    private function createPluginForm()
    {
        // config form should be created here if necessary.
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
