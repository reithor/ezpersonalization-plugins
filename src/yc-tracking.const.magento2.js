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
        "<span class='price'>{{{price}}}</span></span></span></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 0,
        consts: {
            title: {'': 'Personal', en: 'Personal'}
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
        "<span class='price'>{{{price}}}</span></span></span></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 1,
        rows: 2,
        consts: {
            title: {'': 'Bestseller', en: 'Bestseller'}
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
        "<span class='price'>{{{price}}}</span></span></span></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 1,
        rows: 2,
        consts: {
            title: {'': 'Upselling', en: 'Upselling'}
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
        "<span class='price'>{{{price}}}</span></span></span></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        consts: {
            title: {'': 'Related', en: 'Related'}
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
        "<span class='price'>{{{price}}}</span></span></span></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns',
        priority: 0,
        rows: 2,
        consts: {
            title: {'': 'Cross Sell', en: 'Cross Sell'}
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
        "<span class='price'>{{{price}}}</span></span></span></div></div></div></li>{{/each}}{{/each}}</ol></div></div></div>",
        target: '.columns .column.main',
        priority: 0,
        rows: 2,
        consts: {
            title: {'': 'Category', en: 'Category'}
        },
        columns: 5,
        scenario: 'category_page',
        enabled: true
    }
};

var YC_SEARCH_FIELDS = [
    {
        target: ["#search"]
    }
];

var YC_SEARCH_TEMPLATES = {
    ITEM: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
        "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
        "</span></a></li>{{else}}<span class='yc-no-results'>No item results</span>{{/each}}</ul>",
        amount: 10,
        enabled: true,
        priority: 1,
        consts: {
            "title": {'': 'Recommended Products', 'de': 'Empfohlene Produkte'}
        }
    },
    CATEGORY: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
        "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
        "</span></a></li>{{else}}<span class='yc-no-results'>No category results</span>{{/each}}</ul>",
        amount: 5,
        enabled: true,
        priority: 2,
        consts: {
            "title": {'': 'Recommended Category', 'de': 'Empfohlene Category'}
        }
    },
    VENDOR: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
        "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
        "</span></a></li>{{else}}<span class='yc-no-results'>No vendor results</span>{{/each}}</ul>",
        amount: 10,
        enabled: false,
        priority: 3,
        consts: {
            "title": {'': 'Recommended Vendor', 'de': 'Empfohlene Vendor'}
        }
    }
};