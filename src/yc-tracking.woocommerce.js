/* global YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        templates = YC_RECO_TEMPLATES,
        ycObject,
        requestsSent = 0,
        language,
        currentPage,
        responsesCount = 0,
        script;

    function categoryFromBreadcrumb(left, right) {
        var breadcrumbs = document.querySelectorAll(YC_BREADCRUMBS_SELECTOR),
            category = [],
            i = left ? left : 0,
            n = right ? right : 0;

        for (; breadcrumbs && i < breadcrumbs.length - n; i++) {
            category.push(breadcrumbs[i][YC_BREADCRUMBS_VALUE].trim());
        }

        return category.join('/');
    }

    function trackClickAndRate() {
        var itemId = ycObject ? ycObject.productIds : null,
            reviewForm = document.querySelector(YC_ARTICLE_RATING_FORM_SELECTOR),
            category = categoryFromBreadcrumb(YC_BC_OFFSETS.product.left, YC_BC_OFFSETS.product.right);

        if (currentPage === 'product' && itemId) {
            YcTracking.trackClick(1, itemId, category, language);

            if (reviewForm) {
                reviewForm.addEventListener('submit', function (e) {
                    var rating = reviewForm.rating,
                        ratingValue;

                    if (rating) {
                        ratingValue = parseInt(rating.value);
                        YcTracking.trackRate(1, itemId, ratingValue * 20, language);
                    }
                }, false);
            }
        }
    }

    function hookBasketHandlers() {
        var itemId = ycObject ? ycObject.productIds : null,
            productButton = document.querySelector(YC_ARTICLE_BASKET_BUTTON),
            elements = document.querySelectorAll(YC_ADD_BASKET_BUTTON_SELECTOR),
            i;

        for (i = 0; i < elements.length; i++) {
            elements[i].addEventListener('click', function (e) {
                YcTracking.trackBasket(1, this.getAttribute(YC_ADD_BASKET_BUTTON_ITEMID), document.location.pathname, language);
            }, false);
        }

        if (currentPage === 'product' && productButton) {
            productButton.addEventListener('click', function (e) {
                YcTracking.trackBasket(1, itemId, categoryFromBreadcrumb(YC_BC_OFFSETS.product.left, YC_BC_OFFSETS.product.right), language);
            }, false);
        }
    }

    function trackBuy() {
        var orders = ycObject ? ycObject.orderData : null;

        if (currentPage === 'buyout') {
            orders.forEach(function (order) {
                if (order) {
                    YcTracking.trackBuy(1, parseInt(order['id']), parseInt(order['qty']),
                            parseFloat(order['price']), order['currency'], language);
                }
            });
        }
    }
    
    function hookLogoutHandler(trackid) {
        if (!trackid && (typeof YcTracking.getUserId() === 'number')) {
            YcTracking.resetUser();
        }
    }
    
    function fetchRecommendations() {
        var boxes = ycObject ? ycObject.boxes : null,
            products = ycObject && ycObject.productIds ? ycObject.productIds : [],
            tpl, i,
            elements,
            url = context['yoochoose_ajax_script'].ajaxurl + '?action=yoochoose_products&productIds=',
            fncName,
            category = null;

        if (!boxes) {
            return;
        }

        if (currentPage === 'product') {
            category = categoryFromBreadcrumb(YC_BC_OFFSETS.product.left, YC_BC_OFFSETS.product.right);
        } else if (currentPage === 'category') {
            category = categoryFromBreadcrumb(YC_BC_OFFSETS.category.left, YC_BC_OFFSETS.category.right);
        } else if (currentPage === 'cart') {
            category = document.location.pathname;
        }
        
        if (currentPage === 'category' || currentPage === 'home') {
            elements = document.querySelectorAll(YC_ADD_BASKET_BUTTON_SELECTOR);
            for (i = 0; i < elements.length; i++) {
               products.push(elements[i].getAttribute(YC_ADD_BASKET_BUTTON_ITEMID));
            }
            
            products = products.join();
        }

        for (i = 0; i < boxes.length; i++) {
            if (boxes[i].display) {
                tpl = templates[boxes[i].id];
                if (!tpl) {
                    document.body.appendChild(document.createComment(
                            'Yoochoose: Template for ' + boxes[i].id + ' recommendation box is not found!'));
                    console.log('Template for ' + boxes[i].id + ' recommendation box is not found!');
                    boxes[i].priority = 999;
                    continue;
                }

                boxes[i].template = tpl;
                boxes[i].priority = tpl.priority;
                fncName = 'YcTracking_jsonpCallback' + boxes[i].id;
                window[fncName] = fetchRecommendedProducts(boxes[i], url);
                YcTracking.callFetchRecommendedProducts(1, tpl.scenario, tpl.rows * tpl.columns, products, category, fncName);
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

            url += productIds.join();
            if (window.XMLHttpRequest) {
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
                                YcTracking.renderRecommendation(box, language);
                                attachFollowEvents(box);
                            });
                        }
                    }
                }
            };

            requestsSent++;
            xmlHttp.open('GET', url, true);
            xmlHttp.send();
        };
    }

    function attachFollowEvents(box){
        var elem = document.getElementsByClassName('rendered-' + box.id), i;

        for (i = 0; i < elem.length; i++) {
            elem[i].onclick = trackFollowEvent(box.products[i], box.template.scenario);
        }
    }

    function trackFollowEvent(product, scenario) {
        return function () {
            YcTracking.trackClickRecommended(1, product.id, scenario);
        };
    }

    if (!window['Handlebars']) {
        script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min.js';
        document.head.appendChild(script);
    }

    context.addEventListener('load', function () {
        var trackid;

        ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
        language = ycObject ? ycObject.lang : null;
        currentPage = ycObject ? ycObject.currentPage : null;
        trackid = ycObject ? ycObject.trackid : null;

        YcTracking.trackLogin(trackid);
        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
        hookLogoutHandler(trackid);
        YcTracking.hookSearchingHandler(language);
        fetchRecommendations();
    }, false);
}

