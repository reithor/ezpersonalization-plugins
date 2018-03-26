//{namespace name=backend/yoochoose/view/main}
//{block name="backend/yoochoose/view/main"}
Ext.define('Shopware.apps.Yoochoose.view.Main', {
    extend: 'Enlight.app.Window',
    alias: 'widget.yoochoose-main-window',
    layout: 'fit',
    width: '550px',
    height: '640px',
    maximizable: false,
    minimizable: true,
    autoScroll: true,
    stateful: true,
    stateId: 'YoochooseId',
    border: false,
    store: 'Settings',
    snippets: {
        title: '{s name=config/title}Yoochoose{/s}',
        cancel: '{s name=config/cancel}Cancel{/s}',
        save: '{s name=config/save}Save{/s}',
        savetooltip: '{s name=detail/save_tooltip}Save (CTRL + S){/s}'
    },
    initComponent: function () {
        var me = this;
        Ext.applyIf(me, {
            title: me.snippets.title
        });
        me.items = [
            me.createTabPanel()
        ];
        me.registerEvents();
        me.callParent(arguments);
    },
    createTabPanel: function() {
        var me = this;

        me.tabPanel = Ext.create('Ext.tab.Panel', {
            flex: 1,
            items: me.getItems(),
            bbar: me.getToolbar()
        });

        return me.tabPanel;
    },
    registerEvents: function () {
        this.addEvents(
                'saveForm'
                );
    },
    getItems: function () {
        var me = this,
            results = [],
            settings = me.record[0].raw;

        Object.keys(settings).forEach(function(key){
            Object.keys(settings[key]).forEach(function(k){
                if (settings[key][k] && (k === 'performance' || k === 'logSeverity')) {
                    settings[key][k] = parseInt(settings[key][k]);
                }
            });
            results.push(Ext.create('Ext.form.Panel', {
                collapsible: false,
                region: 'center',
                autoScroll: true,
                title: settings[key].shop,
                cls: 'swag-update-yoochoose-panel',
                items: [
                    {
                        xtype: 'yoochoose-config',
                        record: settings[key]
                    }
                ]
            }));
        });

        return results;
    },
    getToolbar: function () {
        var me = this;
        return [
            '->',
            {
                text: me.snippets.cancel,
                cls: 'secondary',
                handler: function () {
                    me.destroy();
                }
            },
            {
                text: me.snippets.save,
                cls: 'primary',
                tooltip: me.snippets.savetooltip,
                handler: function () {
                    me.fireEvent('saveForm', me);
                }
            }
        ];
    }
});
//{/block}
