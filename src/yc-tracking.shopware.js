function initYcTrackingShopwareModule(context) {

    'use strict';

    function stripItemId(itemId) {
        return itemId.substr(0, 2) === 'SW' ? itemId.substr(2) : itemId;
    }

    function trackClick() {
        var articleBox = document.getElementById('buybox'),
            itemId,
            category,
            metaTags,
            i;
        if (document.body.className === 'ctl_detail' && articleBox) {
            metaTags = articleBox.getElementsByTagName('meta');
            for (i = 0; i < metaTags.length; i++) {
                if (metaTags[i].getAttribute('itemprop') === 'category') {
                    category = metaTags[i].getAttribute('content').replace(/ > /g, '/');
                } else if (metaTags[i].getAttribute('itemprop') === 'identifier') {
                    itemId = metaTags[i].getAttribute('content').split(':')[1];
                }
            }

            if (itemId) {
                YcTracking.trackClick(1, stripItemId(itemId), category);
            }
        }
    }

    function hookBasketHandlers() {
        // in widget: <a href="..shopurl../checkout/addArticle/sAdd/ITEMID" ...>...</a>
        // on product details form: <form name="sAddToBasket" action="..shopurl../checkout/addArticle" ...>
        var anchors = document.getElementsByTagName('a'),
            btn = document.getElementById('basketButton'),
            i;
        for (i = 0; i < anchors.length; i += 1) {
            if (/checkout\/addArticle\/sAdd/i.test(anchors[i].href)) {
                anchors[i].onclick = function (e) {
                    var parts = e.target.href.split('/'),
                        itemId = parts[parts.length - 1];
                    YcTracking.trackBasket(1, stripItemId(itemId), document.location.href);
                };
            }
        }

        if (document.body.className === 'ctl_detail' && btn) {
            btn.onclick = function (e) {
                var form = e.target.parentNode.parentNode,
                    inputs = form.getElementsByTagName('input'),
                    itemId, i;

                if (inputs) {
                    for (i = 0; i < inputs.length; i += 1) {
                        if (inputs[i].name === 'sAdd') {
                            itemId = inputs[i].value;
                            break;
                        }
                    }
                }

                if (itemId) {
                    YcTracking.trackBasket(1, stripItemId(itemId), document.location.href);
                }
            }
        }
    }

    var YcTracking = context.YcTracking;

    window.onload = function (e) {
        if (window['yc_trackid']) {
            YcTracking.trackLogin(yc_trackid);
        }

        if (window['yc_tracklogout']) {
            YcTracking.resetUser();
        }

        trackClick();
        hookBasketHandlers();
    };
}
