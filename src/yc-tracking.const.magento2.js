var YC_CUSTOMER_ID = 909;

var YC_CONSTS = {
    "currency": 'EUR',
    "currencySign": '&euro;'
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}" +
            "{{#if const.category_name}} in {{{const.category_name}}}{{/if}}</h2></div>" +
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
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}" +
            "{{#if const.category_name}} in {{{const.category_name}}}{{/if}}</h2></div>" +
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
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}" +
            "{{#if const.category_name}} in {{{const.category_name}}}{{/if}}</h2></div>" +
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
            },
            viewers: {
                '': 'Nutzer schauen gerade dieses Produkt an',
                en: 'users are currently viewing this'
            }
        },
        rows: 1,
        columns: 5,
        scenario: 'up_selling',
        enabled: true
    },
    related: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}" +
            "{{#if const.category_name}} in {{{const.category_name}}}{{/if}}</h2></div>" +
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
        position: 'BELOW',
        consts: {
            title: {
                '': 'Related',
                en: 'Related'
            },
            viewers: {
                '': 'Nutzer schauen gerade dieses Produkt an',
                en: 'users are currently viewing this'
            }
        },
        priority: 0,
        rows: 1,
        columns: 5,
        scenario: 'related_products',
        enabled: true
    },
    crossselling: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}" +
            "{{#if const.category_name}} in {{{const.category_name}}}{{/if}}</h2></div>" +
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
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}" +
            "{{#if const.category_path}} in {{{const.category_path}}}{{/if}}</h2></div>" +
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
    bundle: {
        html_template: "<div class='content-heading'><h2 class='title'>{{{const.title}}}</h2></span></div>" +
            "<div class='block widget block-products-list grid'><div class='block-content'>" +
            "<div class='products-grid grid'><ol class='product-items widget-product-grid'>{{#each rows}}{{#each columns}}" +
            "<li class='product-item yc-product-item{{#if totalItems}}3{{/if}}'><div class='yc-product-item-info'>" +
            "<div class='yc-product-item-info' data-container='product-grid'><a href='{{{link}}}' class='product photo product-item-photo' tabindex='-1'>" +
            "<span class='product-image-container' style='width:240px;'><span class='product-image-wrapper' style='padding-bottom: 125%;'>" +
            "<img class='product-image-photo' src='{{{image}}}' width='240' height='300' alt='{{{title}}}'></span></span></a>" +
            "<div class='product details product-item-details'><strong class='product name product-item-name'>" +
            "<a class='product-item-link' href='{{{link}}}'>{{{title}}}</a></strong><div class='price-box price-final_price' data-role='priceBox' data-product-id='{{id}}'>" +
            "<span class='price-container price-final_price tax'>" +
            "<input type='checkbox' class='yc-input-checkbox' value='{{price}}' data-product-id='{{id}}' data-post='{{postData}}' checked='checked'><span class='price'>{{{price}}}</span></span></span></div>" +
            "<div class='swatch-opt-{{id}}'></div><div class='yc-product-item-inner'><div class='product actions product-item-actions'>" +
            "<div class='actions-primary'><form class='tocart-form yc-tocart-form' data-role='tocart-form' data-product-id='{{id}}' action='{{{formAction}}}'><input type='hidden' name='product' value='{{id}}'>" +
            "<input type='hidden' name='uenc' value='{{{uenc}}}'><input name='form_key' type='hidden' value='{{{formKey}}}'>" +
            "</form></div></div></div>" +
            "</div><span class='yc-plus'>+</div></li>{{/each}}{{/each}}</ol></div class='product-item-actions'>" +
            "<div class='actions-secondary' data-role='add-to-links'><h4>{{{const.price}}}: <span id='yc-total-price-bundle'>0.00</span></h4>" +
            "<button type='button' title='Add to Cart' class='action tocart primary yc-add-to-cart'><span class='yc-add-to-cart-text-one'>Add to Cart</span>" +
            "<span class='yc-add-to-cart-text-two'>{{const.bundle2}}</span><span class='yc-add-to-cart-text-three'>{{const.bundle3}}</span></button></div><div></div></div></div>",
        target: '.product-info-main',
        priority: 1,
        consts: {
            title: {
                '': 'Frequently brought together',
                en: 'Frequently brought together'
            },
            price: {
                '': 'Price',
                en: 'Price'
            },
            bundle2 : {
                '' : 'Add both items to cart',
                "de" : 'Beide Produkte in den Warenkorb'
            },
            bundle3 : {
                '' : 'Add all three items to cart',
                "de" : 'Alle drei Produkte in den Warenkorb'
            },
            viewers : {
                "" : " users are currently viewing this",
                "en" : " users are currently viewing this",
                "de" : " Nutzer schauen gerade dieses Produkt an"
            }
        },
        rows: 1,
        columns: 3,
        scenario: 'bundle',
        enabled: true
    }
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
