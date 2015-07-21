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
                registerNewUser: me.onRegisterNewUser
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
            form = [];

        Ext.each(elements, function(item){
            var o = {
              'name': item.name,
              'value': item.value
            };
            form.push(o);
        });

        Ext.Ajax.request({
            url: '{url controller="Yoochoose" action="saveForm"}',
            method: 'POST',
            params : { 
                form : JSON.stringify(form), 
            },
            success: function(response) {
                var result = Ext.decode(response.responseText);
                message = Ext.String.format(result.message, '');
                Shopware.Notification.createGrowlMessage('Success!', message, 'new message');
            },
            failure: function (response) {
                var result = Ext.decode(response.responseText);
                message = Ext.String.format(result.message, '');
                Shopware.Notification.createGrowlMessage('Error!', message, 'new message');
            }
        });
    },
    
    onRegisterNewUser: function () {
        Ext.Ajax.request({
            url: '{url controller="Yoochoose" action="getCustomerData"}',
            method: 'POST',
            success: function(response) {
                var result = Ext.decode(response.responseText),
                    form = document.createElement('form'),
                    input = document.createElement('input');

                form.method = 'post';
                form.action = result.url;
                form.target = '_blank';

                for (var property in result.data) {
                    if (result.data.hasOwnProperty(property)) {
                        input.setAttribute('name', property);
                        input.setAttribute('value', result.data[property]);
                        form.appendChild(input.cloneNode(true));
                    }
                }

                form.submit();
            },
            failure: function (response) {
                var result = Ext.decode(response.responseText),
                    message = Ext.String.format(result.message, '');

                Shopware.Notification.createGrowlMessage('Error!', message, 'new message');
            }
        });
    }
});
//{/block}
