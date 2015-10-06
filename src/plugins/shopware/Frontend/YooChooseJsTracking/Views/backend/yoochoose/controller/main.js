//{block name="backend/yoochoose\controller\main"}
Ext.define('Shopware.apps.Yoochoose.controller.Main', {
    extend: 'Ext.app.Controller',
    mainWindow: null,
    init: function () {
        var me = this;

        me.control({
            'yoochoose-main-window': {
                saveForm: me.onSaveForm
            },
            'general-settings': {
                registerNewUser: me.onRegisterNewUser,
                configureYoochoose: me.onConfigureYoochoose
            }
        });

        var store = me.subApplication.getStore('StoreSettings');
        store.load({
            callback: function(data) {
                me.mainWindow = me.getView('Main').create({
                    record: data
                }).show();
            }
        });

        me.callParent(arguments);
    },
    onSaveForm: function (window) {
        var me = this,
            message,
            str = [],
            form = window.down('form').getForm(),
            fields = form.getFields();

        if (form.isValid()) {
            me.saveFormConfig(fields);
        } else {
            fields.items.forEach(function(item){
                if (!item.wasValid) {
                    str.push(item.name);
                }
            });

            message = Ext.String.format('Not valid!', 'Erorr');
            Shopware.Notification.createGrowlMessage(str.join(', '), message, 'new message');
        }
    },
    saveFormConfig: function (fields) {
        var elements = fields.items,
            message,
            myMask = new Ext.LoadMask(this.mainWindow, { msg:"Please wait..." }),
            form = [];

        myMask.show();
        Ext.each(elements, function(item){
            var value = item.value, trimmed;

            trimmed = typeof value === 'string' ? value.trim() : value;
            form.push({
                'name': item.name,
                'value': trimmed
            });
        });

        Ext.Ajax.request({
            url: '{url controller="Yoochoose" action="saveForm"}',
            method: 'POST',
            params : { 
                form : JSON.stringify(form), 
            },
            success: function(response) {
                var result = Ext.decode(response.responseText);
                myMask.hide();
                message = Ext.String.format(result.message, '');
                Shopware.Notification.createGrowlMessage('Success!', message, 'new message');
            },
            failure: function (response) {
                var result = Ext.decode(response.responseText);
                myMask.hide();
                message = Ext.String.format(result.message, '');
                Shopware.Notification.createGrowlMessage('Error!', message, 'new message');
            }
        });
    },
    onRegisterNewUser: function () {
        var form = document.createElement('form'),
            lang = Ext.editorLang.split('_')[0];

            form.method = 'get';
            form.action = 'https://admin.yoochoose.net/login.html?product=shopware_Direct&lang=' + lang;
            form.target = '_blank';

            form.submit();
    },
    onConfigureYoochoose: function (customerId) {
        var form = document.createElement('form');

            form.method = 'get';
            form.action = 'https://admin.yoochoose.net/?customer_id=<customer_id>';
            form.target = '_blank';

            form.submit();
    }
});
//{/block}
