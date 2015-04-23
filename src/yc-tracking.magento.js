/* global Mage, YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        templates = YC_RECO_TEMPLATES;
    
    function categoryFromBreadcrumb() {
        var breadcrumbs = document.getElementsByClassName('breadcrumbs'),
            category = '',
            list, i;

        if (breadcrumbs) {
            list = breadcrumbs[0].children[0].children;
            for (i = 0; i < list.length; i++) {
                if (list[i].className.indexOf('category') !== -1) {
                    category += list[i].children[0].text + '/';
                }
            }
        }
        
        return category;
    }

    function trackClickAndRate() {
        var addToCartForm = document.getElementById('product_addtocart_form'),
            ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            itemType = ycObject ? ycObject.itemType : null,
            language = ycObject ? ycObject.language : null,
            itemId = ycObject ? ycObject.articleId : null,
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

        if (itemId) {
            YcTracking.trackClick(itemType, itemId, category, language);

            if (reviewForm) {
                reviewForm.onsubmit = function (e) {
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
        var addToCartForm = document.getElementById('product_addtocart_form'),
            ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            itemType = ycObject ? ycObject.itemType : null,
            language = ycObject ? ycObject.language : null;

        override('setLocation');
        override('setPLocation');

        if (window['addWItemToCart']) {
            var oldAddWItemToCart = window.addWItemToCart;
            window.addWItemToCart = function (itemId) {
                YcTracking.trackBasket(itemType, itemId, document.location.pathname, language);
                oldAddWItemToCart(itemId);
            };
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
            };
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
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            itemType = ycObject ? ycObject.itemType : null,
            language = ycObject ? ycObject.language : null,
            orders = ycObject ? ycObject.orderData : null,
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

    function hookLogoutHandler() {
        var container = document.getElementById('header-account'),
            anchors = container ? container.getElementsByTagName('a') : null,
            i;

        if (anchors) {
            for (i = 0; i < anchors.length; i++) {
                if (/customer\/account\/logout/i.test(anchors[i].href)) {
                    anchors[i].onclick = function () {
                        YcTracking.resetUser();
                    };
                }
            }
        }
    }
    
    function createJsonpCallbackFnc(box) {
        return function (response) {
            var xmlHttp,
                products,
                productIds = [],
                url = location.origin + Mage.Cookies.path + '/yoochoose/products/index/?productIds=';

            if (!response.hasOwnProperty('recommendationResponseList')) {
                console.log(response);
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
                if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                    products = JSON.parse(xmlHttp.responseText);
                    renderRecommendation(box, products);
                }
            };

            xmlHttp.open('GET', url, true);
            xmlHttp.send();
        };
    }

    function fetchRecommendations() {
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            itemType = ycObject ? ycObject.itemType : null,
            boxes = ycObject ? ycObject.boxes : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            tpl,
            fncName,
            category = null;

        if (currentPage === 'product') {
            category = categoryFromBreadcrumb();
        } else if (currentPage === 'cart') {
            category = document.location.pathname;
        }
        
        if (boxes !== null) {
            for (var i = 0; i < boxes.length; i++) {
                if (boxes[i].display) {
                    tpl = templates[boxes[i].id];
                    fncName = 'YcTracking_jsonpCallback' + boxes[i].id;
                    window[fncName] = createJsonpCallbackFnc(boxes[i]);

                    YcTracking.fetchRecommendedProducts(itemType, tpl.scenario, tpl.rows * tpl.columns, ycObject.products, category, fncName);
                }
            }
        }
    }

    function renderRecommendation(box, products) {
        var parser = new DOMParser(),
            doc = parser.parseFromString(templates[box.id].html_template, 'text/xml'),
            section = doc.firstChild,
            productDiv = section.childNodes[1],
            title = productDiv.getElementsByClassName('yc_title')[0],
            id = productDiv.getElementsByClassName('yc_id')[0],
            price = productDiv.getElementsByClassName('yc_price')[0],
            currency = productDiv.getElementsByClassName('yc_currency')[0],
            img = productDiv.getElementsByTagName('img')[0],
            elem = null;

        section.childNodes[0].innerHTML = box.title;
        section.removeChild(section.childNodes[1]);
        currency.innerHTML = products.currency;

        products.products.forEach(function (entry) {
            id.innerHTML = entry.entity_id;
            title.innerHTML = entry.name;
            title.attributes.href.value = entry.url_path;
            price.innerHTML = entry.price;
            img.attributes.src.value = entry.thumbnail;
            section.appendChild(productDiv.cloneNode(true));
        });

        elem = document.getElementsByClassName(templates[box.id].target)[0];
        elem.appendChild(section);
    }

    window.onload = function () {
        var ycObject = window['yc_config_object'] ? window['yc_config_object'] : null,
            trackid = ycObject ? ycObject.trackid : null;
        
        YcTracking.trackLogin(trackid);
        trackClickAndRate();
        hookBasketHandlers();
        hookLogoutHandler();
        trackBuy();
        fetchRecommendations();
    };
}
