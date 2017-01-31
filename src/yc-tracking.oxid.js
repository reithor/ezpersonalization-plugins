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
            categories.push(crumbs[i][YC_BREADCRUMBS_VALUE].trim());
        }

        return categories.join('/');
    }

    function trackClickAndRate() {
        var category,
            itemId = ycObject ? ycObject.products : null,
            form;

        if (currentPage === 'product') {
            category = getCategoriesFromBreadcrumb();
            YcTracking.trackClick(itemType, itemId, category, language);

            form = document.querySelector(YC_ARTICLE_RATING_FORM_SELECTOR);
            if (form) {
                form.addEventListener('submit', function () {
                    var rating = parseInt(form.artrating.value);
                    YcTracking.trackRate(1, itemId, rating * 20, language);
                }, false);
            }
        }
    }

    function hookBasketHandlers() {
        var forms = document.querySelectorAll(YC_BASKET_FORMS_SELECTOR),
            links = document.querySelectorAll(YC_BASKET_LINKS_SELECTOR),
            tempId, i, category = getCategoriesFromBreadcrumb(),
            onClickLink = function (e) {
                var tempar = e.target.search.split('&');
                for (var i = 0; i < tempar.length; i++) {
                    var temp = tempar[i].split('=');
                    if (temp[0] == 'aid') {
                        tempId = temp[1];
                        break;
                    }
                }

                YcTracking.trackBasket(1, tempId, category, language);
            },
            onFormSubmit = function () {
                tempId = this.anid.value;
                YcTracking.trackBasket(1, tempId, category, language);
            };

        if (links) {
            for (i = 0; i < links.length; i++) {
                links[i].addEventListener('click', onClickLink);
            }
        }

        if (forms) {
            for (i = 0; i < forms.length; i++) {
                forms[i].form.addEventListener('submit', onFormSubmit);
            }
        }
    }

    function trackBuy() {
        var orders = ycObject ? ycObject.orders : null;

        if (currentPage === 'buyout' && orders) {
            orders.forEach(function (order) {
                YcTracking.trackBuy(itemType, order.itemId, order.quantity, order.price, order.currency, language);
            });
        }
    }

    function fetchRecommendations() {
        var products = ycObject ? ycObject.products : [],
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

        url = url.replace('http:', context.location.protocol) + 'Yoochoose/';
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

    function fetchRecommendedProducts(box, url) {
        return function (response) {
            var xmlHttp,
                productIds = [
                    'd86f775338da3228bec9e968f02e7551',
                    'd86236918e1533cccb679208628eda32',
                    '05848170643ab0deb9914566391c0c63',
                    'adc920f4cbfa739803058c663a4a00b9',
                    'adcb9deae73557006a8ac748f45288b4',
                    'f4f0cb3606e231c3fdb34fcaee2d6d04',
                    'f4f981b0d9e34d2aeda82d79412480a4',
                    '6b6ac464656c16c90d671721c93dc6ba',
                    '6b6099c305f591cb39d4314e9a823fc1',
                    '531b537118f5f4d7a427cdb825440922',
                    '6b66d82af984e5ad46b9cb27b1ef8aae',
                    'b563ab240dc19b89fc0349866b2be9c0'
                ];

            if (!response.hasOwnProperty('recommendationResponseList')) {
                return;
            }

            // response.recommendationResponseList.forEach(function (product) {
            //     productIds.push(product.itemId);
            // });

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

                        }
                    }
                }
            };

            requestsSent++;
            xmlHttp.open('GET', url + '?products=' + productIds.join(), true);
            xmlHttp.send();
        };
    }

    function trackFollowEvent(product, scenario) {
        return function () {
            YcTracking.trackClickRecommended(itemType, product.id, scenario);
        };
    }

    function searchInputModify() {
        //Removes search input label
        YC_SEARCH_FIELDS.forEach(function (el) {
            var label = document.querySelector('label[for="' + el.target[0].slice(1) + '"]');
            if (label) {
                label.style.display = 'none';
                document.querySelector(el.target[0]).placeholder = label.innerHTML.trim();
            }
        });
    }

    if (!context['Handlebars']) {
        script = document.createElement('script');
        script.src = YC_HANDLEBARS_CDN;
        document.head.appendChild(script);
    }

    context.addEventListener('load', function () {
        var trackId;

        ycObject = context['yc_config_object'] ? context['yc_config_object'] : null;
        trackId = ycObject ? ycObject.trackid : null;
        currentPage = ycObject ? ycObject.currentPage : null;
        language = ycObject ? ycObject.language : null;
        itemType = ycObject ? ycObject.itemType : null;

        YcTracking.trackLogin(trackId);
        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
        YcTracking.hookSearchingHandler(language);
        fetchRecommendations();

        if (YC_MODIFY_SEARCH) {
            searchInputModify();
        }
    }, false);
}