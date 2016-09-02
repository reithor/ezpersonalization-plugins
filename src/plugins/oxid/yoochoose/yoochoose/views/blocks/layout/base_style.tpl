[{$smarty.block.parent}]
[{assign var="oConfig" value=$oViewConf->getConfig()}]

[{$oViewConf->renderScripts()}]

[{if $oView->getClassName() eq 'thankyou'}]
<script type="text/javascript">
    yc_config_object.orders = [];
    [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
        [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value }]
        [{assign var=oArticle value=$oArticleList[$sArticleId] }]
        yc_config_object.orders.push({
            'itemId': '[{$sArticleId}]',
            'quantity': '[{$orderitem->oxorderarticles__oxamount->value}]',
            'price': '[{$orderitem->oxorderarticles__oxprice->value|string_format:"%.2f"}]',
            'currency': '[{$order->oxorder__oxcurrency->value}]'
        });
    [{/foreach}]
</script>
[{/if}]
