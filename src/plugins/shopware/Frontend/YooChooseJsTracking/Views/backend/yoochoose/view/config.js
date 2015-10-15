//{namespace name=backend/yoochoose/view/config}
//{block name="backend/yoochoose/view/config"}
Ext.define('Shopware.apps.Yoochoose.view.Config', {
    extend: 'Ext.container.Container',
    alias: 'widget.yoochoose-config',
    layout: {
        type: 'vbox',
        align: 'stretch'
     },
    autoScroll: true,
    snippets: {
        title: '{s name=config/title}Base data{/s}'
    },
    initComponent: function () {
        var me = this;

        Ext.applyIf(me, {
            title: me.snippets.title,
            items: me.getItems()

        });

        me.callParent(arguments);
    },
    getItems: function () {
        var me = this;
        return [
            {
                xtype: 'general-settings',
                record: me.record
            },
            {
                xtype: 'script-settings',
                record: me.record
            }
        ];
    }

});
//{/block}