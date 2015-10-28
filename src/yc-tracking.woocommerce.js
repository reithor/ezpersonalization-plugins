/* global YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        templates = YC_RECO_TEMPLATES,
        requestsSent = 0,
        responsesCount = 0;

    function categoryFromBreadcrumb() {
        var breadcrumbs = document.getElementsByClassName('woocommerce-breadcrumb'),
            category = '',
            list, i;

        if (breadcrumbs && breadcrumbs.length) {
            list = breadcrumbs[0].children;
            for (i = 1; i < list.length; i++) {
                category += list[i].text + '/';
            }
        }

        return category;
    }

    function trackClickAndRate() {
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            itemId = ycObject ? ycObject.productIds : null,
            language = ycObject ? ycObject.lang : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            reviewForm = document.getElementById('commentform'),
            category = categoryFromBreadcrumb();

        if (currentPage === 'product' && itemId) {
            YcTracking.trackClick(1, itemId, category, language);

            if (reviewForm) {
                reviewForm.onsubmit = function (e) {
                    var rating = this.elements['rating'],
                        ratingValue;

                    if (rating) {
                        ratingValue = parseInt(rating.value);
                        YcTracking.trackRate(1, itemId, ratingValue * 20, language);
                    }
                };
            }
        }
    }

    function hookBasketHandlers() {
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            itemId = ycObject ? ycObject.productIds : null,
            elements = document.getElementsByClassName('add_to_cart_button'),
            i;

        for (i = 0; i < elements.length; i++) {
            trackBasketFunction(elements[i])();
        }

        if (currentPage === 'product') {
            trackBasketFunction(document.getElementsByClassName('single_add_to_cart_button')[0], itemId)();
        }
    }

    function trackBasketFunction(element, itemId) {
        return function () {
            var oldFunction = element.onclick,
                ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
                language = ycObject ? ycObject.lang : null;

            element.onclick = function (e) {
                itemId = itemId ? itemId : element.getAttribute('data-product_id');
                YcTracking.trackBasket(1, itemId, document.location.pathname, language);
                if (oldFunction) {
                    oldFunction();
                }
            };
        };
    }

    function trackBuy() {
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            language = ycObject ? ycObject.lang : null,
            orders = ycObject ? ycObject.orderData : null;

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
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            boxes = ycObject ? ycObject.boxes : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            products = ycObject ? ycObject.products : [],
            tpl, i,
            elements,
            url = window['yoochoose_ajax_script'].ajaxurl + '?action=yoochoose_products&productIds=',
            fncName,
            category = null;

        if (!boxes) {
            return;
        }

        if (currentPage === 'product' || currentPage === 'category') {
            category = categoryFromBreadcrumb();
        } else if (currentPage === 'cart') {
            category = document.location.pathname;
        }
        
        if (currentPage === 'category') {
            elements = document.getElementsByClassName('add_to_cart_button');
            for (i = 0; i < elements.length; i++) {
               products.push(elements[i].getAttribute('data-product_id'));
            }
            
            products = products.join();
        }

        for (i = 0; i < boxes.length; i++) {
            if (boxes[i].display) {
                tpl = templates[boxes[i].id];
                if (!tpl) {
                    document.getElementsByTagName('body')[0].innerHTML += 
                            '<!-- Yoochoose: Template for ' + boxes[i].id + ' recommendation box is not found! -->';
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
        var ycObject = context['yc_config_object'] ? context['yc_config_object'] : null,
            language = ycObject ? ycObject.lang : '';
        return function (response) {
            var xmlHttp,
                productIds = [];

            if (!response.hasOwnProperty('recommendationResponseList')) {
                return;
            }

            response.recommendationResponseList.forEach(function (product) {
                productIds.push(product.itemId);
            });

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

    window.onload = function () {
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            trackid = ycObject ? ycObject.trackid : null,
            script;

        if (!window['Handlebars']) {
            script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min.js';
            document.getElementsByTagName('head')[0].appendChild(script);
        }

        YcTracking.trackLogin(trackid);
        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
        hookLogoutHandler(trackid);
        fetchRecommendations();
    };
}

