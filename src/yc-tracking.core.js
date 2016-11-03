/* global Handlebars */

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
        var customerId = YC_CUSTOMER_ID,

            /**
             * Holds path to event tracking api with customer id.
             *
             * @private
             * @readonly
             * @type {string}
             */
            eventHost = YC_RECO_EVENT_HOST + customerId,

            /**
             * Holds path to product recommendations api with customer id.
             *
             * @private
             * @readonly
             * @type {string}
             */
            recommendationHost = YC_RECO_RECOM_HOST + customerId,

            /**
             * Holds path to product recommendations api with customer id.
             *
             * @private
             * @readonly
             * @type {string}
             */
            recommendationHostV2 = YC_RECO_RECOM_HOST + 'v2/' + customerId,

            /**
             * Holds path to search recommendations api with customer id.
             *
             * @private
             * @readonly
             * @type {string}
             */
            searchRecommendationHost = YC_RECO_RECOM_HOST + 'v4/search/' + customerId + '/get_suggestions.jsonp?',

            /**
             * Duration of session in minutes.
             *
             * @private
             * @readonly
             * @type {number}
             */
            sessionDuration = 60 * 24 * 14,

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
            },

            /**
             * Executes call to remote server by placing script tag.
             *
             * @private
             * @param {string} url - Url to call.
             */
            _executeJsonpCall = function (url) {
                // recommendations use JSONP call
                var script = GLOBAL.document.createElement('script');
                script.src = url;
                GLOBAL.document.head.appendChild(script);
            },

            /**
             * Creates jsonp handler function that processes and renders search suggestion data
             *
             * @param {object} searchNode
             * @param {string} language
             * @returns {Function}
             */
            _createJsonpSearchResponseHandler = function (searchNode, language) {
                return function (data, statusCode) {
                    var property,
                        searchResults = [];

                    if (statusCode !== 200) {
                        return;
                    }

                    // Formatting data
                    for (property in data) {
                        if (data.hasOwnProperty(property) && YC_SEARCH_TEMPLATES.hasOwnProperty(property)) {
                            data[property].sort(function (a, b) {
                                return a.yc_score - b.yc_score;
                            });

                            searchResults.push({
                                name: property.toLowerCase(),
                                template: YC_SEARCH_TEMPLATES[property],
                                results: data[property].slice(0, YC_SEARCH_TEMPLATES[property].amount),
                                priority: YC_SEARCH_TEMPLATES[property].priority
                            });
                        }
                    }

                    // Sorting by priority
                    searchResults.sort(function (a, b) {
                        return a.priority - b.priority;
                    });

                    searchNode.view.innerHTML = '';
                    searchResults.forEach(function (elem) {
                        var compiled = Handlebars.compile(elem.template.html_template),
                            wrapper = GLOBAL.document.createElement('div'),
                            filtered = [],
                            view = searchNode.view;

                        _extractConstants(elem.template.consts, elem, language);

                        //reformat prices
                        elem.results.forEach(function (item) {
                            var priceValue,
                                inArray = filtered.find(function (e) {
                                    return e.title === item.title && e.url === item.url;
                                });

                            if (inArray) {
                                return;
                            }

                            filtered.push(item);
                            if (item.price) {
                                priceValue = item.price.replace(YC_CONSTS.currency, '').replace('.', YC_DECIMAL_SEPARATOR);

                                item.price = YC_RENDER_PRICE_FORMAT.replace('{price}', priceValue)
                                    .replace('{currencySign}', YC_CONSTS.currencySign)
                                    .replace('{currency}', YC_CONSTS.currency);
                            }
                        });

                        elem.results = filtered;
                        wrapper.innerHTML = compiled(elem);
                        view.appendChild(wrapper);
                        _repositionSearchResults(searchNode.searchElement, view);
                        view.style.display = 'block';
                    });
                }
            },

            /**
             * Method for fetching search results for given query string.
             * Generates JSONP call to server.
             *
             * @param {object} config - This object contains all paramaters needed to make API call
             * @returns {YcTracking} This object's instance.
             */
            _callFetchSearchResults = function  (config) {
                var url = searchRecommendationHost,
                    parameters = [];

                for (var key in config) {
                    if (config.hasOwnProperty(key)) {
                        if (key !== 'attribute') {
                            parameters.push(key + '=' + encodeURIComponent(config[key]));
                        } else {
                            config[key].forEach(function (attr) {
                                parameters.push(key + '=' + encodeURIComponent(attr));
                            });
                        }
                    }
                }

                _executeJsonpCall(url + parameters.join('&'));
            },

            /**
             *
             * @param {number} direction
             * @param {object} searchInput
             * @param {string} searchText
             * @param {string} viewId
             * @private
             */
            _markAsSelected = function (direction, searchInput, searchText, viewId) {
                var allElements = GLOBAL.document.querySelectorAll('#' + viewId + YC_SEARCH_ALL_RESULTS_SELECTOR),
                    current = GLOBAL.document.querySelector('#' + viewId + YC_SEARCH_SELECTED_SELECTOR),
                    next,
                    i = 0;

                if (!allElements || allElements.length < 1) {
                    return;
                }

                if (current) {
                    current.className = current.className.replace('yc-hover', '');
                    for (; i < allElements.length; i++) {
                        if (allElements[i] == current) {
                            break;
                        }
                    }

                    next = allElements[i + direction];
                    if (!next) {
                        searchInput.value = searchText;
                    } else {
                        next.className += ' yc-hover';
                        searchInput.value = next.attributes['yc-data-title'].value;
                    }
                } else {
                    next = (direction === 1) ? allElements[0] : allElements[allElements.length - 1];
                    next.className += ' yc-hover';
                    searchInput.value = next.attributes['yc-data-title'].value;
                }

            },

            /**
             * Extracts constants from source with given language and adds them to destination object
             *
             * @param {object} source
             * @param {object} destination
             * @param {string} locale
             * @private
             */
            _extractConstants = function (source, destination, locale) {
                var prop,
                    language = locale ? locale.substr(0, locale.indexOf('-')) : '',
                    propertyNames;

                destination.const = {};

                // Adding local constants
                for (prop in source) {
                    if (source.hasOwnProperty(prop)) {
                        if (typeof(source[prop]) === 'object') {
                            propertyNames = Object.getOwnPropertyNames(source[prop]);
                            if (propertyNames.length) {
                                destination.const[prop] = source[prop][locale] ? source[prop][locale] :
                                    source[prop][language] ? source[prop][language] :
                                        source[prop][''] ? source[prop][''] : source[prop][propertyNames[0]];
                            } else {
                                destination.const[prop] = '';
                                GLOBAL.console.log('Error: No translation found. Constant "' + prop + '" is an empty object!');
                            }
                        } else {
                            destination.const[prop] = source[prop];
                        }
                    }
                }

                // Adding global constants
                for (prop in YC_CONSTS) {
                    if (YC_CONSTS.hasOwnProperty(prop) && !destination.const.hasOwnProperty(prop)) {
                        if (typeof(YC_CONSTS[prop]) === 'object') {
                            propertyNames = Object.getOwnPropertyNames(YC_CONSTS[prop]);
                            if (propertyNames.length) {
                                destination.const[prop] = YC_CONSTS[prop][locale] ? YC_CONSTS[prop][locale] :
                                    YC_CONSTS[prop][language] ? YC_CONSTS[prop][language] :
                                        YC_CONSTS[prop][''] ? YC_CONSTS[prop][''] : YC_CONSTS[prop][propertyNames[0]];
                            } else {
                                destination.const[prop] = '';
                                GLOBAL.console.log('Error: No translation found. Constant "' + prop + '" is an empty object!');
                            }
                        } else {
                            destination.const[prop] = YC_CONSTS[prop];
                        }
                    }
                }
            },

            /**
             *
             * @param {object} searchBox
             * @param {object} searchResults
             * @private
             */
            _repositionSearchResults = function(searchBox, searchResults){
                var rect = searchBox.getBoundingClientRect(),
                    resultsRect = searchResults.getBoundingClientRect(),
                    body = GLOBAL.document.body,
                    bodyRect = body.getBoundingClientRect(),
                    offsetY = rect.top - bodyRect.top,
                    x = rect.left + rect.width / 2,
                    y = offsetY + rect.height / 2;

                if (x > body.scrollWidth / 2) {
                    searchResults.style.removeProperty('left');
                    searchResults.style.right = Math.round(bodyRect.width - rect.right) + 'px';
                } else {
                    searchResults.style.removeProperty('right');
                    searchResults.style.left = Math.round(rect.left) + 'px';
                }

                if (y > body.scrollHeight / 2) {
                    searchResults.style.top = Math.round(offsetY - resultsRect.height - rect.height / 2) + 'px';
                } else {
                    searchResults.style.top = Math.round(offsetY + rect.height) + 'px';
                }

                searchResults.style.minWidth = rect.width + 'px';
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
         * @param {string} title
         * @param {string} productUrl
         * @param {string} image
         * @param {string} price
         * @param {string} unitPrice
         * @param {string} oldPrice
         * @param {string} rating
         * @param {string} timestamp
         * @param {string} signature
         * @returns {YcTracking} This object's instance.
         */
        this.trackClick = function (itemTypeId, itemId, categoryPath, language, title, productUrl, image, price,
                                    unitPrice, oldPrice, rating, timestamp, signature) {
            var url = '/click/' + _userId() + '/' + itemTypeId + '/' + itemId;

            url += '?categorypath=' + (categoryPath ? encodeURIComponent(categoryPath) : '');
            url += '&lang=' + language;
            url += title !== undefined ? '&title=' + encodeURIComponent(title) : '';
            url += productUrl !== undefined ? '&url=' + encodeURIComponent(productUrl) : '';
            url += image !== undefined ? '&image=' + encodeURIComponent(image) : '';
            url += price !== undefined ? '&price=' + encodeURIComponent((price + '').replace(',', '.')) : '';
            url += unitPrice !== undefined ? '&unitprice=' + encodeURIComponent(unitPrice) : '';
            url += oldPrice !== undefined ? '&pricebefore=' + encodeURIComponent(oldPrice) : '';
            url += rating !== undefined ? '&rating=' + encodeURIComponent(rating) : '';
            url += timestamp ? '&overridetimestamp=' + encodeURIComponent(timestamp) : '';
            url += signature !== undefined ? '&signature=' + signature : '';

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
                _userId(targetUserId);
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

            if (itemIds.length > 0) {
                _executeEventCall(url + itemIds.join(','));
            }

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

        /**
         * Method for fetching product ids for recommendation scenario.
         * Generates JSONP call to server.
         *
         * @param {number} itemTypeId
         * @param {object} scenario
         * @param {number} count
         * @param {string} products
         * @param {string} categoryPath
         * @param {string} callback Name of the callback function.
         * @param {string} lang
         * @returns {YcTracking} This object's instance.
         */
        this.callFetchRecommendedProducts = function (itemTypeId, scenario, count, products, categoryPath, callback, lang) {
            var url = recommendationHost + '/' + _userId() + '/' + scenario +
                        '.jsonp?numrecs=' + (count * 2) + '&outputtypeid=' + itemTypeId +
                        '&jsonpcallback=' + callback;

            url += '&contextitems=' + (products ? encodeURIComponent(products) : '');
            url += '&categorypath=' + (categoryPath ? encodeURIComponent(categoryPath) : '');
            url += '&lang=' + (lang ? lang : '');

            _executeJsonpCall(url);
            return this;
        };

        /**
         * Method for fetching product ids for recommendation scenario version 2
         * This version beside having product ids in its response also has attribute values
         * that are requested
         * Generates JSONP call to server.
         *
         * @param {object} config - This object contains all paramaters needed to make API call
         * @returns {YcTracking} This object's instance.
         */
        this.callFetchRecommendedProductsV2 = function (config) {
            var url = recommendationHostV2 + '/' + _userId() + '/' + config.scenario +
                '.jsonp?numrecs=' + (config.count * 2) + '&outputtypeid=' + config.itemTypeId +
                '&jsonpcallback=' + config.callback;

            url += '&contextitems=' + (config.products ? encodeURIComponent(config.products) : '');
            url += '&categorypath=' + (config.categoryPath ? encodeURIComponent(config.categoryPath) : '');
            url += '&usecontextcategorypath=' + (config.useContextCategoryPath === true);
            url += '&recommendCategory=' + (config.recommendCategory  === true);
            url += '&lang=' + (config.lang ? config.lang : '');

            config.attributes.forEach(function (attr) {
                url += '&attribute=' + encodeURIComponent(attr);
            });

            if (config.attributeValues) {
                for (var atr in config.attributeValues) {
                    if (config.attributeValues.hasOwnProperty(atr)) {
                        url += '&' + atr + '=' + config.attributeValues[atr];
                    }
                }
            }

            _executeJsonpCall(url);
            return this;
        };

        /**
         * Renders recommendation boxes and displays them on frontend page.
         *
         * @param {object} box
         * @param {string} lang
         * @param {function} trackFunction
         * @param {string} linkProperty
         */
        this.renderRecommendation = function(box, lang, trackFunction, linkProperty) {
            var template = box ? box.template : null,
                section = template ? template.html_template : null,
                num = 0,
                compiled,
                wrapper = GLOBAL.document.createElement('div'),
                rows = [],
                columns = [],
                elem = template ? GLOBAL.document.querySelector(template.target) : null;

            if (!box || !box.products || !box.products.length || !elem) {
                return null;
            }

            _extractConstants(template.consts, box, lang);

            box.products.forEach(function (product) {
               num++;
               columns.push(product);
               if ((num % template.columns) === 0) {
                    rows.push({'columns' : columns});
                    columns = [];
                }
            });
            if (columns.length) {
                rows.push({'columns' : columns});
            }

            box.rows = rows;
            compiled = Handlebars.compile(section);
            wrapper.className = 'yc-recommendation-box';
            wrapper.innerHTML = compiled(box);
            elem.appendChild(wrapper);

            linkProperty = (linkProperty ? linkProperty : 'link');
            box.products.forEach(function (product) {
                var myTags = wrapper.querySelectorAll('a[href="' + product[linkProperty] + '"]'),
                    i;

                for (i = 0; i < myTags.length; i++) {
                    myTags[i].addEventListener('click', trackFunction(product, template.scenario));
                }
            });

            return this;
        };

        /**
         * Hooks search suggestion engine to every search box
         *
         * @param {string} language
         */
        this.hookSearchingHandler = function (language) {
            var allAttributes = [],
                yc_debug = this.getParameterByName('yc_debug'),
                unfiltered,
                property;

            //get all variables from all suggestion search templates
            for (property in YC_SEARCH_TEMPLATES) {
                if (YC_SEARCH_TEMPLATES.hasOwnProperty(property) && YC_SEARCH_TEMPLATES[property].enabled) {
                    unfiltered = this.extractTemplateVariables(YC_SEARCH_TEMPLATES[property].html_template);
                    allAttributes = allAttributes.concat(unfiltered);
                }
            }

            //remove duplicates
            allAttributes = allAttributes.filter(function(item, pos) {
                return allAttributes.indexOf(item) == pos;
            });

            YC_SEARCH_FIELDS.forEach(function (elem, index) {
                var searchInput = GLOBAL.document.querySelector(elem.target),
                    newNode,
                    functionName,
                    parameters = {
                        lang: language,
                        itemtype: 1,
                        attribute: allAttributes
                    },
                    property,
                    searchText = '';

                if (!searchInput) {
                    return;
                }

                // Check if language code is in correct format
                if (language === null || !language.match(/^[a-z]{2}(\-[a-z]{2})?$/i)) {
                    GLOBAL.console.log('Language code (' + language + ') is not in correct format, see http://www.rfc-editor.org/rfc/bcp/bcp47.txt for more info.');
                    delete parameters.lang;
                }

                // Creating and appending searchResult div
                elem.view = GLOBAL.document.createElement('div');
                elem.view.id = 'ycSearchResult' + index;
                elem.view.className = 'yc-search-result';

                // Cloning the node so previous events are hooked off
                newNode = searchInput.cloneNode(true);
                newNode.onkeyup = null;
                newNode.value = null;
                searchInput.parentNode.replaceChild(newNode, searchInput);
                elem.searchElement = newNode;
                //newNode.parentNode.appendChild(elem.view);
                GLOBAL.document.body.appendChild(elem.view);

                // Create jsonp response handler function for this search box
                functionName = 'ycSearchResponseHandler' + index;
                context[functionName] = _createJsonpSearchResponseHandler(elem, language);

                // Adding parameters
                parameters.jsonpCallback = functionName;
                for (property in YC_SEARCH_TEMPLATES) {
                    if (YC_SEARCH_TEMPLATES.hasOwnProperty(property) && YC_SEARCH_TEMPLATES[property].enabled) {
                        parameters[property.toLowerCase()] = YC_SEARCH_TEMPLATES[property].amount;
                    }
                }

                context.addEventListener('resize', function () {
                    _repositionSearchResults(newNode, elem.view);
                }, false);

                // Hooking new events
                newNode.addEventListener('keyup', function (e) {
                    var me = this;

                    switch (e.keyCode) {
                        case 27: // escape
                            elem.view.style.display = 'none';
                            elem.view.innerHTML = '';
                            break;
                        case 38: // arrow up
                            _markAsSelected(-1, me, searchText, elem.view.id);
                            e.preventDefault();
                            return false;
                        case 40: // arrow down
                            _markAsSelected(1, me, searchText, elem.view.id);
                            e.preventDefault();
                            return false;
                    }

                    if (me.value.length < 2) {
                        elem.view.style.display = 'none';
                        return;
                    }

                    parameters.q = searchText = me.value;
                    _callFetchSearchResults(parameters);
                }, false);

                elem.view.addEventListener('click', function (e) {
                    e.stopPropagation();
                });

                if (!yc_debug) {
                    GLOBAL.document.body.addEventListener('click', function () {
                        elem.view.style.display = 'none';
                    });
                }
            });

            return this;
        };

        /**
         * Retrieves query parameter by name
         * @param {string} name
         * @returns {string}
         */
        this.getParameterByName = function (name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        };

        /**
         * Extracts variables that are used in template and returns them as array
         *
         * @param template
         * @returns {Array}
         */
        this.extractTemplateVariables = function (template) {
            var variables, result = [];

            variables = template.match(/\{{2,3}([^.\/#{}]*)\}{2,3}/g);
            if (!variables || variables.length === 0) {
                return result;
            }

            variables = variables.filter(function(item, pos) {
                return variables.indexOf(item) == pos;
            });

            variables.forEach(function (variable) {
                var name = variable.replace(/[{}]/g, '');

                if (['else', 'this', 'log'].indexOf(name) === -1) {
                    result.push(name);
                }
            });

            return result;
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
