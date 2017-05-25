//{namespace name=backend/yoochoose/view/api_settings}
//{block name="backend/yoochoose/view/api_settings"}
Ext.define('Shopware.apps.Yoochoose.view.ApiSettings', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.api-settings',
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
        title: '{s name=yoochoose/api/title}API Settings{/s}'
    },
    initComponent: function () {
        var me = this;

        me.title = me.snippets.title;
        me.items = me.createForm();

        me.callParent(arguments);
    },
    createForm: function () {
        var me = this,
            data = me.record;

        return [
            Ext.create('Ext.form.field.Text', {
                name: 'username',
                fieldLabel: 'Username',
                minWidth: 250,
                readOnly: true,
                helpText: 'Read-only field.',
                value: data.username
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'apiKey',
                fieldLabel: 'API Key',
                minWidth: 250,
                readOnly: true,
                helpText: 'Read-only field.',
                value: data.apiKey
            })
        ];
    }
});
//{/block}