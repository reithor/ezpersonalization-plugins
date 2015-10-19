//{block name="backend/yoochoose/app"}
Ext.define('Shopware.apps.Yoochoose', {
    name: 'Shopware.apps.Yoochoose',
    extend: 'Enlight.app.SubApplication',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: ['Main'],
    models: ['Settings'],
    stores: ['StoreSettings'],
    views: ['Main', 'Config', 'GeneralSettings', 'ScriptSettings'],
    launch: function () {
        var me = this,
            mainController = me.getController('Main');
        return mainController.mainWindow;
    }
});
//{/block}
