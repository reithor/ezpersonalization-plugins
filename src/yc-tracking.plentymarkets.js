/* global YC_RECO_TEMPLATES */

function initYcTrackingModule(context) {

    'use strict';

    var YcTracking = context.YcTracking,
        lang = 'de';

    function getCategory() {
        var category = location.pathname.split('/');

        return category.slice(1, category.length - 2).join('/').replace(/\-/g, ' ');
    }

    function trackClick() {
        var product = document.getElementsByName('ArticleID')[0],
            category = '';

        if (product) {
            category = getCategory();
            YcTracking.trackClick(1, product.value, category, lang);
        }
    }

    function hookBasketEvent() {
        var basketButton = document.getElementsByClassName('singlekaufen')[0],
            product = document.getElementsByName('ArticleID')[0],
            oldFunction,
            category;

        if (basketButton && product) {
            oldFunction = basketButton.onclick;
            category = getCategory();
            basketButton.onclick = function (e) {
                YcTracking.trackBasket(1, product.value, category, lang);
                if (oldFunction) {
                    oldFunction.call(this, e);
                }
            };
        }
    }

    function trackBuyHandle() {
        var pathItems = location.pathname.split('/'),
            cartItems,
            testInterval;

        // if order is finished successfully
        if (pathItems[2] === '-OrderShowQQMakeOrder'){
            // get product data from localStorage and track items
            cartItems = JSON.parse(localStorage.getItem('cartItems'));
            if (cartItems) {
                cartItems.forEach(function (item) {
                    YcTracking.trackBuy(1, item.id, item.quantity, item.price, item.currency, lang);
                });
            }

            localStorage.removeItem('cartItems');
        }
        // if on last step before buying
        else if (pathItems[1] === 'checkout') {
            // collect data about products from page
            testInterval = setInterval(function () {
                var data;
                if (document.getElementById('button_place_orderWebOrderOverview')) {
                    data = fetchDataFromPage();
                    localStorage.setItem('cartItems', JSON.stringify(data));
                    clearInterval(testInterval);
                }
            }, 250);
        }
        // any other page
        else {
            localStorage.removeItem('cartItems');
        }
    }

    function fetchDataFromPage() {
        var quantys = document.getElementsByClassName('ItemsQuantity'),
            prices = document.getElementsByClassName('ItemsDescriptionUnitPriceDetail'),
            ids = document.getElementsByClassName('ItemsDescriptionIDDetail'),
            result = [],
            currency,
            i;

        if (quantys.length === prices.length && prices.length === ids.length) {
            currency = prices[0].children[1].innerHTML;
            for (i = 0; i < prices.length; i++) {
                result.push({
                    'price': prices[i].children[0].innerHTML,
                    'quantity': quantys[i].children[0].innerHTML,
                    'id': ids[i].innerHTML.trim(),
                    'currency': currency
                });
            }

            console.log(result);
        }

        return result;
    }

    window.onload = function () {
        trackClick();
        hookBasketEvent();
        trackBuyHandle();
    };
}
