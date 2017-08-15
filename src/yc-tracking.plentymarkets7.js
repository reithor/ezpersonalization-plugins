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
        storeViewId,
        script,
        templates = YC_RECO_TEMPLATES,
        possibleRecoTemplatesPositions = ['APPEND', 'PREPEND', 'ABOVE', 'BELOW'];

    function getCategoriesFromBreadcrumb() {
        var crumbs = document.querySelectorAll(YC_BREADCRUMBS_SELECTOR),
            i = 0,
            categories = [];

        for (; crumbs && i < crumbs.length; i++) {
            if (crumbs[i].innerHTML && crumbs[i].pathname !== '/' && (ycObject.currentPage !== 'product' || crumbs[i].tagName !== 'SPAN')) {
                categories.push(crumbs[i].innerHTML.trim());
            }
        }

        return categories.join('/');
    }

    function trackClickAndRate() {
        var category,
            itemId = ycObject ? ycObject.productIds : null;

        if (currentPage === 'product' && itemId) {
            category = getCategoriesFromBreadcrumb();
            YcTracking.trackClick(itemType, itemId, category, language);
        }
    }

    function hookBasketHandlers() {
        var cartButtons = document.querySelectorAll(YC_CATEGORY_BASKET_SELECTOR),
            productCart = null,
            categoryPath = getCategoriesFromBreadcrumb();

        [].forEach.call(cartButtons, function (button) {
            button.addEventListener('click', function () {
                var productId = null,
                    vueElements = context['vueApp'].$children;

                vueElements.forEach(function (element) {
                    if (element._data.basketItem) {
                        if (element._data.basketItem.currentBasketItem.item) {
                            productId = element._data.basketItem.currentBasketItem.item.id;
                        }
                    }
                });

                if (productId) {
                    YcTracking.trackBasket(itemType, productId, categoryPath, language);
                }
            });
        });

        if (currentPage === 'product') {
            productCart = document.querySelector(YC_BASKET_FORMS_SELECTOR);
            productCart.addEventListener('click', function () {
                if (productCart && [].indexOf.call(cartButtons, productCart) == -1) {
                    YcTracking.trackBasket(itemType, ycObject.productIds, categoryPath, language);
                }
            });
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
                allBoxes.push({
                    'id': 'category_page'
                });
                category = getCategoriesFromBreadcrumb();
                break;
            case 'product':
                allBoxes.push({
                    'id': 'upselling'
                });
                allBoxes.push({
                    'id': 'related'
                });
                category = getCategoriesFromBreadcrumb();
                break;
            case 'home':
                allBoxes.push({
                    'id': 'personal'
                });
                allBoxes.push({
                    'id': 'bestseller'
                });
                category = context.location.pathname;
                break;
            case 'cart':
                allBoxes.push({
                    'id': 'crossselling'
                });
                category = context.location.pathname;
                break;
        }

        url = url.replace('http:', context.location.protocol) + 'yc/products/';
        for (i = 0; i < allBoxes.length; i++) {
            tpl = templates[allBoxes[i].id];

            if (!tpl) {
                document.body.appendChild(document.createComment(
                    'Yoochoose: Template for ' + allBoxes[i].id + ' recommendation box was not found!'));
                GLOBAL.console.error('Template for ' + allBoxes[i].id + ' recommendation box was not found!');
                allBoxes[i].priority = 999;
                return;
            }

            if (tpl.enabled){
                if (!isValidRecoTemplate(tpl)) {
                    allBoxes[i].priority = 999;
                    return;
                }

                allBoxes[i].template = tpl;
                allBoxes[i].priority = tpl.priority;
                fncName = 'YcTracking_jsonpCallback' + allBoxes[i].id;
                context[fncName] = fetchRecommendedProducts(allBoxes[i], url);
                YcTracking.callFetchRecommendedProductsV21(itemType, tpl.scenario, tpl.rows * tpl.columns, products, category, fncName, language);
            }
        }
    }

    function fetchRecommendedProducts(box, url) {
        return function (response) {
            var div = document.createElement('div'),
                xmlHttp,
                productIds = [];

            box.response = response;
            if (!response.hasOwnProperty('recommendationItems')) {
                return;
            }

            if (response.hasOwnProperty('recommendationItems')) {
                response.recommendationItems.forEach(function (product) {
                    productIds.push(product.itemId);
                });
            }

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
                                    var priceValue = item.newPrice.toFixed(2).toString().replace(YC_CONSTS.currency, '').replace('.', YC_DECIMAL_SEPARATOR);
                                    idHistory.push(item.id);
                                    renderedIds.push(item.id);
                                    item.totalItems = box.products.length === 3 ? '3' : false;
                                    if (item.oldPrice) {
                                        item.oldPrice = YC_RENDER_PRICE_FORMAT.replace('{price}', priceValue)
                                            .replace('{currencySign}', YC_CONSTS.currencySign)
                                            .replace('{currency}', YC_CONSTS.currency);
                                        
                                    }
                                    if (item.newPrice) {
                                        item.newPrice = YC_RENDER_PRICE_FORMAT.replace('{price}', priceValue)
                                            .replace('{currencySign}', YC_CONSTS.currencySign)
                                            .replace('{currency}', YC_CONSTS.currency);
                                    }
                                    
                                    box.response.recommendationItems.forEach(function (product) {
                                        if (item.id === product.itemId) {
                                            item.links = product.links;
                                            YcTracking.trackRenderedV2(product.links.rendered, language);
                                        }
                                    });
                                });

                                YcTracking.renderRecommendationV2(box, language, trackFollowEventV2, false);
                            });
                        }
                    }
                }
            };

            requestsSent++;
            xmlHttp.open('GET', url   + '?productIds=' +  productIds.join(), true);
            xmlHttp.send();
        };
    }

    function trackFollowEventV2(url) {
        return function () {
            YcTracking.trackClickRecommendedV3(url);
        };
    }

    function setupTracking() {
        context.addEventListener('load', function () {
            var trackId,
                enableSearch,
                productIds = [],
                vueElements = context['vueApp'].$children;

            vueElements.forEach(function (element) {
                if (element.item) {
                    productIds.push(element.item.item.id);
                }
            });
            
            ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
            ycObject.productIds = productIds ? productIds.join() : null;
            trackId = ycObject ? ycObject.trackid : null;
            currentPage = ycObject ? ycObject.currentPage : null;
            language = ycObject ? ycObject.language : null;
            itemType = ycObject ? ycObject.itemType : null;
            enableSearch = ycObject ? ycObject.enableSearch : null;
            storeViewId = ycObject ? ycObject.storeViewId : null;

            YcTracking.setStoreViewId(storeViewId);
            YcTracking.trackLogin(trackId);
            trackClickAndRate();
            hookBasketHandlers();
            trackBuy();
            if (enableSearch === '1') {
                YcTracking.hookSearchingHandler(language);
            }
            fetchRecommendations();

        }, false);
    }

    function isValidRecoTemplate( recoTemplate ) {
        if (recoTemplate.hasOwnProperty('position')) {
            if (possibleRecoTemplatesPositions.indexOf(recoTemplate.position) === -1)  {
                GLOBAL.console.error('RECO_TEMPLATES "' + recoTemplate.scenario + '.position" value is invalid ("'+ recoTemplate.position +'"). Possible values are: ' + possibleRecoTemplatesPositions.toString());
                return false;
            }
        }
        return true;
    }

    if (typeof require === 'function') {
        require.config({
            paths: {
                "Handlebars": YC_HANDLEBARS_CDN
            },
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