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
            categories.push(crumbs[i].children[0][YC_BREADCRUMBS_VALUE].trim());
        }

        return categories.join('/');
    }
    
    function calculateBundlePrice(checkBoxes) {
        var updatedTotalPrice = 0,
            stionText = '',
            priceOfBundleItem,
            checked = 0,
            addToCartText1 = GLOBAL.document.querySelectorAll('.yc-recommendation-box' + YC_BUNDLE_ADDTOCART_ONE_SELECTOR),
            addToCartText2 = GLOBAL.document.querySelectorAll('.yc-recommendation-box' + YC_BUNDLE_ADDTOCART_TWO_SELECTOR),
            addToCartText3 = GLOBAL.document.querySelectorAll('.yc-recommendation-box' + YC_BUNDLE_ADDTOCART_THREE_SELECTOR);

        checkBoxes.forEach(function (checkBox){
            if (checkBox.checked) {
                checked++;
                stionText = checkBox.value.replace(/[0-9]/g, '');
                stionText = stionText.replace('.', '');
                priceOfBundleItem = checkBox.value.replace(/[^0-9]/g, '');
                updatedTotalPrice = updatedTotalPrice + parseInt(priceOfBundleItem);
            }
        });

        checked > 2 ? addToCartText3[0].style.display = 'block' : addToCartText3[0].style.display = 'none';
        checked === 2 ? addToCartText2[0].style.display = 'block' : addToCartText2[0].style.display = 'none';
        checked <= 1 ? addToCartText1[0].style.display = 'block' : addToCartText1[0].style.display = 'none';

        updatedTotalPrice = updatedTotalPrice / 100;
        return stionText + Number(updatedTotalPrice).toFixed(2);
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
                allBoxes.push({
                    'id': 'bundle3'
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

        url = url.replace('http:', context.location.protocol) + 'ycproductexport/';
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

    function hookTrackFollowEvent() {
        allBoxes.forEach(function (box) {
            var template = box ? box.template : null,
                elements = template ? GLOBAL.document.querySelectorAll(template.target + ' .yc-recommendation-box') : [];
            [].forEach.call(elements, function (elem) {
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

    function updateTotalPrice(checkBoxes, priceHolder) {
        return function () {
            priceHolder.innerHTML = calculateBundlePrice(checkBoxes);
        }
    }

    function fetchRecommendedProducts(box, url) {
        return function (response) {
            var div = document.createElement('div'),
                element = GLOBAL.document.querySelectorAll(YC_VIEWERS_SELECTOR),
                returnForm = (box.id == 'bundle3') ? 'bundle' : 'standard',
                viewerElement = GLOBAL.document.querySelectorAll('#yc-viewers-number'),
                translate = box.template.consts.viewers,
                language_code = language ? language.substr(0, language.indexOf('-')) : '',
                viewersNumber = '',
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
                
                if (response.contextItems.length > 0) {
                    viewersNumber = response.contextItems[0].viewers;

                    if (viewersNumber > 0 && viewerElement.length === 0) {
                        div.innerHTML = viewersNumber;
                        div.id = 'yc-viewers-number';
                        div.innerHTML = div.innerHTML + ' ' + translate[language_code];
                        element[0].firstElementChild.appendChild(div);
                    }
                }
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
                                    idHistory.push(item.id);
                                    renderedIds.push(item.id);
                                    item.totalItems = box.products.length === 3 ? '3' : false;
                                    box.response.recommendationItems.forEach(function (product) {
                                        if (item.id == product.itemId || item.sentId == product.itemId) {
                                            item.links = product.links;
                                            YcTracking.trackRenderedV2(product.links.rendered, language);
                                        }
                                    });
                                });
                                
                                YcTracking.renderRecommendationV2(box, language, trackFollowEventV2, false);
                                
                                box.products.forEach(function (item) {
                                    if (item.jsonSwatchConfig && item.jsonConfig) {
                                        require(['jquery', 'jquery/ui', 'Magento_Swatches/js/swatch-renderer'], function ($) {
                                            $('.swatch-opt-' + item.id).SwatchRenderer({
                                                selectorProduct: '.product-item-details',
                                                onlySwatches: true,
                                                enableControlLabel: item.enableControlLabel ? item.enableControlLabel : false,
                                                numberToShow: item.numberToShow ? item.numberToShow : 16,
                                                jsonConfig: JSON.parse(item.jsonConfig),
                                                jsonSwatchConfig: JSON.parse(item.jsonSwatchConfig),
                                                mediaCallback: item.mediaCallback
                                            });
                                        });
                                    }
                                });
                            });

                            hookTrackFollowEvent();

                            if (box.template.scenario !== 'bundle3') {
                                hookRecommendedBasketHandlers();
                            } else {
                                hookBundleUpdatePriceHandlers();
                                hookBundleBasketHandlers(box.template.target);
                            }
                        }
                    }
                }
            };

            requestsSent++;
            xmlHttp.open('GET', url   + '?type=' + returnForm + '&productIds=' +  productIds.join(), true);
            xmlHttp.send();
        };
    }

    function hookBundleUpdatePriceHandlers() {
        var checkBoxes = GLOBAL.document.querySelectorAll('.yc-recommendation-box ' + YC_BUNDLE_CHECKBOX_SELECTOR),
            priceHolder = GLOBAL.document.querySelector('#yc-total-price-bundle');

        checkBoxes[0].disabled = true;
        priceHolder.innerHTML = calculateBundlePrice(checkBoxes);

        checkBoxes.forEach(function (checkBox) {
            checkBox.addEventListener('change', updateTotalPrice(checkBoxes, priceHolder));
        });
    }

    function hookBundleBasketHandlers(target) {
        var addToCart = GLOBAL.document.querySelectorAll(target + ' .yc-recommendation-box' + YC_BUNDLE_ADDTOCART_SELECTOR);

        if (addToCart[0]) {
            addToCart[0].addEventListener('click', function (e) {
                var promises = [],
                    checkboxes = GLOBAL.document.querySelectorAll('.yc-recommendation-box' + YC_BUNDLE_CHECKBOX_SELECTOR),
                    forms = GLOBAL.document.querySelectorAll('.yc-recommendation-box' + YC_BUNDLE_FORM_SELECTOR);

                e.currentTarget.disabled = true;
                e.currentTarget.innerHTML = '<span>Adding...</span>';

                checkboxes.forEach(function (checkBox) {
                    var form = forms,
                        promise = null,
                        xmlHttp = new XMLHttpRequest(),
                        categoryPath = getCategoriesFromBreadcrumb(),
                        productId,
                        params = '';

                    if (checkBox.checked) {
                        forms.forEach(function (f) {
                            if (checkBox.attributes['data-product-id'].value === f.attributes['data-product-id'].value) {
                                form = f;
                            }
                        });

                        if (form) {
                            productId = form.attributes['data-product-id'].value;
                            promise = new Promise(function (resolve, reject) {

                                YcTracking.trackBasket(itemType, productId, categoryPath, language);

                                xmlHttp.onreadystatechange = function () {
                                    if (xmlHttp.readyState === 4) {
                                        if (xmlHttp.status === 200) {
                                            resolve();
                                        } else {
                                            reject();
                                        }
                                    }
                                };

                                xmlHttp.open('POST', form.action, true);
                                xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                                for (var i = 0; i < form.children.length; i++) {
                                    if (form.children[i].tagName === 'INPUT') {
                                        params += [form.children[i].name] + '=' + form.children[i].value + '&';
                                    }
                                }

                                params = params.substring(0, params.length - 1);
                                xmlHttp.send(params);
                            });

                            promises.push(promise);
                        }
                    }
                });

                Promise.all(promises).then(function () {
                    window.location.reload();
                });
            });
        }
    }

    function trackFollowEvent(product, scenario) {
        return function () {
            YcTracking.trackClickRecommended(itemType, product.id, scenario);
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
                enableSearch;

            ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
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
            if (enableSearch == 1) {
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