function initYcTrackingShopwareModule(context) {

    'use strict';

    function trackClickAndRate() {
        var articleBox = document.getElementById('buybox'),
            itemId = window['yc_articleId'] ? window['yc_articleId'] : null,
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
                } else if (!itemId && metaTags[i].getAttribute('itemprop') === 'identifier') {
                    itemId = metaTags[i].getAttribute('content').split(':')[1];
                }
            }

            if (itemId) {
                YcTracking.trackClick(1, itemId, category);

                // by default, rating is done when user evaluates product
                comments = document.getElementById('comments');
                form = comments ? comments.getElementsByTagName('form') : null;
                if (form && form.length === 1) {
                    form = form[0];
                    rating = form.getElementsByTagName('select');
                    if (rating && rating[0].name === 'sVoteStars') {
                        form.onsubmit = function () {
                            YcTracking.trackRate(1, itemId, rating[0].value * 10);
                        };
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
            i,
            clickHandler = function (e) {
                var input = e.target.parentNode.querySelector('[name="yc_articleId"]'),
                    parts = e.target.href.split('/'),
                    itemId = parts[parts.length - 1];

                YcTracking.trackBasket(1, input ? input.value : itemId, document.location.pathname);
            };
        for (i = 0; i < anchors.length; i += 1) {
            if (/checkout\/addArticle\/sAdd/i.test(anchors[i].href)) {
                anchors[i].onclick = clickHandler;
            }
        }

        if (document.body.className === 'ctl_detail' && btn) {
            btn.onclick = function (e) {
                var form = e.target.parentNode.parentNode,
                    inputs = form.getElementsByTagName('input'),
                    itemId = window['yc_articleId'] ? window['yc_articleId'] : null,
                    i;

                if (!itemId && inputs) {
                    for (i = 0; i < inputs.length; i += 1) {
                        if (inputs[i].name === 'sAdd') {
                            itemId = inputs[i].value;
                            break;
                        }
                    }
                }

                if (itemId) {
                    YcTracking.trackBasket(1, itemId, document.location.pathname);
                }
            };
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

                YcTracking.trackBuy(1, itemId, quantity, price, currency);
            }
        }
    }

    var YcTracking = context.YcTracking;

    window.onload = function (e) {
        if (window['yc_trackid']) {
            YcTracking.trackLogin(window['yc_trackid']);
        }

        if (window['yc_tracklogout']) {
            YcTracking.resetUser();
        }

        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
    };
}
