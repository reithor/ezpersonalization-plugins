var YC_CUSTOMER_ID = 909;

var YC_CONSTS = {
    "currency": 'EUR',
    "currencySign": '&euro;'
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'><div class='products-grid grid'>" +
            "<ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}<li class='product-item'>" +
            "<div class='product-item-info'><a href='{{{link}}}' class='product-item-photo'><span class='product-image-container'" +
            " style='width: 240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'><img src='{{{image}}}'" +
            " class='product-image-photo' width='240' height='300' alt='{{{title}}}'/></span></span></a><div " +
            "class='product-item-details'><strong class='product-item-name'><a title='{{{title}}}' href='{{{link}}}' " +
            "class='product-item-link'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox'" +
            " data-product-id='{{{itemId}}}'><span class='price-container price-final_price tax weee'><span " +
            "class='old-price-14-widget-product-grid' data-price-type='finalPrice' class='price-wrapper '>" +
            "<span class='price'>{{{price}}}</span></span></span></div><div class='product-item-actions'>" +
            "<div class='actions-primary'>{{#if postData}}<button class='action tocart primary' " +
            "data-post='{{{postData}}}' data-action='add-to-wishlist' type='button' title='Add to Cart'>" +
            "<span>Add to Cart</span></button>{{/if}}</div><div class='actions-secondary' data-role='add-to-links'>" +
            "{{#if wishlistData}}<a href='#' data-post='{{{wishlistData}}}' class='action towishlist' data-action='add-to-wishlist' title='Add to Wish List'>" +
            "<span>Add to Wish List</span></a>{{/if}}{{#if compareData}}<a href='#' class='action tocompare' data-post='{{{compareData}}}' title='Add to Compare'>" +
            "<span>Add to Compare</span></a>{{/if}}</div></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 0,
        consts: {
            title: {
                '': 'Personal',
                en: 'Personal'
            }
        },
        rows: 2,
        columns: 5,
        scenario: 'personalized',
        enabled: true
    },
    bestseller: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'><div class='products-grid grid'>" +
            "<ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}<li class='product-item'>" +
            "<div class='product-item-info'><a href='{{{link}}}' class='product-item-photo'><span class='product-image-container'" +
            " style='width: 240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'><img src='{{{image}}}'" +
            " class='product-image-photo' width='240' height='300' alt='{{{title}}}'/></span></span></a><div " +
            "class='product-item-details'><strong class='product-item-name'><a title='{{{title}}}' href='{{{link}}}' " +
            "class='product-item-link'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox'" +
            " data-product-id='{{{itemId}}}'><span class='price-container price-final_price tax weee'><span " +
            "class='old-price-14-widget-product-grid' data-price-type='finalPrice' class='price-wrapper '>" +
            "<span class='price'>{{{price}}}</span></span></span></div><div class='product-item-actions'>" +
            "<div class='actions-primary'>{{#if postData}}<button class='action tocart primary' " +
            "data-post='{{postData}}' data-action='add-to-wishlist' type='button' title='Add to Cart'>" +
            "<span>Add to Cart</span></button>{{/if}}</div><div class='actions-secondary' data-role='add-to-links'>" +
            "{{#if wishlistData}}<a href='#' data-post='{{wishlistData}}' class='action towishlist' data-action='add-to-wishlist' title='Add to Wish List'>" +
            "<span>Add to Wish List</span></a>{{/if}}{{#if compareData}}<a href='#' class='action tocompare' data-post='{{compareData}}' title='Add to Compare'>" +
            "<span>Add to Compare</span></a>{{/if}}</div></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 1,
        rows: 2,
        consts: {
            title: {
                '': 'Bestseller',
                en: 'Bestseller'
            }
        },
        columns: 5,
        scenario: 'landing_page',
        enabled: true
    },
    upselling: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'><div class='products-grid grid'>" +
            "<ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}<li class='product-item'>" +
            "<div class='product-item-info'><a href='{{{link}}}' class='product-item-photo'><span class='product-image-container'" +
            " style='width: 240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'><img src='{{{image}}}'" +
            " class='product-image-photo' width='240' height='300' alt='{{{title}}}'/></span></span></a><div " +
            "class='product-item-details'><strong class='product-item-name'><a title='{{{title}}}' href='{{{link}}}' " +
            "class='product-item-link'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox'" +
            " data-product-id='{{{itemId}}}'><span class='price-container price-final_price tax weee'><span " +
            "class='old-price-14-widget-product-grid' data-price-type='finalPrice' class='price-wrapper '>" +
            "<span class='price'>{{{price}}}</span></span></span></div><div class='product-item-actions'>" +
            "<div class='actions-primary'>{{#if postData}}<button class='action tocart primary' " +
            "data-post='{{postData}}' data-action='add-to-wishlist' type='button' title='Add to Cart'>" +
            "<span>Add to Cart</span></button>{{/if}}</div><div class='actions-secondary' data-role='add-to-links'>" +
            "{{#if wishlistData}}<a href='#' data-post='{{wishlistData}}' class='action towishlist' data-action='add-to-wishlist' title='Add to Wish List'>" +
            "<span>Add to Wish List</span></a>{{/if}}{{#if compareData}}<a href='#' class='action tocompare' data-post='{{compareData}}' title='Add to Compare'>" +
            "<span>Add to Compare</span></a>{{/if}}</div></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 1,
        rows: 2,
        consts: {
            title: {
                '': 'Upselling',
                en: 'Upselling'
            }
        },
        columns: 5,
        scenario: 'ultimately_bought',
        enabled: true
    },
    related: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'><div class='products-grid grid'>" +
            "<ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}<li class='product-item'>" +
            "<div class='product-item-info'><a href='{{{link}}}' class='product-item-photo'><span class='product-image-container'" +
            " style='width: 240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'><img src='{{{image}}}'" +
            " class='product-image-photo' width='240' height='300' alt='{{{title}}}'/></span></span></a><div " +
            "class='product-item-details'><strong class='product-item-name'><a title='{{{title}}}' href='{{{link}}}' " +
            "class='product-item-link'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox'" +
            " data-product-id='{{{itemId}}}'><span class='price-container price-final_price tax weee'><span " +
            "class='old-price-14-widget-product-grid' data-price-type='finalPrice' class='price-wrapper '>" +
            "<span class='price'>{{{price}}}</span></span></span></div><div class='product-item-actions'>" +
            "<div class='actions-primary'>{{#if postData}}<button class='action tocart primary' " +
            "data-post='{{postData}}' data-action='add-to-wishlist' type='button' title='Add to Cart'>" +
            "<span>Add to Cart</span></button>{{/if}}</div><div class='actions-secondary' data-role='add-to-links'>" +
            "{{#if wishlistData}}<a href='#' data-post='{{wishlistData}}' class='action towishlist' data-action='add-to-wishlist' title='Add to Wish List'>" +
            "<span>Add to Wish List</span></a>{{/if}}{{#if compareData}}<a href='#' class='action tocompare' data-post='{{compareData}}' title='Add to Compare'>" +
            "<span>Add to Compare</span></a>{{/if}}</div></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        consts: {
            title: {
                '': 'Related',
                en: 'Related'
            }
        },
        priority: 0,
        rows: 2,
        columns: 5,
        scenario: 'cross_rate',
        enabled: true
    },
    crossselling: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'><div class='products-grid grid'>" +
            "<ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}<li class='product-item'>" +
            "<div class='product-item-info'><a href='{{{link}}}' class='product-item-photo'><span class='product-image-container'" +
            " style='width: 240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'><img src='{{{image}}}'" +
            " class='product-image-photo' width='240' height='300' alt='{{{title}}}'/></span></span></a><div " +
            "class='product-item-details'><strong class='product-item-name'><a title='{{{title}}}' href='{{{link}}}' " +
            "class='product-item-link'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox'" +
            " data-product-id='{{{itemId}}}'><span class='price-container price-final_price tax weee'><span " +
            "class='old-price-14-widget-product-grid' data-price-type='finalPrice' class='price-wrapper '>" +
            "<span class='price'>{{{price}}}</span></span></span></div><div class='product-item-actions'>" +
            "<div class='actions-primary'>{{#if postData}}<button class='action tocart primary' " +
            "data-post='{{postData}}' data-action='add-to-wishlist' type='button' title='Add to Cart'>" +
            "<span>Add to Cart</span></button>{{/if}}</div><div class='actions-secondary' data-role='add-to-links'>" +
            "{{#if wishlistData}}<a href='#' data-post='{{wishlistData}}' class='action towishlist' data-action='add-to-wishlist' title='Add to Wish List'>" +
            "<span>Add to Wish List</span></a>{{/if}}{{#if compareData}}<a href='#' class='action tocompare' data-post='{{compareData}}' title='Add to Compare'>" +
            "<span>Add to Compare</span></a>{{/if}}</div></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 0,
        rows: 2,
        consts: {
            title: {
                '': 'Cross Sell',
                en: 'Cross Sell'
            }
        },
        columns: 5,
        scenario: 'cross_sell',
        enabled: true
    },
    category_page: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'><div class='products-grid grid'>" +
            "<ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}<li class='product-item'>" +
            "<div class='product-item-info'><a href='{{{link}}}' class='product-item-photo'><span class='product-image-container'" +
            " style='width: 240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'><img src='{{{image}}}'" +
            " class='product-image-photo' width='240' height='300' alt='{{{title}}}'/></span></span></a><div " +
            "class='product-item-details'><strong class='product-item-name'><a title='{{{title}}}' href='{{{link}}}' " +
            "class='product-item-link'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox'" +
            " data-product-id='{{{itemId}}}'><span class='price-container price-final_price tax weee'><span " +
            "class='old-price-14-widget-product-grid' data-price-type='finalPrice' class='price-wrapper '>" +
            "<span class='price'>{{{price}}}</span></span></span></div><div class='product-item-actions'>" +
            "<div class='actions-primary'>{{#if postData}}<button class='action tocart primary' " +
            "data-post='{{postData}}' data-action='add-to-wishlist' type='button' title='Add to Cart'>" +
            "<span>Add to Cart</span></button>{{/if}}</div><div class='actions-secondary' data-role='add-to-links'>" +
            "{{#if wishlistData}}<a href='#' data-post='{{wishlistData}}' class='action towishlist' data-action='add-to-wishlist' title='Add to Wish List'>" +
            "<span>Add to Wish List</span></a>{{/if}}{{#if compareData}}<a href='#' class='action tocompare' data-post='{{compareData}}' title='Add to Compare'>" +
            "<span>Add to Compare</span></a>{{/if}}</div></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns .column.main',
        priority: 0,
        rows: 2,
        consts: {
            title: {
                '': 'Category',
                en: 'Category'
            }
        },
        columns: 5,
        scenario: 'category_page',
        enabled: true
    }
};

