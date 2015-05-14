/* global YC_RECO_TEMPLATES, __st */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        templates = YC_RECO_TEMPLATES,
        language = document.getElementsByTagName('html')[0].getAttribute('lang'),
        syObject = window['__st'] ? window['__st'] : null,
        shopify = window['Shopify'] ? window['Shopify'] : null,
        trackid = syObject ? syObject.cid : null,
        script = document.createElement('script');

    function categoryFromBreadcrumb() {
        var breadcrumbs = document.querySelectorAll('.breadcrumb a span'),
            category = '',
            i;

        if (breadcrumbs && breadcrumbs.length) {
            for (i = 1; i < breadcrumbs.length; i++) {
                category += breadcrumbs[i].innerHTML + '/';
            }
        }

        return category;
    }

    function trackClickAndRate() {
        var currentPage = syObject ? syObject.p : null,
            pageUrl = syObject ? syObject.pageurl : null,
            category,
            itemSlug;

        if (currentPage === 'product') {
            category = categoryFromBreadcrumb();
            itemSlug = pageUrl.split('/').pop().split('?')[0].replace(/-/g, "_");
            YcTracking.trackClick(1, itemSlug, category, language);
            //there is no rating option by default in Shopify
        }
    }

    function hookBasketHandlers() {
        var currentPage = syObject ? syObject.p : null,
            pageUrl = syObject ? syObject.pageurl : null,
            category = document.location.pathname,
            itemSlug,
            submitButton,
            oldFunction,
            oldAddItem = shopify.addItem;

        if (currentPage === 'product') {
            submitButton = document.getElementById('add-to-cart');
            itemSlug = pageUrl.split('/').pop().split('?')[0].replace(/-/g, "_");
            oldFunction = submitButton.onclick;
            submitButton.onclick = function (e) {
                YcTracking.trackBasket(1, itemSlug, category, language);
                if (oldFunction) {
                    oldFunction.call(this, e);
                }
            };
        }

        //Overriding of ahopify ajax addItem method
        shopify.addItem = function (itemVariant, quantity, invokedFunction) {
            var me = this,
                newFunction = function (product) {
                    YcTracking.trackBasket(1, product.handle.replace(/-/g, "_"), category, language);
                    if (typeof invokedFunction === 'function') {
                        invokedFunction.call(me, product);
                    }
                };

            oldAddItem.call(this, itemVariant, quantity, newFunction);
        };
    }

    function trackBuy() {
        var currentPage = syObject ? syObject.t : null,
            script = document.createElement('script'),
            checkout = shopify.checkout,
            productIds = [];

        if (currentPage !== 'checkout' || !shopify) {
            return;
        }

        checkout.line_items.forEach(function (order) {
            productIds.push(order.product_id);
        });

        window['yc_trackBuyJsonpCallback'] = createTrackBuyJsonp(checkout.line_items, checkout.currency);
        script.src = 'https://localhost/shopify/index.php?shop=' + shopify.shop + '&ids=' + productIds.join() + '&jsonpCallback=yc_trackBuyJsonpCallback';
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    function createTrackBuyJsonp(lineItems, currency) {
        return function (response) {
            if (!response) {
                return;
            }

            lineItems.forEach(function (item) {
                YcTracking.trackBuy(1, response[item.product_id].replace(/-/g, "_"), parseInt(item.quantity),
                        parseFloat(item.price), currency);
            });
        };
    }

    function hookLogoutHandler(trackid) {
        if (!trackid && (typeof YcTracking.getUserId() === 'number')) {
            YcTracking.resetUser();
        }
    }

    function fetchRecommendations() {
        var currentPage = syObject.p ? syObject.p : syObject.t,
            pageUrl = syObject ? syObject.pageurl : '',
            category = null,
            products = [],
            boxes = [];

        switch (currentPage) {
            case 'product':
                category = categoryFromBreadcrumb();
                boxes.push({id: 'related', template: templates.related});
                boxes.push({id: 'upselling', template: templates.upselling});
                products.push(pageUrl.split('/').pop().split('?')[0].replace(/-/g, "_"));
                break;
            case 'home':
                boxes.push({id: 'personal', template: templates.personal});
                boxes.push({id: 'bestseller', template: templates.bestseller});
                break;
            case 'collection':
                category = categoryFromBreadcrumb();
                boxes.push({id: 'category_page', template: templates.category_page});
                break;
            case 'prospect':
                category = document.location.pathname;
                boxes.push({id: 'crossselling', template: templates.crossselling});
                shopify.getCart(function (cart) {
                    cart.items.forEach(function (item) {
                        products.push(item.handle);
                    });
                });
                break;
        }

        boxes.forEach(function (box) {
            var tpl = box.template,
                fncName;

            if (tpl.display) {
                box.title = tpl.title;
                box.trackFollowEvent = trackFollowEvent;
                fncName = 'YcTracking_jsonpCallback' + box.id;
                window[fncName] = fetchRecommendedProducts(box);
                YcTracking.callFetchRecommendedProducts(1, tpl.scenario, tpl.rows * tpl.columns, products.join(), category, fncName);
            }
        });
    }

    function fetchRecommendedProducts(box) {
        return function (response) {
            var productIds = [],
                products = [],
                current = 0,
                all;

            if (!response.hasOwnProperty('recommendationResponseList') || !response.recommendationResponseList.length) {
                return;
            }

            all = response.recommendationResponseList.length;
            response.recommendationResponseList.forEach(function (item) {
                productIds.push(item.itemId);

                // Shopify js API is hard depandant on jQuery, so it is safe to use it
                jQuery.ajax({
                    dataType: "json",
                    url: "/products/" + item.itemId.replace(/_/g, "-") + ".js",
                    success: function (e) {
                        products.push(e);
                    },
                }).always(function () {
                    current++;
                    if (current === all) {
                        box.products = products;
                        YcTracking.trackRendered(1, productIds);
                        YcTracking.renderRecommendation(box);
                    }
                });
            });
        };
    }

    function trackFollowEvent(product, scenario) {
        return function () {
            YcTracking.trackClickRecommended(1, product.handle, scenario);
        };
    }

    if (!window['Handlebars']) {
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    YcTracking.trackLogin(trackid);
    trackClickAndRate();
    hookBasketHandlers();
    trackBuy();
    hookLogoutHandler(trackid);
    fetchRecommendations();
}
