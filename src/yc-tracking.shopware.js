/* global YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        ycObject = null,
        trackId = null,
        language = null,
        responsesCount = 0,
        requestsSent = 0,
        templates = YC_RECO_TEMPLATES;

    function getCategoriesFromBreadcrumb() {
        var aaa = document.getElementById('breadcrumb').getElementsByTagName('a'),
            i = 0, 
            category = [];

        for (; i < aaa.length; i++) {
            category.push(aaa[i].text.trim());
        }

        return category.join('/');
    }

    function getArticleCategories() {
        var articleBox = document.getElementById('buybox'),
            metaTags = articleBox.getElementsByTagName('meta'), 
            i, category = '';

        for (i = 0; i < metaTags.length; i++) {
            if (metaTags[i].getAttribute('itemprop') === 'category') {
                category = metaTags[i].getAttribute('content').replace(/ > /g, '/');
                break;
            }
        }

        return category;
    }

    function trackClickAndRate() {
        var itemId = context['yc_articleId'] ? context['yc_articleId'] : null,
            currentPage = ycObject ? ycObject.currentPage : null,
            category,
            comments,
            form,
            rating;

        if (currentPage === 'product' && itemId) {
            category = getArticleCategories();
            YcTracking.trackClick(1, itemId, category, language);

            // by default, rating is done when user evaluates product
            comments = document.getElementById('comments');
            form = comments ? comments.getElementsByTagName('form') : null;
            if (form && form.length === 1) {
                form = form[0];
                rating = form.getElementsByTagName('select');
                if (rating && rating[0].name === 'sVoteStars') {
                    form.onsubmit = function () {
                        YcTracking.trackRate(1, itemId, rating[0].value * 10, language);
                    };
                }
            }
        }
    }

    function hookBasketHandlers() {
        // in widget: <a href="..shopurl../checkout/addArticle/sAdd/ITEMID" ...>...</a>
        // on product details form: <form name="sAddToBasket" action="..shopurl../checkout/addArticle" ...>
        var anchors = document.getElementsByTagName('a'),
            btn = document.getElementById('basketButton'),
            i,
            clickHandler = function (e) {
                var input = e.target.parentNode.querySelector('[name="yc_articleId"]'),
                    parts = e.target.href.split('/'),
                    itemId = parts[parts.length - 1];

                YcTracking.trackBasket(1, input ? input.value : itemId, document.location.pathname, language);
            };
        for (i = 0; i < anchors.length; i += 1) {
            if (/checkout\/addArticle\/sAdd/i.test(anchors[i].href)) {
                anchors[i].onclick = clickHandler;
            }
        }

        if (document.body.className === 'ctl_detail' && btn) {
            btn.onclick = function (e) {
                var form = e.target.parentNode.parentNode,
                    inputs = form.getElementsByTagName('input'),
                    itemId = context['yc_articleId'] ? context['yc_articleId'] : null,
                    i;

                if (!itemId && inputs) {
                    for (i = 0; i < inputs.length; i += 1) {
                        if (inputs[i].name === 'sAdd') {
                            itemId = inputs[i].value;
                            break;
                        }
                    }
                }

                if (itemId) {
                    YcTracking.trackBasket(1, itemId, document.location.pathname, language);
                }
            };
        }
    }

    function trackBuy() {
        var container = document.getElementById('yc-buy-items'),
            i;
        if (document.body.className === 'ctl_checkout' && container) {
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
            category = null;

        if (!boxes || boxes.length === 0 || !url) {
            return;
        }

        if (currentPage === 'product') {
            category = getArticleCategories();
        } else if (currentPage === 'cart') {
            category = document.location.pathname;
        } else if (currentPage === 'category') {
            category = getCategoriesFromBreadcrumb();
        }

        if (currentPage === 'category') {
            elements = document.getElementsByName('yc_articleId');
            for (i = 0; i < elements.length; i++) {
               products.push(elements[i].value);
            }

            products = products.join();
        }

        for (i = 0; i < boxes.length; i++) {
            tpl = templates[boxes[i].id];

            if (tpl && tpl.enabled) {
                boxes[i].template = tpl;
                boxes[i].title = tpl.title;
                boxes[i].priority = tpl.priority;
                fncName = 'YcTracking_jsonpCallback' + boxes[i].id;
                window[fncName] = fetchRecommendedProducts(boxes[i], url);
                YcTracking.callFetchRecommendedProducts(1, tpl.scenario, tpl.rows * tpl.columns, products, category, fncName);
            } else {
                document.getElementsByTagName('body')[0].innerHTML += 
                        '<!-- Yoochoose: Template for ' + boxes[i].id + ' recommendation box is not found! -->';
                console.log('Template for ' + boxes[i].id + ' recommendation box is not found!');
                boxes[i].priority = 999;
                continue;
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

            url += '?productIds=' + productIds.join();
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
                                YcTracking.renderRecommendation(box);
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
        var trackLogout,
            script;

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

        if (!window['Handlebars']) {
            script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min.js';
            document.getElementsByTagName('head')[0].appendChild(script);
        }

        trackClickAndRate();
        hookBasketHandlers();
        trackBuy();
        fetchRecommendations();
    };
}
