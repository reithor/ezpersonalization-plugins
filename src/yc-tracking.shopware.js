function initYcTrackingShopwareModule(context) {

    'use strict';

    var USE_N2GO = true;

    function stripItemId(itemId) {
        if (USE_N2GO) {
            return itemId;
        }

        return itemId.substr(0, 2) === 'SW' ? itemId.substr(2) : itemId;
    }

    function trackClickAndRate() {
        var articleBox = document.getElementById('buybox'),
            itemId,
            category,
            metaTags,
            comments,
            form,
            rating,
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

            // by default, rating is done when user evaluates product
            comments = document.getElementById('comments');
            form = comments ? comments.getElementsByTagName('form') : null;
            if (form && form.length === 1) {
                form = form[0];
                rating = form.getElementsByTagName('select');
                if (rating && rating[0].name === 'sVoteStars') {
                    form.onsubmit = function () {
                        YcTracking.trackRate(1, stripItemId(itemId), rating[0].value * 10);
                    }
                }
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
                    YcTracking.trackBasket(1, stripItemId(itemId), document.location.pathname);
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
                    YcTracking.trackBasket(1, stripItemId(itemId), document.location.pathname);
                }
            }
        }
    }

    function trackBuy() {
        var container = document.getElementById('yc-buy-items'),
            i;
        if (document.body.className === 'ctl_checkout' && container) {
            for (i = 0; i < container.children.length; i++) {
                var div = container.children[i],
                    itemId = div.children[0].value,
                    quantity = div.children[1].value,
                    price = div.children[2].value,
                    currency = div.children[3].value;

                YcTracking.trackBuy(1, stripItemId(itemId), quantity, price, currency);
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

        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
    };
}
