function initYcTrackingModule(context) {

    'use strict';

    function trackClickAndRate() {
        var addToCartForm = document.getElementById('product_addtocart_form'),
            itemType = window['yc_itemType'] ? window['yc_itemType'] : null,
            language = window['yc_language'] ? window['yc_language'] : null,
            itemId = window['yc_articleId'] ? window['yc_articleId'] : null,
            category = '',
            breadcrumbs, list,
            reviewForm,
            form = document.getElementById('review-form'),
            i;
        if (addToCartForm) {
            if (!itemId) {
                if (addToCartForm.product) {
                    itemId = addToCartForm.product.value;
                }
            }

            // category is only stored in breadcrumbs
            breadcrumbs = document.getElementsByClassName('breadcrumbs');
            if (breadcrumbs) {
                list = breadcrumbs[0].children[0].children;
                for (i = 0; i < list.length; i++) {
                    if (list[i].className.indexOf('category') !== -1) {
                        category += list[i].children[0].text + '/';
                    }
                }
            }
        }

        if (itemId) {
            YcTracking.trackClick(itemType, itemId, category, language);

            if (reviewForm) {
                reviewForm.onsubmit = function (e) {
                    var getRatings = function(elements, sub) {
                            for (var i = 0; i < elements.length; i++) {
                                if (elements[i].checked) {
                                    return parseInt(elements[i].value) - sub;
                                }
                            }

                            return 0;
                        },
                        qualityRatings = getRatings(this.elements['ratings[1]'], 0),
                        valueRatings = getRatings(this.elements['ratings[2]'], 5),
                        priceRatings = getRatings(this.elements['ratings[3]'], 10);

                    if (qualityRatings !== 0 && valueRatings !== 0 && priceRatings !== 0) {
                        YcTracking.trackRate(itemType, itemId, qualityRatings * 20, language);
                        YcTracking.trackRate(itemType, itemId, valueRatings * 20, language);
                        YcTracking.trackRate(itemType, itemId, priceRatings * 20, language);
                    }
                };
            }
        }
    }

    function hookBasketHandlers() {
        var addToCartForm = document.getElementById('product_addtocart_form'),
            itemType = window['yc_itemType'] ? window['yc_itemType'] : null,
            language = window['yc_language'] ? window['yc_language'] : null;

        override('setLocation');
        override('setPLocation');

        if (window['addWItemToCart']) {
            var oldAddWItemToCart = window.addWItemToCart;
            window.addWItemToCart = function (itemId) {
                YcTracking.trackBasket(itemType, itemId, document.location.pathname, language);
                oldAddWItemToCart(itemId);
            }
        }

        if (window['addAllWItemsToCart']) {
            var oldAddAllWItemsToCart = window.addAllWItemsToCart;
            window.addAllWItemsToCart = function () {
                var items, field, i;
                oldAddAllWItemsToCart();
                field = document.getElementById('qty');
                if (field) {
                    items = JSON.parse(field.value);
                    if (items) {
                        for (i = 0; i < items.length; i++) {
                            if (items[i]) {
                                YcTracking.trackBasket(itemType, i, document.location.pathname, language);
                            }
                        }
                    }
                }
            }
        }

        if (addToCartForm) {
            attachSubmitAddToCartForm(addToCartForm);
        }

        if (window['productAddToCartForm']) {
            attachSubmitAddToCartForm(window['productAddToCartForm'].form);
        }

        function override(func) {
            if (window[func]) {
                var oldFunc = window[func];
                window[func] = function (url) {
                    trackBasketFromUrl(url);
                    oldFunc(url);
                }
            }
        }

        function trackBasketFromUrl(url) {
            if (/checkout\/cart\/add/i.test(url)) {
                var parts = url.split('/'),
                    itemId, i;

                for (i = 0; i < parts.length; i++) {
                    if (parts[i] === 'product') {
                        itemId = parseInt(parts[i + 1]);
                        break;
                    }
                }

                if (itemId) {
                    YcTracking.trackBasket(itemType, itemId, document.location.pathname, language);
                }
            }
        }

        function attachSubmitAddToCartForm(form) {
            var oldSubmit = null,
                processForm = function() {
                    if (this.product && this.product.value) {
                        YcTracking.trackBasket(itemType, this.product.value, document.location.pathname, language);
                    }

                    if (oldSubmit) {
                        oldSubmit.call(this);
                    }
                };

            if (form) {
                // bad! But since Magento js handlers did not use regular onsubmit event,
                // this is the only way to handle submit on add to cart forms, because standard onSubmit event
                // is not being fired.
                oldSubmit = form.submit;
                form.submit = processForm;
            }
        }
    }

    function trackBuy() {
        var orders, order, i,
            language = window['yc_language'] ? window['yc_language'] : null;
        if (window['yc_orderData']) {
            orders = window['yc_orderData'];
            for (i = 0; i < orders.length; i++) {
                order = orders[i];
                if (order) {
                    YcTracking.trackBuy(itemType, parseInt(order['id']), parseInt(order['quantity']),
                        parseFloat(order['price']), order['currency'], language);
                }
            }
        }
    }

    function hookLogoutHandler() {
        var container = document.getElementById('header-account'),
            anchors = container ? container.getElementsByTagName('a') : null,
            i;

        if (anchors) {
            for (i = 0; i < anchors.length; i++) {
                if (/customer\/account\/logout/i.test(anchors[i].href)) {
                    anchors[i].onclick = function () {
                        YcTracking.resetUser();
                    }
                }
            }
        }
    }

    var YcTracking = context.YcTracking;

    window.onload = function () {
        YcTracking.trackLogin(window['yc_trackid']);

        trackClickAndRate();
        hookBasketHandlers();
        hookLogoutHandler();
        trackBuy();
    };
}
