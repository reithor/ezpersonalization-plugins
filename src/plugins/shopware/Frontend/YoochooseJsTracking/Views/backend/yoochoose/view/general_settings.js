//{namespace name=backend/yoochoose/view/general_settings}
//{block name="backend/yoochoose/view/general_settings"}
Ext.define('Shopware.apps.Yoochoose.view.GeneralSettings', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.general-settings',
    collapsible: true,
    collapsed: false,
    hidden: false,
    width: '100%',
    margin: 5,
    autoScroll: true,
    border: true,
    defaults: {
        labelWidth: 160,
        anchor: '100%'
    },
    snippets: {
        title: '{s name=general/title}General Settings{/s}'
    },
    initComponent: function () {
        var me = this;

        me.title = me.snippets.title;
        me.items = me.createForm();

        me.callParent(arguments);
    },
    createForm: function () {
        var me = this,
            data = me.record,
            customerLink = data.customerId ? '/?customer_id=' + data.customerId : '';

        return [
            {
                xtype: 'button',
                fieldLabel: 'If you don\'t have a Customer ID yet please',
                text: 'Click here to register new Yoochoose account',
                style: 'margin-bottom: 5px',
                handler: function () {
                    var url = yoochooseAdminUrl + 'login.html?product=shopware_Direct&lang=' + Ext.editorLang.split('_')[0],
                        win = window.open(url, '_blank');

                    win.focus();
                }
            },
            Ext.create('Ext.form.field.Text', {
                name: 'customerId',
                fieldLabel: 'Customer ID',
                minWidth: 250,
                allowBlank: false,
                blankText: 'This field is required',
                required: true,
                value: data.customerId ? data.customerId : null,
                listeners: {
                    'change': function(){
                        var customerLink = document.getElementById('yoochoose-admin-link'), 
                            newCustomerId = arguments[1];

                        customerLink.href = customerLink.href.replace(/(.*=)(\d*)/g, function () {
                            return arguments[1] + newCustomerId;
                        });
                    }
                  }
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'licenseKey',
                fieldLabel: 'License Key',
                minWidth: 250,
                allowBlank: false,
                blankText: 'This field is required',
                required: true,
                supportText: 'You can find you license key and detailed statistics on the\n\
                             <a id="yoochoose-admin-link" href=" https://admin.yoochoose.net' + customerLink + '" target="_blank">Yoochoose Configuration Backend</a>',
                value: data.licenseKey
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'pluginId',
                fieldLabel: 'Plugin ID',
                minWidth: 250,
                allowBlank: true,
                supportText: 'Optional field. If you have only one shop, please leave this field blank.',
                value: data.pluginId
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'endpoint',
                fieldLabel: 'Endpoint',
                minWidth: 250,
                allowBlank: true,
                blankText: 'This field is required',
                required: false,
                readOnly: true,
                helpText: 'Your shop must be accessible from the Internet. This field is read-only here. The value can be changed in Basic settings -> Shop settings',
                value: data.endpoint
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'design',
                fieldLabel: 'Design',
                minWidth: 250,
                allowBlank: true,
                blankText: 'This field is required',
                required: false,
                readOnly: true,
                helpText: 'Read-only field.',
                supportText: 'We will try a design template for your shop.\n\
                              Please read <a href="https://doc.yoochoose.net/display/PUBDOC/Shopware+Plugin+2.0+Tutorial" target="_blank">Shopware Connect Extension Tutorial</a>,\n\
                              if you need to customize the design of the recommendations.',
                value: data.design
            }),
            Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Log Severity',
                name: 'logSeverity',
                typeAhead: false,
                transform: 'stateSelect',
                width: 135,
                forceSelection: true,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                editable: false,
                required: true,
                value: data.logSeverity ? data.logSeverity : 1,
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'name'],
                    data: [
                        { id: 1, name: 'Info' },
                        { id: 2, name: 'Debug' }
                    ]
                })
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'shopId',
                minWidth: 250,
                hidden: true,
                required: true,
                readOnly: true,
                value: data.shopId
            }),
        ];
    }
});
//{/block}