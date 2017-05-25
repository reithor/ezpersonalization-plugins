//{block name="backend/yoochoose\controller\main"}
Ext.define('Shopware.apps.Yoochoose.controller.Main', {
    extend: 'Ext.app.Controller',
    mainWindow: null,
    init: function () {
        var me = this;

        me.control({
            'yoochoose-main-window': {
                saveForm: me.onSaveForm
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
    onSaveForm: function () {
        var me = this,
            message, form, fields,
            items = [],
            formsValid = true,
            str = [],
            panels = Ext.ComponentQuery.query('panel[cls=swag-update-yoochoose-panel]');

        Ext.each(panels, function (panel) {
            if (panel.hidden === false) {
                form = panel.getForm();
                fields = form.getFields();
                if (form.isValid() && formsValid) {
                    items.push(fields.items);
                } else {
                    formsValid = false;
                    fields.items.forEach(function (item) {
                        if (!item.wasValid) {
                            str.push(item.name);
                        }
                    });
                }
            }
        });

        if (formsValid) {
            me.saveFormConfig(items);
        } else {
            message = Ext.String.format('Not valid!', 'Erorr');
            Shopware.Notification.createGrowlMessage(str.join(', '), message, 'new message');
        }
    },
    saveFormConfig: function (elements) {
        var message,
            myMask = new Ext.LoadMask(this.mainWindow, { msg:"Please wait..." }),
            shops = [];

        myMask.show();
        Ext.each(elements, function(element){
            var fields = [], shopId;

            Ext.each(element, function(item){
                var value = item.value,
                    trimmed = typeof value === 'string' ? value.trim() : value;

                if (item.name === 'shopId') {
                    shopId = trimmed;
                } else {
                    fields.push({
                        'name': item.name,
                        'value': trimmed
                    });
                }
            });
            if (shopId) {
                shops.push({
                    'shopId': shopId,
                    'fields': fields
                });
            }

        });

        Ext.Ajax.request({
            url: '{url controller="Yoochoose" action="saveForm"}',
            method: 'POST',
            params : { 
                shops : JSON.stringify(shops)
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
    }
});
//{/block}
