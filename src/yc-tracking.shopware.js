/* global YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        document = context.document,
        ycObject = null,
        trackId = null,
        language = null,
        responsesCount = 0,
        requestsSent = 0,
        script,
        templates = YC_RECO_TEMPLATES;

    function getCategoriesFromBreadcrumb(left, right) {
        var aaa = document.querySelectorAll(YC_BREADCRUMBS_SELECTOR),
            i = left ? left : 0,
            n = right ? right : 0,
            category = [];

        for (; aaa && i < aaa.length - n; i++) {
            if (aaa[i][YC_BREADCRUMBS_VALUE] && (ycObject.currentPage !== 'product' || location.href !== aaa[i].href)) {
                category.push(aaa[i][YC_BREADCRUMBS_VALUE].trim());
            }
        }

        return category.join('/');
    }

    function trackClickAndRate() {
        var itemId = context['yc_articleId'] ? context['yc_articleId'] : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            category,
            form,
            rating;

        if (currentPage === 'product' && itemId) {
            category = getCategoriesFromBreadcrumb(YC_BC_OFFSETS.product.left, YC_BC_OFFSETS.product.right);
            YcTracking.trackClick(1, itemId, category, language);
            // by default, rating is done when user evaluates product
            form = document.querySelector(YC_ARTICLE_RATING_FORM_SELECTOR);
            if (form) {
                rating = form.querySelector(YC_ARTICLE_RATING_FIELD_SELECTOR);
                if (rating) {
                    form.addEventListener('submit', function () {
                        YcTracking.trackRate(1, itemId, rating.value * 10, language);
                    }, false);
                }
            }
        }
    }

    function hookBasketHandlers() {
        // in widget: <a href="..shopurl../checkout/addArticle/sAdd/ITEMID" ...>...</a>
        // on product details form: <form name="sAddToBasket" action="..shopurl../checkout/addArticle" ...>
        var anchors = document.getElementsByTagName('a'),
            currentPage = ycObject ? ycObject.currentPage : null,
            btn = document.querySelector(YC_ARTICLE_BASKET_BUTTON),
            itemId = context['yc_articleId'] ? context['yc_articleId'] : null,
            i,
            clickHandler = function (e) {
                var input = e.target.parentNode.querySelector('[name="yc_articleId"]'),
                    parts = e.target.href.split('/'),
                    itemId = parts[parts.length - 1];

                YcTracking.trackBasket(1, input ? input.value : itemId, document.location.pathname, language);
            };

        for (i = 0; i < anchors.length; i += 1) {
            if (/checkout\/addArticle\/sAdd/i.test(anchors[i].href)) {
                anchors[i].addEventListener('click', clickHandler, false);
            }
        }

        if (currentPage === 'product' && btn) {
            btn.addEventListener('click', function () {
                if (itemId) {
                    YcTracking.trackBasket(1, itemId, document.location.pathname, language);
                }
            }, false);
        }
    }

    function trackBuy() {
        var container = document.getElementById('yc-buy-items'),
            currentPage = ycObject ? ycObject.currentPage : null,
            i;

        if (currentPage === 'buyout' && container) {
            for (i = 0; i < container.children.length; i++) {
                var div = container.children[i],
                    itemId = div.children[0].value,
                    quantity = div.children[1].value,
                    price = div.children[2].value,
                    currency = div.children[3].value;

                YcTracking.trackBuy(1, itemId, quantity, price, currency, language);
            }
        }
    }

    function fetchRecommendations() {
        var boxes = ycObject ? ycObject.boxes : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            products = context['yc_articleId'] ? context['yc_articleId'] : [],
            tpl, i,
            elements,
            url = ycObject ? ycObject.url : null,
            fncName,
            category = currentPage === 'cart' ? document.location.pathname : getCategoriesFromBreadcrumb(YC_BC_OFFSETS.product.left, YC_BC_OFFSETS.product.right);

        if (!boxes || boxes.length === 0 || !url) {
            return;
        }

        if (currentPage === 'category') {
            category = getCategoriesFromBreadcrumb(YC_BC_OFFSETS.category.left, YC_BC_OFFSETS.category.right);
            elements = document.getElementsByName('yc_articleId');
            for (i = 0; i < elements.length; i++) {
               products.push(elements[i].value);
            }

            products = products.join();
        }

        url = url.replace('http:', location.protocol);
        for (i = 0; i < boxes.length; i++) {
            tpl = templates[boxes[i].id];

            if (tpl && tpl.enabled) {
                boxes[i].template = tpl;
                boxes[i].priority = tpl.priority;
                fncName = 'YcTracking_jsonpCallback' + boxes[i].id;
                context[fncName] = fetchRecommendedProducts(boxes[i], url);
                YcTracking.callFetchRecommendedProducts(1, tpl.scenario, tpl.rows * tpl.columns, products, category, fncName, language);
            } else {
                console.log('Template for ' + boxes[i].id + ' recommendation box is not found!');
                boxes[i].priority = 999;
            }
        }
    }

    /**
     * Creates function for JSONP callback. Fetches requested products from backend
     * and renders them using supplied function.
     * 
     * @param {object} box Recommendation box config with products in it.
     * @param {string} url Backend url
     * @returns {function} Callback function
     */
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

            url += '?productIds=' + productIds.join();
            if (context.XMLHttpRequest) {
                xmlHttp = new XMLHttpRequest();
            } else {
                xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
            }

            xmlHttp.onreadystatechange = function () {
                var allBoxes = context.yc_config_object.boxes,
                    idHistory = [];

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

                                YcTracking.trackRendered(1, renderedIds);
                                YcTracking.renderRecommendation(box, language, trackFollowEvent);
                            });
                            StateManager.updatePlugin('*[data-product-slider-yc="true"]', 'swProductSlider');
                        }
                    }
                }
            };

            requestsSent++;
            xmlHttp.open('GET', url, true);
            xmlHttp.send();
        };
    }

    function trackFollowEvent(product, scenario) {
        return function () {
            YcTracking.trackClickRecommended(1, product.id, scenario);
        };
    }

    if (!context['Handlebars']) {
        script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min.js';
        document.head.appendChild(script);
    }

    context.addEventListener('load' ,function () {
        var trackLogout;

        ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
        trackId = ycObject ? ycObject.trackid : null;
        language = ycObject ? ycObject.lang : null;
        trackLogout = ycObject ? ycObject.trackLogout : null;

        if (trackId) {
            YcTracking.trackLogin(trackId);
        }

        if (trackLogout) {
            YcTracking.resetUser();
        }

        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
        YcTracking.hookSearchingHandler(language);
        fetchRecommendations();

    }, false);
}
