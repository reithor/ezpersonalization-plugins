function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        document = context.document,
        allBoxes = [],
        ycObject,
        language,
        currentPage,
        responsesCount = 0,
        requestsSent = 0,
        itemType,
        script,
        templates = YC_RECO_TEMPLATES;

    function getCategoriesFromBreadcrumb() {
        var crumbs = document.querySelectorAll(YC_BREADCRUMBS_SELECTOR),
            i = 0,
            categories = [];

        for (; crumbs && i < crumbs.length; i++) {
            categories.push(crumbs[i].children[0][YC_BREADCRUMBS_VALUE].trim());
        }

        return categories.join('/');
    }

    function trackClickAndRate() {
        var category,
            itemId = ycObject ? ycObject.productIds : null,
            form;

        if (currentPage === 'product' && itemId) {
            category = getCategoriesFromBreadcrumb();
            YcTracking.trackClick(itemType, itemId, category, language);

            form = document.querySelector(YC_ARTICLE_RATING_FORM_SELECTOR);
            if (form) {
                form.addEventListener('submit', function () {
                    var rating = form.querySelector(YC_ARTICLE_RATING_FORM_RADIO_SELECTOR),
                        value = rating ? rating.value : 0;

                    YcTracking.trackRate(1, itemId, value * 5, language);
                }, false);
            }
        }
    }

    function hookBasketHandlers() {
        var cartButtons = document.querySelectorAll(YC_BASKET_LINKS_SELECTOR),
            productCart = null,
            categoryPath = getCategoriesFromBreadcrumb();

        [].forEach.call(cartButtons, function (button) {
                button.addEventListener('click', function () {
                    var productId = null,
                        data;

                    if (button.form && button.form.product) {
                        //button has form
                        productId = button.form.product.value;
                    } else if (button.attributes.getNamedItem('data-post')) {
                        data = JSON.parse(button.attributes.getNamedItem('data-post').value);
                        productId = data.data.product;
                    }

                    if (productId) {
                        YcTracking.trackBasket(itemType, productId, categoryPath, language);
                    }

                });
        });

        if (currentPage === 'product') {
            productCart = document.querySelector(YC_BASKET_FORMS_SELECTOR);
            if (productCart && [].indexOf.call(cartButtons, productCart) == -1) {
                YcTracking.trackBasket(itemType, productCart.form.prduct.value, categoryPath, language);
            }
        }
    }

    function trackBuy() {
        var orders = ycObject ? ycObject.orderData : null;

        if (currentPage === 'buyout' && orders) {
            orders.forEach(function (order) {
                YcTracking.trackBuy(itemType, order.id, order.qty, order.price, order.currency, language);
            });
        }
    }

    function fetchRecommendations() {
        var products = ycObject ? ycObject.productIds : [],
            tpl, i,
            url = ycObject ? ycObject.url : null,
            fncName,
            category = null;

        if (!url) {
            return;
        }

        switch (currentPage) {
            case 'category':
                allBoxes.push({'id': 'category_page'});
                category = getCategoriesFromBreadcrumb();
                break;
            case 'product':
                allBoxes.push({'id': 'upselling'});
                allBoxes.push({'id': 'related'});
                category = getCategoriesFromBreadcrumb();
                break;
            case 'home':
                allBoxes.push({'id': 'personal'});
                allBoxes.push({'id': 'bestseller'});
                category = context.location.pathname;
                break;
            case 'cart':
                allBoxes.push({'id': 'crossselling'});
                category = context.location.pathname;
                break;
        }

        url = url.replace('http:', context.location.protocol) + 'ycproductexport/';
        for (i = 0; i < allBoxes.length; i++) {
            tpl = templates[allBoxes[i].id];

            if (!tpl) {
                document.body.appendChild(document.createComment(
                    'Yoochoose: Template for ' + allBoxes[i].id + ' recommendation box is not found!'));
                console.log('Template for ' + allBoxes[i].id + ' recommendation box is not found!');
                allBoxes[i].priority = 999;
                return;
            }

            if (tpl.enabled) {
                allBoxes[i].template = tpl;
                allBoxes[i].priority = tpl.priority;
                fncName = 'YcTracking_jsonpCallback' + allBoxes[i].id;
                context[fncName] = fetchRecommendedProducts(allBoxes[i], url);
                YcTracking.callFetchRecommendedProducts(itemType, tpl.scenario, tpl.rows * tpl.columns, products, category, fncName, language);
            }

        }

    }

    function hookRecommendedBasketHandlers() {
        var cartButtons = document.querySelectorAll('.yc-recommendation-box ' + YC_BASKET_LINKS_SELECTOR),
            categoryPath = getCategoriesFromBreadcrumb();

        [].forEach.call(cartButtons, function (button) {
            button.addEventListener('click', function () {
                var productId = null,
                    data;

                if (button.attributes.getNamedItem('data-post')) {
                    data = JSON.parse(button.attributes.getNamedItem('data-post').value);
                    productId = data.data.product;
                }

                if (productId) {
                    YcTracking.trackBasket(itemType, productId, categoryPath, language);
                }

            });

        });
    }

    function hookTrackFollowEvent(){
        allBoxes.forEach(function (box) {
            var template = box ? box.template : null,
                elements = template ? GLOBAL.document.querySelectorAll(template.target + ' .yc-recommendation-box') : [];
            elements.forEach(function (elem) {
                if (!box['products']) {
                    return;
                }

                box.products.forEach(function (product) {
                    var buttons = elem.querySelectorAll('.yc-recommendation-box ' + YC_BASKET_LINKS_SELECTOR);

                    [].forEach.call(buttons, function (button) {
                        var productId = null;

                        if (button.attributes.getNamedItem('data-post')) {
                            var data = JSON.parse(button.attributes.getNamedItem('data-post').value);
                            productId = data.data.product;
                        }
                        if (productId === product.id) {
                            button.addEventListener('click', trackFollowEvent(product, template.scenario));
                        }
                    });
                });
            });
        });
    }

    function fetchRecommendedProducts(box, url) {
        return function (response) {
            var xmlHttp,
                productIds = [];

            if (!response.hasOwnProperty('recommendationResponseList')) {
                return;
            }

            response.recommendationResponseList.forEach(function (product) {
                productIds.push(product.itemId);
            });

            if (productIds.length === 0) {
                return;
            }

            if (context.XMLHttpRequest) {
                xmlHttp = new XMLHttpRequest();
            } else {
                xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
            }

            xmlHttp.onreadystatechange = function () {
                var idHistory = [];

                if (xmlHttp.readyState === 4) {
                    responsesCount++;
                    if (xmlHttp.status === 200) {
                        box.products = JSON.parse(xmlHttp.responseText);
                        if (responsesCount === requestsSent) {
                            allBoxes.sort(function (a, b) {
                                return a.priority - b.priority;
                            });

                            allBoxes.forEach(function (box) {
                                var renderedIds = [],
                                    currentBox = [];

                                if (!box.products) {
                                    return;
                                }

                                //select products that weren't rendered in higher priority boxes
                                box.products.forEach(function (item) {
                                    if (idHistory.indexOf(item.id) === -1) {
                                        currentBox.push(item);
                                    }
                                });

                                //out of unique products, take first N products
                                box.products = currentBox.slice(0, box.template.rows * box.template.columns);

                                //add Ids of N selected products, so they wouldn't have duplicates
                                box.products.forEach(function (item) {
                                    idHistory.push(item.id);
                                    renderedIds.push(item.id);
                                });

                                YcTracking.trackRendered(itemType, renderedIds);
                                YcTracking.renderRecommendation(box, language, trackFollowEvent);
                            });
                            hookTrackFollowEvent();
                            hookRecommendedBasketHandlers();
                        }
                    }
                }
            };

            requestsSent++;
            xmlHttp.open('GET', url + '?productIds=' + productIds.join(), true);
            xmlHttp.send();
        };
    }

    function trackFollowEvent(product, scenario) {
        return function () {
            YcTracking.trackClickRecommended(itemType, product.id, scenario);
        };
    }

    function setupTracking() {
        context.addEventListener('load', function () {
            var trackId,
                enableSearch;

            ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
            trackId = ycObject ? ycObject.trackid : null;
            currentPage = ycObject ? ycObject.currentPage : null;
            language = ycObject ? ycObject.language : null;
            itemType = ycObject ? ycObject.itemType : null;
            enableSearch = ycObject ? ycObject.enableSearch : null;

            YcTracking.trackLogin(trackId);
            trackClickAndRate();
            hookBasketHandlers();
            trackBuy();
            if (enableSearch == 1) {
                YcTracking.hookSearchingHandler(language);
            }
            fetchRecommendations();

        }, false);
    }

    if (typeof require === 'function') {
        require.config({
            paths: {"Handlebars": YC_HANDLEBARS_CDN},
            waitSeconds: 2
        });

        require(["Handlebars"], function (handlebars) {
            context.Handlebars = handlebars;
            setupTracking();
        });
    } else {
        if (!context['Handlebars']) {
            script = document.createElement('script');
            script.src = YC_HANDLEBARS_CDN;
            document.head.appendChild(script);
        }

        setupTracking();
    }
}