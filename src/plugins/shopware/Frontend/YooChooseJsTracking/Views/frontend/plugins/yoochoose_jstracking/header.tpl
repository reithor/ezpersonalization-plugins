{extends file='parent:frontend/index/header.tpl'}

{block name="frontend_index_header_javascript" append}
<script type="text/javascript" src="//localhost:63342/JS-Tracking/dist/yc-tracking.js"></script>
{/block}

{block name="frontend_index_header_javascript_inline" append}
    var yc_trackid = '{$ycTrackingId}';
    var yc_tracklogout = '{$ycTrackLogout}';
{/block}