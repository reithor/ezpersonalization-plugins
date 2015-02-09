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
     * Holds customer Id. This should be automatically populated on server from request to js when serving js.
     * Request is in format https://cdn.yoochoose.net/v1/100001/tracking.js, where 100001 is customer id.
     *
     * @type {number}
     */
    var customerId = 903;

    /**
     * Duration of session in days.
     *
     * @type {number}
     */
    var sessionDuration = 30;

    /**
     * Holds path to event tracking api with customer id.
     *
     * @type {string}
     */
    var eventHost = '//event.yoochoose.net/api/' + customerId;

    /**
     * Checks if local storage can be used to store user data.
     * https://github.com/Modernizr/Modernizr/blob/master/feature-detects/storage/localstorage.js
     *
     * @private
     * @returns {boolean}
     */
    function canUseLocalStorage() {
        var mod = 'yc_localStorageTest';
        try {
            GLOBAL.localStorage.setItem(mod, mod);
            GLOBAL.localStorage.removeItem(mod);
            return true;
        } catch(e) {
            return false;
        }
    }

    /**
     * Gets user data from local storage.
     *
     * @returns {object}
     */
    function getLocalStore() {
        var storeId = 'YCStore';
        return canUseLocalStorage() ? JSON.parse(GLOBAL.localStorage.getItem(storeId)) : null;
    }

    /**
     * Saves user data to local storage.
     *
     * @param value {object} Data to store.
     */
    function setLocalStore(value) {
        var storeId = 'YCStore';
        if (canUseLocalStorage()) {
            GLOBAL.localStorage.setItem(storeId, JSON.stringify(value));
        } else {
            throw 'Local storage not supported!';
        }
    }

    /**
     * Generates unique identifier.
     *
     * @private
     * @returns {string}
     */
    function generateGuid() {
        /**
         * @returns {string}
         */
        var S4 = function () {
            return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
        };
        return (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4());
    }

    /**
     * Gets current user unique identifier. It tries to use local storage if browser supports it.
     * If not, cookie is used.
     *
     * @returns {string}
     */
    function userId(userId) {
        /**
         * Gets or sets user identifier using local store. Stores user identifier if supplied through parameter userId.
         *
         * @param [userId] {string} User identifier
         * @returns {string}
         */
        function userIdFromLocalStorage(userId) {
            var userData = getLocalStore(),
                expirationDate = new Date();

            expirationDate.setDate(expirationDate.getDate() + sessionDuration);
            if (!userId) {
                if (userData && userData.expires > new Date().getTime()) {
                    userId = userData.userId;
                } else {
                    userId = generateGuid();
                }
            }

            setLocalStore({ userId: userId, expires: expirationDate.getTime() });
            return userId;
        }

        /**
         * Gets or sets user identifier using cookie. Stores user identifier if supplied through parameter userId.
         *
         * @param [userId] {string} User identifier
         * @returns {string}
         */
        function userIdFromCookie(userId) {
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
                    userId = generateGuid();
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
        }

        if (canUseLocalStorage()) {
            return userIdFromLocalStorage(userId);
        }

        return userIdFromCookie(userId);
    }

    /**
     * Constructor for the YcTracking Object.
     *
     * @constructor
     */
    var YcTracking = function () {

        this._executeEventCall = function (url) {
            // event tracking must use pixel method because server does not support JSONP response
            var img = new Image(1, 1);
            img.src = eventHost + url;
        };

        return this;
    };

    // @todo: add validations for all events

    /**
     * Method for tracking Click event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @param [categoryPath] {string}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackClick = function (itemTypeId, itemId, categoryPath) {
        var url = '/click/' + userId() + '/' + itemTypeId + '/' + itemId;
        if (categoryPath) {
            url += '?categorypath=' + encodeURIComponent(categoryPath);
        }

        this._executeEventCall(url);
        return this;
    };

    /**
     * Method for tracking Rate event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @param rating {number}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackRate = function (itemTypeId, itemId, rating) {
        var url = '/rate/' + userId() + '/' + itemTypeId + '/' + itemId + '?rating=' + rating;
        this._executeEventCall(url);
        return this;
    };

    /**
     * Method for tracking Basket event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @param [categoryPath] {string}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackBasket = function (itemTypeId, itemId, categoryPath) {
        var url = '/basket/' + userId() + '/' + itemTypeId + '/' + itemId;
        if (categoryPath) {
            url += '?categorypath=' + encodeURIComponent(categoryPath);
        }

        this._executeEventCall(url);
        return this;
    };

    /**
     * Method for tracking Buy event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @param quantity {number}
     * @param price {number}
     * @param currencyCode {string}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackBuy = function (itemTypeId, itemId, quantity, price, currencyCode) {
        var url = '/buy/' + userId() + '/' + itemTypeId + '/' + itemId +
            '?fullprice=' + price + currencyCode +'&quantity=' + quantity;

        this._executeEventCall(url);
        return this;
    };

    /**
     * Method for tracking Login (Transfer) event.
     *
     * @param targetUserId {string} New user ID
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackLogin = function (targetUserId) {
        this._executeEventCall('/login/' + userId() + '/' + encodeURIComponent(targetUserId));

        // @todo: check whether targetUserId should be used from now on.
        userId(targetUserId);
        return this;
    };

    /**
     * Method for tracking Rendered event.
     *
     * @param itemTypeId {number}
     * @param itemIds {array} Array of item identifiers (string).
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackRendered = function (itemTypeId, itemIds) {
        var url = '/rendered/' + userId() + '/' + itemTypeId + '/' + itemIds.join(',');
        this._executeEventCall(url);
        return this;
    };

    /**
     * Method for tracking Click Recommended (Follow) event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @param scenario {string}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackClickRecommended = function (itemTypeId, itemId, scenario) {
        var url = '/clickrecommended/' + userId() + '/' + itemTypeId + '/' + itemId + '?scenario=' + encodeURIComponent(scenario);

        this._executeEventCall(url);
        return this;
    };

    /**
     * Method for tracking Consume event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackConsume = function (itemTypeId, itemId) {
        this._executeEventCall('/consume/' + userId() + '/' + itemTypeId + '/' + itemId);
        return this;
    };

    /**
     * Method for tracking Blacklist event.
     *
     * @param itemTypeId {number}
     * @param itemId {number}
     * @return {YcTracking} Callee instance.
     */
    YcTracking.prototype.trackBlacklist = function (itemTypeId, itemId) {
        var url = '/blacklist/' + userId() + '/' + itemTypeId + '/' + itemId;
        this._executeEventCall(url);
        return this;
    };

    context.YcTracking = YcTracking;

    if (DEBUG) {
        /**
         * For testing purposes only!
         * @returns {string}
         */
        YcTracking.prototype.getUserId = function() {
            return userId();
        };

        /**
         * For testing purposes only!
         * @returns {*}
         */
        YcTracking.prototype.getUserDetails = function() {
            return getLocalStore();
        };
    }
}
