//{namespace name=backend/yoochoose/view/script_settings}
//{block name="backend/yoochoose/view/script_settings"}
Ext.define('Shopware.apps.Yoochoose.view.ScriptSettings', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.script-settings',
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
        title: '{s name=general/title}Script Settings{/s}'
    },
    initComponent: function () {
        var me = this;

        me.title = me.snippets.title;
        me.items = me.createForm();

        me.callParent(arguments);
    },
    createForm: function () {
        var me = this,
                data = me.record[0].data;

        return [
            Ext.create('Ext.form.field.ComboBox', {
                fieldLabel: 'Performace',
                name: 'performance',
                typeAhead: false,
                transform: 'stateSelect',
                width: 135,
                forceSelection: true,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                editable: false,
                required: true,
                supportText: 'CDN mode provide better performance but takes about\n\
                              a 30 minutes, if the configuration is updated. Please \n\
                              switch to CDN only, if the configuration is done and stable.',
                value: data.performance ? data.performance : 1,
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'name'],
                    data: [
                        { id: 1, name: 'Load scripts from the Amazon content delivery network (CDN)' },
                        { id: 2, name: 'Load scripts directly from Yoochoose server' }
                    ]
                })
            }),
            Ext.create('Ext.form.field.Text', {
                name: 'scriptUrl',
                fieldLabel: 'Overwrite Endpoint',
                minWidth: 250,
                allowBlank: true,
                blankText: 'This field is required',
                required: false,
                helpText: 'Type plugin name, required field',
                supportText: 'Attention! See the <a href="https://doc.yoochoose.net/display/PUBDOC/Shopware+Plugin+Tutorial" target="_blank">extension manual</a>,\n\
                              if you about to use this property',
                value: data.scriptUrl,
                //{literal}
                regex: /^(https:\/\/|http:\/\/|\/\/)?([a-zA-Z][\w\-]*)((\.[a-zA-Z][\w\-]*)*)(:\d+)?((\/[a-zA-Z][\w\-]*){0,2})(\/)?$/
                //{/literal}
            })
        ];
    }
});
//{/block}