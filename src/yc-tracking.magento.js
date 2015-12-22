/* global Mage, YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        document = context.document,
        templates = YC_RECO_TEMPLATES,
        ycObject,
        Mage = context['Mage'],
        currentPage,
        itemType,
        script,
        language,
        requestsSent = 0,
        responsesCount = 0;
    
    function categoryFromBreadcrumb() {
        var breadcrumbs = document.getElementsByClassName('breadcrumbs'),
            category = '',
            list, i;

        if (breadcrumbs) {
            list = breadcrumbs[0].children[0].children;
            for (i = 0; i < list.length; i++) {
                if (list[i].className.indexOf('category') !== -1) {
                    category += list[i].children[0].innerHTML + '/';
                }
            }
        }

        return category;
    }

    function trackClickAndRate() {
        var addToCartForm = document.getElementById('product_addtocart_form'),
            itemId = ycObject ? ycObject.products : null,
            category = '',
            reviewForm = document.getElementById('review-form');

        if (addToCartForm) {
            if (!itemId) {
                if (addToCartForm.product) {
                    itemId = addToCartForm.product.value;
                }
            }

            // category is only stored in breadcrumbs
            category = categoryFromBreadcrumb();
        }

        if (itemId && currentPage === 'product') {
            YcTracking.trackClick(itemType, itemId, category, language);

            if (reviewForm) {
                reviewForm.onsubmit = function () {
                    var getRatings = function (elements, sub) {
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
        var addToCartForm = document.getElementById('product_addtocart_form');

        override('setLocation');
        override('setPLocation');

        if (context['addWItemToCart']) {
            var oldAddWItemToCart = context.addWItemToCart;
            context.addWItemToCart = function (itemId) {
                YcTracking.trackBasket(itemType, itemId, document.location.pathname, language);
                oldAddWItemToCart(itemId);
            };
        }

        if (context['addAllWItemsToCart']) {
            var oldAddAllWItemsToCart = context.addAllWItemsToCart;
            context.addAllWItemsToCart = function () {
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
            };
        }

        if (addToCartForm) {
            attachSubmitAddToCartForm(addToCartForm);
        }

        if (context['productAddToCartForm']) {
            attachSubmitAddToCartForm(context['productAddToCartForm'].form);
        }

        function override(func) {
            if (context[func]) {
                var oldFunc = context[func];
                context[func] = function (url) {
                    trackBasketFromUrl(url);
                    oldFunc(url);
                };
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
                    processForm = function () {
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
        var orders = ycObject ? ycObject.orderData : null,
            order, i;

        if (orders) {
            for (i = 0; i < orders.length; i++) {
                order = orders[i];
                if (order) {
                    YcTracking.trackBuy(itemType, parseInt(order['id']), parseInt(order['quantity']),
                        parseFloat(order['price']), order['currency'], language);
                }
            }
        }
    }

    function hookLogoutHandler(trackid) {
        if (!trackid && (typeof YcTracking.getUserId() === 'number')) {
            YcTracking.resetUser();
        }
    }

    function fetchRecommendations() {
        var boxes = ycObject ? ycObject.boxes : null,
            tpl,
            url = document.location.origin + (Mage ? Mage.Cookies.path : ''),
            fncName,
            category = null;

        if (!boxes) {
            return;
        }

        url = ((url[url.length - 1] === '/') ? url.slice(0, -1) : url) + '/yoochoose/products/index/?productIds=';
        if (currentPage === 'product' || currentPage === 'category') {
            category = categoryFromBreadcrumb();
        } else if (currentPage === 'cart') {
            category = document.location.pathname;
        }

        if (currentPage === 'category') {
            boxes[0].title =  boxes[0].title.replace(/%/g, category);
        }

        for (var i = 0; i < boxes.length; i++) {
            if (boxes[i].display) {
                tpl = templates[boxes[i].id];
                if (!tpl) {
                    document.body.appendChild(document.createComment(
                        'Yoochoose: Template for ' + boxes[i].id + ' recommendation box is not found!'));
                    context.console.log('Template for ' + boxes[i].id + ' recommendation box is not found!');
                    boxes[i].priority = 999;
                    continue;
                }

                boxes[i].template = tpl;
                boxes[i].priority = tpl.priority;
                fncName = 'YcTracking_jsonpCallback' + boxes[i].id;
                context[fncName] = fetchRecommendedProducts(boxes[i], url);
                YcTracking.callFetchRecommendedProducts(itemType, tpl.scenario, tpl.rows * tpl.columns, ycObject.products, category, fncName);
            }
        }
    }

    function trackFollowEvent(product, scenario) {
        var itemType = ycObject ? ycObject.itemType : null;

        return function () {
            YcTracking.trackClickRecommended(itemType, product.entity_id, scenario);
        };
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
                                    if (idHistory.indexOf(item.entity_id) === -1) {
                                        currentBox.push(item);
                                    } 
                                });

                                //out of unique products, take first N products
                                box.products = currentBox.slice(0, box.template.rows * box.template.columns);

                                //add Ids of N selected products, so they wouldn't have duplicates
                                box.products.forEach(function (item) {
                                    idHistory.push(item.entity_id);
                                    renderedIds.push(item.entity_id);
                                });

                                YcTracking.trackRendered(itemType, renderedIds);
                                YcTracking.renderRecommendation(box, language, trackFollowEvent, 'url_path');
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

    if (!context['Handlebars']) {
        script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min.js';
        document.head.appendChild(script);
    }

    context.addEventListener('load', function () {
        var trackid;

        ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
        trackid = ycObject ? ycObject.trackid : null;
        language = ycObject ? ycObject.language : null;
        itemType = ycObject ? ycObject.itemType : null;
        currentPage = ycObject ? ycObject.currentPage : null;

        YcTracking.trackLogin(trackid);
        trackClickAndRate();
        hookBasketHandlers();
        hookLogoutHandler(trackid);
        trackBuy();
        YcTracking.hookSearchingHandler(language);
        fetchRecommendations();
    }, false);
}
