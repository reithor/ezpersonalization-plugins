//{block name="backend/yoochoose/model/settings"}
Ext.define('Shopware.apps.Yoochoose.model.Settings', {
    extend: 'Ext.data.Model',
    autoLoad:true,
    fields: [
        { name: 'customerId', type: 'int' },
        { name: 'licenseKey', type: 'string' },
        { name: 'pluginId', type: 'string' },
        { name: 'endpoint', type: 'string' },
        { name: 'design', type: 'string' },
        { name: 'performance', type: 'int' },
        { name: 'scriptUrl', type: 'string' },
        { name: 'username', type: 'string' },
        { name: 'apiKey', type: 'string' },
        { name: 'logSeverity', type: 'string' },
        { name: 'shopId', type: 'string' }
    ],
    proxy:{
        type:'ajax',
        api: {
            read: '{url controller="Yoochoose" action="getData"}'
        },
        reader:{
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    }

});
//{/block}
