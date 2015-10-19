//{block name="backend/yoochoose/store/store_settings"}
Ext.define('Shopware.apps.Yoochoose.store.StoreSettings', {
    extend:'Ext.data.Store',
    autoLoad: true,
    remoteSort: true,
    remoteFilter : true,
    batch: true,
    model: 'Shopware.apps.Yoochoose.model.Settings'
});
//{/block}
