{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_javascript" append}
    {if $ycTrackingScriptUrl != ''}
        <link type="text/css" href="{$ycTrackingCssUrl}" rel="stylesheet" />
        <script type="text/javascript" src="{$ycTrackingScriptUrl}"></script>
    {else}
        <!--[if false]>
            Skipping importing tracking script. Please enter your customer ID and license key in the Shopware backend.
        <![endif]-->
    {/if}
{/block}

{block name="frontend_index_header_javascript_inline" append}
    var yc_articleId = '{if $sArticle}{$sArticle.articleID}{/if}';
    var yc_config_object = {$ycConfigObject};
    yc_config_object.url = '{url controller="yoochoose"}';
{/block}