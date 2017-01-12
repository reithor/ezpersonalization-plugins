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
        position: 'APPEND',
        priority: 0,
        consts: {
            title: {
                '': 'Personal',
                en: 'Personal'
            }
        },
        rows: 1,
        columns: 5,
        scenario: 'personal',
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
        position: 'APPEND',
        priority: 1,
        consts: {
            title: {
                '': 'Bestseller',
                en: 'Bestseller'
            }
        },
        rows: 1,
        columns: 5,
        scenario: 'bestseller',
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
        position: 'APPEND',
        priority: 1,
        consts: {
            title: {
                '': 'Upselling',
                en: 'Upselling'
            }
        },
        rows: 1,
        columns: 5,
        scenario: 'up_selling',
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
        position: 'APPEND',
        consts: {
            title: {
                '': 'Related',
                en: 'Related'
            }
        },
        priority: 0,
        rows: 1,
        columns: 5,
        scenario: 'related_products',
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
        position: 'APPEND',
        priority: 0,
        consts: {
            title: {
                '': 'Cross Sell',
                en: 'Cross Sell'
            }
        },
        rows: 1,
        columns: 5,
        scenario: 'cross_selling',
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
        target: '.columns',
        position: 'APPEND',
        priority: 1,
        consts: {
            title: {
                '': 'Category Bestseller',
                en: 'Category Bestseller'
            }
        },
        rows: 1,
        columns: 5,
        scenario: 'category_page',
        enabled: true
    },
};

var YC_SEARCH_FIELDS = [{
    target: ["#search"]
}];

/* 
 * ABOUT NEW CONFIGURATION FIELDS
 * ==============================
 *
 * - positionColumn: <int>, //starts in 0
 * - positionRow:    <int>; //starts in 0
 * - topRowWhenResize: <bool>; In the searchbox, sets the first template to be displayed when the window resizes. Only one as 'true' is allowed.
 * - hideOnNoResults:  <bool>; Hides the template in the searchbox when no (search) result was found for that template.
 *
 * when positionColumn is not defined, the old design is used (single column search results) to keep backward compatibility
 * */
var YC_SEARCH_TEMPLATES = {
    "CATEGORY": {
        "html_template": "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><span class='yc-search-title'>{{{searchTitle}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No category results</span>{{/each}}</ul>",
        "amount": 10,
        "enabled": true,
        "consts": {
            "title": {
                "": "Category suggestions",
                "de": "Kategorievorschläge"
            }
        },
        "positionColumn": 0,
        "positionRow": 0,
        "topRowWhenResize": false,
        "hideOnNoResults": false
    },
    "VENDOR": {
        "html_template": "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><span class='yc-search-title'>{{{searchTitle}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No vendor results</span>{{/each}}</ul>",
        "amount": 10,
        "enabled": true,
        "consts": {
            "title": {
                "": "Brand suggestions",
                "de": "Markenvorschläge"
            }
        },
        "positionColumn": 0,
        "positionRow": 1,
        "topRowWhenResize": false,
        "hideOnNoResults": true
    },
    "ITEM": {
        "html_template": "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><img src='{{thumbnailImageUrl}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{searchTitle}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No item results</span>{{/each}}</ul>",
        "amount": 10,
        "enabled": true,
        "consts": {
            "title": {
                "": "Product suggestions",
                "de": "Produktvorschläge"
            }
        },
        "positionColumn": 1,
        "positionRow": 0,
        "topRowWhenResize": true,
        "hideOnNoResults": false
    }
};