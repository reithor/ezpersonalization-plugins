{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_javascript" append}
    <link type="text/css" href="{$ycTrackingCssUrl}" rel="stylesheet" />
    <script type="text/javascript" src="{$ycTrackingScriptUrl}"></script>
{/block}

{block name="frontend_index_header_javascript_inline" append}
    var yc_articleId = '{if $sArticle}{$sArticle.articleID}{/if}';
    var yc_config_object = {$ycConfigObject};
    yc_config_object.url = '{url controller="yoochoose"}';
{/block}