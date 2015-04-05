/*! yc-tracking - v0.9.0 - 2015-04-05 - Soprex */
;(function (global) {

/**
 * Reference to global object.
 */
var Fn = Function, GLOBAL = new Fn('return this')();

/**
 * Init wrapper for the core module.
 * @param {Object} context The Object that the YcTracking gets attached to in yc-tracking.init.js.
 * If the YcTracking was not loaded with an AMD loader such as require.js, this is the global Object.
 */
function initYcTrackingCore(context) {
    'use strict';

    /**
     * Constructor for the YcTracking Object.
     *
     * @constructor
     */
    var YcTracking = function () {

        /**
         * Holds customer Id. This should be automatically populated on server from request to js when serving js.
         * Request is in format https://cdn.yoochoose.net/v1/100001/tracking.js, where 100001 is customer id.
         *
         * @private
         * @readonly
         * @type {number}
         */
        var customerId = 904,

            /**
             * Holds path to event tracking api with customer id.
             *
             * @private
             * @readonly
             * @type {string}
             */
            eventHost = '//event.yoochoose.net/api/' + customerId,

            /**
             * Duration of session in minutes.
             *
             * @private
             * @readonly
             * @type {number}
             */
            sessionDuration = 30,

            /**
             * Name of local storage store.
             *
             * @private
             * @readonly
             * @type {string}
             */
            storeId = 'YCStore',

            /**
             * Checks if local storage can be used to store user data.
             * https://github.com/Modernizr/Modernizr/blob/master/feature-detects/storage/localstorage.js
             *
             * @private
             * @returns {boolean}
             */
            _canUseLocalStorage = function () {
                var mod = 'yc_localStorageTest';
                try {
                    GLOBAL.localStorage.setItem(mod, mod);
                    GLOBAL.localStorage.removeItem(mod);
                    return true;
                } catch (e) {
                    return false;
                }
            },

            /**
             * Gets user data from local storage.
             *
             * @private
             * @returns {object}
             */
            _getLocalStore = function () {
                return _canUseLocalStorage() ? JSON.parse(GLOBAL.localStorage.getItem(storeId)) : null;
            },

            /**
             * Saves user data to local storage.
             *
             * @private
             * @param {object} value - Data to store.
             */
            _setLocalStore = function (value) {
                if (_canUseLocalStorage()) {
                    GLOBAL.localStorage.setItem(storeId, JSON.stringify(value));
                } else {
                    throw 'Local storage not supported!';
                }
            },

            /**
             * Generates unique identifier.
             *
             * @private
             * @returns {string}
             */
            _generateGuid = function () {
                /**
                 * @returns {string}
                 */
                var S4 = function () {
                    return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
                };
                return (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4());
            },

            /**
             * Gets or sets user identifier using local store.
             * Stores user identifier if supplied through parameter userId.
             *
             * @private
             * @param {string} [userId] - User identifier
             * @returns {string}
             */
            _userIdFromLocalStorage = function (userId) {
                var userData = _getLocalStore(),
                    expirationDate = new Date();

                expirationDate.setMinutes(expirationDate.getMinutes() + sessionDuration);
                if (!userId) {
                    if (userData && userData.expires > new Date().getTime()) {
                        userId = userData.userId;
                    } else {
                        userId = _generateGuid();
                    }
                }

                _setLocalStore({ userId: userId, expires: expirationDate.getTime() });
                return userId;
            },

            /**
             * Gets or sets user identifier using cookie.
             * Stores user identifier if supplied through parameter userId.
             *
             * @private
             * @param {string} [userId] - User identifier
             * @returns {string}
             */
            _userIdFromCookie = function (userId) {
                var cookieId = 'YCCookie',
                    cookieValue = GLOBAL.document.cookie,
                    expirationDate = new Date(),
                    c_start = cookieValue.indexOf(' ' + cookieId + '='),
                    c_end;

                expirationDate.setMinutes(expirationDate.getMinutes() + sessionDuration);
                if (!userId) {
                    if (c_start === -1) {
                        c_start = cookieValue.indexOf(cookieId + '=');
                    }

                    if (c_start === -1) {
                        userId = _generateGuid();
                    } else {
                        c_start = cookieValue.indexOf('=', c_start) + 1;
                        c_end = cookieValue.indexOf(';', c_start);
                        if (c_end === -1) {
                            c_end = cookieValue.length;
                        }

                        userId = decodeURI(cookieValue.substring(c_start, c_end));
                    }
                }

                GLOBAL.document.cookie = cookieId + '=' + encodeURI(userId) + '; expires=' + expirationDate.toUTCString() + '; path=/';
                return userId;
            },

            /**
             * Gets or sets current user identifier. It tries to use local storage if browser supports it.
             * If not, cookie is used.
             * If userId is set, local user identifier will be updated.
             *
             * @private
             * @param {string} [userId] - User identifier to set.
             * @returns {string} User identifier.
             */
            _userId = function (userId) {
                if (_canUseLocalStorage()) {
                    return _userIdFromLocalStorage(userId);
                }

                return _userIdFromCookie(userId);
            },

            /**
             * Executes call to remote server by placing pixel image.
             *
             * @private
             * @param {string} url - Url to call.
             */
            _executeEventCall = function (url) {
                // event tracking is using pixel method because server does not support JSONP response
                var img = new Image(1, 1);
                img.src = eventHost + url;
            };

        /**
         * Resets user identifier. Should be called when user logs out.
         */
        this.resetUser = function () {
            return _userId(_generateGuid());
        };

        /**
         * Gets current user identifier used in tracking calls.
         *
         * @returns {string}
         */
        this.getUserId = function () {
            return _userId();
        };

        /**
         * Method for tracking Click event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {string} [categoryPath] The forward slash separated path of categories of the item.
         * @param {string} language
         * @return {YcTracking} This object's instance.
         */
        this.trackClick = function (itemTypeId, itemId, categoryPath, language) {
            var url = '/click/' + _userId() + '/' + itemTypeId + '/' + itemId;
            
            url += '?categorypath=' + (categoryPath ? encodeURIComponent(categoryPath) : '');
            url += '&lang=' + language;

            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Rate event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {number} rating The rating a user gives an item. Value range: [0-100]
         * @param {string} language
         * @return {YcTracking} This object's instance.
         */
        this.trackRate = function (itemTypeId, itemId, rating, language) {
            var url = '/rate/' + _userId() + '/' + itemTypeId + '/' + itemId + '?rating=' + rating + '&lang=' + language;
            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Basket event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {string} [categoryPath] The category path from where the item is placed into the shopping cart.
         * @param {string} language
         * @return {YcTracking} This object's instance.
         */
        this.trackBasket = function (itemTypeId, itemId, categoryPath, language) {
            var url = '/basket/' + _userId() + '/' + itemTypeId + '/' + itemId;

            url += '?categorypath=' + (categoryPath ? encodeURIComponent(categoryPath) : '');
            url += '&lang=' + language;

            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Buy event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {number} quantity The number of items the user bought.
         * @param {number} price A price in decimal format for a <b>single item</b>.
         *      If the price has a decimal part, the dot must be used.
         * @param {string} currencyCode
         * @param {string} language
         * @return {YcTracking} This object's instance.
         */
        this.trackBuy = function (itemTypeId, itemId, quantity, price, currencyCode, language) {
            var url = '/buy/' + _userId() + '/' + itemTypeId + '/' + itemId +
                '?fullprice=' + (price + '').replace(',', '.') + currencyCode + '&quantity=' + quantity
                + '&lang=' + language;

            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Login (Transfer) event.
         *
         * @param {string} targetUserId - New user ID
         * @return {YcTracking} This object's instance.
         */
        this.trackLogin = function (targetUserId) {
            var userID = _userId();
            if (targetUserId && userID !== targetUserId) {
                _executeEventCall('/login/' + userID + '/' + encodeURIComponent(targetUserId));
                _userId(targetUserId, true);
            }

            return this;
        };

        /**
         * Method for tracking Rendered event.
         *
         * @param {number} itemTypeId
         * @param {string[]} itemIds - Array of item identifiers (string).
         * @return {YcTracking} This object's instance.
         */
        this.trackRendered = function (itemTypeId, itemIds) {
            var url = '/rendered/' + _userId() + '/' + itemTypeId + '/';

            if (!Array.isArray(itemIds)) {
                itemIds = [itemIds];
            }

            _executeEventCall(url + itemIds.join(','));
            return this;
        };

        /**
         * Method for tracking Click Recommended (Follow) event.
         *
         * @param {number} itemTypeId
         * @param {number} itemId
         * @param {string} scenario Name of the scenario, where recommendations originated from.
         * @return {YcTracking} This object's instance.
         */
        this.trackClickRecommended = function (itemTypeId, itemId, scenario) {
            var url = '/clickrecommended/' + _userId() + '/' + itemTypeId + '/' + itemId + '?scenario=' + encodeURIComponent(scenario);

            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Consume event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {number} percentage It defines how much of an item was consumed,
         *      e.g. an article was read only by 20%, a movie was watched by 90% or someone finished 3/4
         *      of all levels of a game. Value range: [0-100]
         * @return {YcTracking} This object's instance.
         */
        this.trackConsume = function (itemTypeId, itemId, percentage) {
            _executeEventCall('/consume/' + _userId() + '/' + itemTypeId + '/' + itemId + (percentage ? '?percentage=' + percentage : ''));
            return this;
        };

        /**
         * Method for tracking Blacklist event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @return {YcTracking} This object's instance.
         */
        this.trackBlacklist = function (itemTypeId, itemId) {
            var url = '/blacklist/' + _userId() + '/' + itemTypeId + '/' + itemId;
            _executeEventCall(url);
            return this;
        };

        return this;
    };

    var YcValidator = {

        /**
         * Validates integer number.
         *
         * @param {*} param - Variable to validate. Numbers with exponent (e.g. 12e3) will not pass validation.
         * @param {boolean} throwException - If set to true, exception will be thrown if variable is not valid.
         * @returns {*|boolean}
         */
        validateInt: function (param, throwException) {
            var result = /^-?\d+$/.test(param);
            if (!result && throwException) {
                throw 'Param is not valid. Expected integer.';
            }

            return result;
        },

        /**
         * Validates floating point number number. Numbers with exponent (e.g. 12.4e3) will not pass validation.
         *
         * @param {*} param - Variable to validate.
         * @param {boolean} throwException - If set to true, exception will be thrown if variable is not valid.
         * @returns {*|boolean}
         */
        validateFloat: function (param, throwException) {
            var result = /^-?\d*(\.\d*)?$/.test(param);
            if (!result && throwException) {
                throw 'Param is not valid. Expected number.';
            }

            return result;
        }
    };

    context.YcTracking = new YcTracking();
    context.YcValidator = YcValidator;
}

function initYcTrackingModule(context) {

    'use strict';

    function trackClickAndRate() {
        var addToCartForm = document.getElementById('product_addtocart_form'),
            itemType = window['yc_itemType'] ? window['yc_itemType'] : null,
            language = window['yc_language'] ? window['yc_language'] : null,
            itemId = window['yc_articleId'] ? window['yc_articleId'] : null,
            category = '',
            breadcrumbs, list,
            reviewForm,
            form = document.getElementById('review-form'),
            i;
        if (addToCartForm) {
            if (!itemId) {
                if (addToCartForm.product) {
                    itemId = addToCartForm.product.value;
                }
            }

            // category is only stored in breadcrumbs
            breadcrumbs = document.getElementsByClassName('breadcrumbs');
            if (breadcrumbs) {
                list = breadcrumbs[0].children[0].children;
                for (i = 0; i < list.length; i++) {
                    if (list[i].className.indexOf('category') !== -1) {
                        category += list[i].children[0].text + '/';
                    }
                }
            }
        }

        if (itemId) {
            YcTracking.trackClick(itemType, itemId, category, language);

            if (reviewForm) {
                reviewForm.onsubmit = function (e) {
                    var getRatings = function(elements, sub) {
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
            itemType = window['yc_itemType'] ? window['yc_itemType'] : null,
            language = window['yc_language'] ? window['yc_language'] : null;

        override('setLocation');
        override('setPLocation');

        if (window['addWItemToCart']) {
            var oldAddWItemToCart = window.addWItemToCart;
            window.addWItemToCart = function (itemId) {
                YcTracking.trackBasket(itemType, itemId, document.location.pathname, language);
                oldAddWItemToCart(itemId);
            }
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
            }
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
                }
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
                processForm = function() {
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
        var orders, order, i,
            language = window['yc_language'] ? window['yc_language'] : null;
        if (window['yc_orderData']) {
            orders = window['yc_orderData'];
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
                    }
                }
            }
        }
    }

    var YcTracking = context.YcTracking;

    window.onload = function () {
        YcTracking.trackLogin(window['yc_trackid']);

        trackClickAndRate();
        hookBasketHandlers();
        hookLogoutHandler();
        trackBuy();
    };
}

var initYcTracking = function (context) {

    initYcTrackingCore(context);
    initYcTrackingModule(context);

    return context.YcTracking;
};

if (typeof define === 'function' && define.amd) {
    // Expose YcTracking as an AMD module if it's loaded with RequireJS or similar.
    define(function () {
        return initYcTracking({});
    });
} else {
    // Load YcTracking normally (creating a YcTracking global) if not using an AMD loader.
    initYcTracking(this);
}

} (this));
