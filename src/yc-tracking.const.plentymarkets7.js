//{% include "PMTest::PageDesign.PageDesign" %}
var YC_CUSTOMER_ID = 906;

var YC_CONSTS = {
    "currency" : 'EUR',
    "currencySign" : '&euro;',
    "details" : {'': 'View details', de: ' Artikel ansehen'}
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: "<div class='row'><div class='col-xs-12'><h3>{{{const.title}}}</h3>" +
        "<ul class='product-list row grid' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-6 col-md-4 col-lg-3'><article class='cmp cmp-product-thumb " +
        "cmp-product-thumb--grid'><div class='thumb-inner'><div class='thumb-background'></div>" +
        "<div class='thumb-image'><div class='square-container'> <div class='square-inner main-image'>" +
        "<a href='{{link}}'><img class='img-fluid lazy' data-original='{{image}}' src='{{image}}'" +
        "style='display: inline;'></a></div></div></div><div class='thumb-content'><a href='{{link}}'" +
        "class='thumb-title small'>{{{title}}}</a><div class='thumb-meta'><div class='prices'><a href='{{link}}'>" +
        "{{#if oldPrice}}<del class='crossprice'>{{{oldPrice}}}</del>{{/if}}</a><a href='{{link}}'><div class='price'>{{{newPrice}}}</div>" +
        "</a></div></div></div></div></article></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.container .row .categoryView',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        columns: 4,
        scenario: 'personalized',
        consts: {
            title: {'': 'Personal', de: 'Personal'}
        },
        enabled: true
    },
    bestseller: {
        html_template: "<div class='row'><div class='col-xs-12'><h3>{{{const.title}}}</h3>" +
        "<ul class='product-list row grid' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-6 col-md-4 col-lg-3'><article class='cmp cmp-product-thumb " +
        "cmp-product-thumb--grid'><div class='thumb-inner'><div class='thumb-background'></div>" +
        "<div class='thumb-image'><div class='square-container'> <div class='square-inner main-image'>" +
        "<a href='{{link}}'><img class='img-fluid lazy' data-original='{{image}}' src='{{image}}'" +
        "style='display: inline;'></a></div></div></div><div class='thumb-content'><a href='{{link}}'" +
        "class='thumb-title small'>{{{title}}}</a><div class='thumb-meta'><div class='prices'><a href='{{link}}'>" +
        "{{#if oldPrice}}<del class='crossprice'>{{{oldPrice}}}</del>{{/if}}</a><a href='{{link}}'><div class='price'>{{{newPrice}}}</div>" +
        "</a></div></div></div></div></article></li>{{/each}}{{/each}}</ul></div></div>",
        target: '#page-body.main',
        position: 'APPEND',
        priority: 1,
        rows: 1,
        columns: 4,
        scenario: 'landing_page',
        consts: {
            title: {'': 'Bestseller', de: 'Bestseller'}
        },
        enabled: true
    },
    related: {
        html_template: "<div class='row'><div class='col-xs-12'><h3>{{{const.title}}}</h3>" +
        "<ul class='product-list row grid' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-6 col-md-4 col-lg-3'><article class='cmp cmp-product-thumb " +
        "cmp-product-thumb--grid'><div class='thumb-inner'><div class='thumb-background'></div>" +
        "<div class='thumb-image'><div class='square-container'> <div class='square-inner main-image'>" +
        "<a href='{{link}}'><img class='img-fluid lazy' data-original='{{image}}' src='{{image}}'" +
        "style='display: inline;'></a></div></div></div><div class='thumb-content'><a href='{{link}}'" +
        "class='thumb-title small'>{{{title}}}</a><div class='thumb-meta'><div class='prices'><a href='{{link}}'>" +
        "{{#if oldPrice}}<del class='crossprice'>{{{oldPrice}}}</del>{{/if}}</a><a href='{{link}}'><div class='price'>{{{newPrice}}}</div>" +
        "</a></div></div></div></div></article></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.page-content',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        columns: 4,
        // scenario: 'cross-rate',
        scenario: 'landing_page',
        consts: {
            title: {'': 'CrossRate', de: 'CrossRate'}
        },
        enabled: true
    },
    upselling: {
        html_template: "<div class='row'><div class='col-xs-12'><h3>{{{const.title}}}</h3>" +
        "<ul class='product-list row grid' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-6 col-md-4 col-lg-3'><article class='cmp cmp-product-thumb " +
        "cmp-product-thumb--grid'><div class='thumb-inner'><div class='thumb-background'></div>" +
        "<div class='thumb-image'><div class='square-container'> <div class='square-inner main-image'>" +
        "<a href='{{link}}'><img class='img-fluid lazy' data-original='{{image}}' src='{{image}}'" +
        "style='display: inline;'></a></div></div></div><div class='thumb-content'><a href='{{link}}'" +
        "class='thumb-title small'>{{{title}}}</a><div class='thumb-meta'><div class='prices'><a href='{{link}}'>" +
        "{{#if oldPrice}}<del class='crossprice'>{{{oldPrice}}}</del>{{/if}}</a><a href='{{link}}'><div class='price'>{{{newPrice}}}</div>" +
        "</a></div></div></div></div></article></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.page-content',
        position: 'APPEND',
        priority: 1,
        rows: 1,
        columns: 4,
        // scenario: 'ultimately_bought',
        scenario: 'category_page',
        consts: {
            title: {'': 'Upselling', de: 'Upselling'}
        },
        enabled: true
    },
    crossselling: {
        html_template: "<div class='row'><div class='col-xs-12'><h3>{{{const.title}}}</h3>" +
        "<ul class='product-list row grid' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-6 col-md-4 col-lg-3'><article class='cmp cmp-product-thumb " +
        "cmp-product-thumb--grid'><div class='thumb-inner'><div class='thumb-background'></div>" +
        "<div class='thumb-image'><div class='square-container'> <div class='square-inner main-image'>" +
        "<a href='{{link}}'><img class='img-fluid lazy' data-original='{{image}}' src='{{image}}'" +
        "style='display: inline;'></a></div></div></div><div class='thumb-content'><a href='{{link}}'" +
        "class='thumb-title small'>{{{title}}}</a><div class='thumb-meta'><div class='prices'><a href='{{link}}'>" +
        "{{#if oldPrice}}<del class='crossprice'>{{{oldPrice}}}</del>{{/if}}</a><a href='{{link}}'><div class='price'>{{{newPrice}}}</div>" +
        "</a></div></div></div></div></article></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.container.checkoutDetails.formControlWrapper',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        columns: 4,
        scenario: 'cross_sell',
        consts: {
            title: {'': 'CrossSelling', de: 'CrossSelling'}
        },
        enabled: true
    },
    category_page: {
        html_template: "<div class='row'><div class='col-xs-12'><h3>{{{const.title}}}</h3>" +
        "<ul class='product-list row grid' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-6 col-md-4 col-lg-3'><article class='cmp cmp-product-thumb " +
        "cmp-product-thumb--grid'><div class='thumb-inner'><div class='thumb-background'></div>" +
        "<div class='thumb-image'><div class='square-container'> <div class='square-inner main-image'>" +
        "<a href='{{link}}'><img class='img-fluid lazy' data-original='{{image}}' src='{{image}}'" +
        "style='display: inline;'></a></div></div></div><div class='thumb-content'><a href='{{link}}'" +
        "class='thumb-title small'>{{{title}}}</a><div class='thumb-meta'><div class='prices'><a href='{{link}}'>" +
        "{{#if oldPrice}}<del class='crossprice'>{{{oldPrice}}}</del>{{/if}}</a><a href='{{link}}'><div class='price'>{{{newPrice}}}</div>" +
        "</a></div></div></div></div></article></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.page-content',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        columns: 4,
        scenario: 'category_page',
        consts: {
            title: {'': 'Category', de: 'Category'}
        },
        enabled: true
    }
};

var YC_SEARCH_FIELDS = [
    {
        target: [".search-input"]
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