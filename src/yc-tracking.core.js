// Compiler directive for UglifyJS.  See yc-tracking.const.js for more info.
if (typeof DEBUG === 'undefined') {
    DEBUG = true;
}

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
        var customerId = 903,

            /**
             * Holds path to event tracking api with customer id.
             *
             * @private
             * @readonly
             * @type {string}
             */
            eventHost = '//event.yoochoose.net/api/' + customerId,

            /**
             * Duration of session in days.
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

                expirationDate.setDate(expirationDate.getDate() + sessionDuration);
                if (!userId) {
                    if (userData && userData.expires > new Date().getTime()) {
                        userId = userData.userId;
                    } else {
                        userId = _generateGuid();
                    }
                }

                _setLocalStore({userId: userId, expires: expirationDate.getTime()});
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

                expirationDate.setDate(expirationDate.getDate() + sessionDuration);
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
         * Method for tracking Click event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {string} [categoryPath]
         * @return {YcTracking} This object's instance.
         */
        this.trackClick = function (itemTypeId, itemId, categoryPath) {
            var url = '/click/' + _userId() + '/' + itemTypeId + '/' + itemId;
            if (categoryPath) {
                url += '?categorypath=' + encodeURIComponent(categoryPath);
            }

            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Rate event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {number} rating
         * @return {YcTracking} This object's instance.
         */
        this.trackRate = function (itemTypeId, itemId, rating) {
            var url = '/rate/' + _userId() + '/' + itemTypeId + '/' + itemId + '?rating=' + rating;
            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Basket event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {string} [categoryPath]
         * @return {YcTracking} This object's instance.
         */
        this.trackBasket = function (itemTypeId, itemId, categoryPath) {
            var url = '/basket/' + _userId() + '/' + itemTypeId + '/' + itemId;
            if (categoryPath) {
                url += '?categorypath=' + encodeURIComponent(categoryPath);
            }

            _executeEventCall(url);
            return this;
        };

        /**
         * Method for tracking Buy event.
         *
         * @param {number} itemTypeId
         * @param {string} itemId
         * @param {number} quantity
         * @param {number} price
         * @param {string} currencyCode
         * @return {YcTracking} This object's instance.
         */
        this.trackBuy = function (itemTypeId, itemId, quantity, price, currencyCode) {
            var url = '/buy/' + _userId() + '/' + itemTypeId + '/' + itemId +
                '?fullprice=' + price + currencyCode + '&quantity=' + quantity;

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
            _executeEventCall('/login/' + _userId() + '/' + encodeURIComponent(targetUserId));

            // @todo: check whether targetUserId should be used from now on.
            _userId(targetUserId);
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
         * @param {string} scenario
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
         * @return {YcTracking} This object's instance.
         */
        this.trackConsume = function (itemTypeId, itemId) {
            _executeEventCall('/consume/' + _userId() + '/' + itemTypeId + '/' + itemId);
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

        if (DEBUG) {
            /**
             * For testing purposes only!
             * @returns {string}
             */
            this.getUserId = function () {
                return _userId();
            };

            /**
             * For testing purposes only!
             * @returns {*}
             */
            this.getUserDetails = function () {
                return _getLocalStore();
            };
        }
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

    context.YcTracking = YcTracking;
    context.YcValidator = YcValidator;
}