var YC_SEARCH_FIELDS = [{
    target: ["#search"]
}];

/* 
 * ABOUT NEW CONFIGURATION FIELDS
 * ==============================
 * new fileds: position, topRowWhenResize, hideOnNoResults
 * deprecated: priority. Use position instead.
 *
 *  use: 
 * - priority: <int>
 * OR
 * - position: {
 *   column: <int>, //starts in 0
 *   row: <int>; optional
 * }
 * when priority is defined, position is ignored (to keep backward compatibility)
 * in position: row is optional when, from all the templates, only 1 template exists in the column
 * 
 * - topRowWhenResize: <bool>; optional. In the searchbox, is the first template to be displayed when the window resizes. only one allowed.
 * - hideOnNoResults:  <bool>; optional. Hides the template in the searchbox when no (search) result was found for that template.
 * */
var YC_SEARCH_TEMPLATES = {
    "CATEGORY": {
        "html_template": "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{searchTitle}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No category results</span>{{/each}}</ul>",
        "amount": 10,
        "enabled": true,
        "consts": {
            "title": {
                "": "Category suggestions",
                "de": "Kategorievorschläge"
            }
        },
        //priority is deprecated. use position instead
        /*"priority": 0,
        "position": {
            "column": 0,
            "row": 0
        },*/
        "hideOnNoResults": false
    },
    "VENDOR": {
        "html_template": "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{searchTitle}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No vendor results</span>{{/each}}</ul>",
        "amount": 10,
        "enabled": true,
        "consts": {
            "title": {
                "": "Brand suggestions",
                "de": "Markenvorschläge"
            }
        },
        // priority is deprecated. use position instead
        /*"priority": 1,
        "position": {
            "column": 0,
            "row": 1
        },*/
        "hideOnNoResults": true
    },
    "ITEM": {
        "html_template": "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{searchTitle}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No item results</span>{{/each}}</ul>",
        "amount": 10,
        "enabled": true,
        "consts": {
            "title": {
                "": "Product suggestions",
                "de": "Produktvorschläge"
            }
        },
        //priority is deprecated. use position instead
        /*"priority": 2,
        "position": {
            "column": 1
                //row is optional when there is only 1 template in the colum
        },*/
        "topRowWhenResize": true
    }
};