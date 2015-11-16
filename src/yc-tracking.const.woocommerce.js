var YC_CUSTOMER_ID = 904;

var YC_CONSTS = {
    "currency" : 'GBP',
    "currencySign" : '£'
};

var YC_RECO_TEMPLATES = {
    related: {
        html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
        "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
        " rendered-related type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
        "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
        "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
        " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
        "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
        "{{/each}}</ul></div>",
        target: '#main',
        priority: 0,
        consts: {},
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'cross-rate'
    },
    crossselling: {
        html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
        "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
        " rendered-crossselling type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
        "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
        "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
        " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
        "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
        "{{/each}}</ul></div>",
        target: '#main',
        priority: 0,
        consts: {},
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'cross_sell'
    },
    personal: {
        html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
        "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
        " rendered-personal type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
        "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
        "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
        " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
        "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
        "{{/each}}</ul></div>",
        target: '#main',
        priority: 0,
        consts: {},
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'personalized'
    },
    upselling: {
        html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
        "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
        " rendered-upselling type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
        "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
        "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
        " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
        "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
        "{{/each}}</ul></div>",
        target: '#main',
        priority: 1,
        consts: {},
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'ultimately_bought'
    },
    bestseller: {
        html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
        "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
        " rendered-bestseller type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
        "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
        "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
        " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
        "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
        "{{/each}}</ul></div>",
        target: '#main',
        priority: 1,
        consts: {},
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'landing_page'
    },
    category_page: {
        html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
        "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
        " rendered-category_page type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
        "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
        "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
        " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
        "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
        "{{/each}}</ul></div>",
        target: '#main',
        priority: 0,
        consts: {},
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'category_page'
    }
};

var YC_SEARCH_FIELDS = [
    {
        target: ['.search-field']
    }
];

var YC_SEARCH_TEMPLATES = {
    ITEM: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
        "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
        "</span></a></li>{{else}}<span class='yc-no-results'>No item results</span>{{/each}}</ul>",
        amount: 10,
        enabled: true,
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
        consts: {
            "title": {'': 'Recommended Vendor', 'de': 'Empfohlene Vendor'}
        }
    }
};