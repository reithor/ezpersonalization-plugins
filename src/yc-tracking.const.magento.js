var YC_CUSTOMER_ID = 903;

var YC_CONSTS = {
    "currency": 'EUR',
    "currencySign": '�'
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: "<div><h2 class='subtitle'>{{{title}}}</h2><ul " +
            "class='products-grid products-grid--max-5-col'>" +
            "{{#each rows}}{{#each columns}}" +
            "<li class='rendered-personal item last'><a href='{{url_path}}' " +
            "title='{{name}}' class='product-image'>" +
            "<img src='{{thumbnail}}' alt='{{name}}'>" +
            "</a><h3 class='product-name'><a href='{{url_path}}' " +
            "title='{{name}}'>{{name}}</a></h3><div class='price-box'>" +
            "<span class='regular-price' id='product-price-410-new'>" +
            "<span class='price'>{{price}}</span></span></div></li>{{/each}}" +
            "{{/each}}</ul></div>",
        target: '.main',
        priority: 0,
        consts: {
            title: {
                '': 'Personalized Recommendations',
                en: 'Personalized Recommendations'
            }
        },
        enabled: true,
        rows: 2,
        columns: 2,
        scenario: 'personal'
    },
    bestseller: {
        html_template: "<div><h2 class='subtitle'>{{{title}}}</h2><ul " +
            "class='products-grid products-grid--max-5-col'>" +
            "{{#each rows}}{{#each columns}}" +
            "<li class='rendered-bestseller item last'><a href='{{url_path}}' " +
            "title='{{name}}' class='product-image'>" +
            "<img src='{{thumbnail}}' alt='{{name}}'>" +
            "</a><h3 class='product-name'><a href='{{url_path}}' " +
            "title='{{name}}'>{{name}}</a></h3><div class='price-box'>" +
            "<span class='regular-price' id='product-price-410-new'>" +
            "<span class='price'>{{price}}</span></span></div></li>{{/each}}" +
            "{{/each}}</ul></div>",
        target: '.main',
        priority: 1,
        consts: {
            title: {
                '': 'Bestseller',
                en: 'Bestseller'
            }
        },
        enabled: true,
        rows: 2,
        columns: 2,
        scenario: 'bestseller'
    },
    upselling: {
        html_template: "<div><h2 class='subtitle'>{{{title}}}</h2><ul " +
            "class='products-grid products-grid--max-5-col'>" +
            "{{#each rows}}{{#each columns}}" +
            "<li class='rendered-upselling item last'><a href='{{url_path}}' " +
            "title='{{name}}' class='product-image'>" +
            "<img src='{{thumbnail}}' alt='{{name}}'>" +
            "</a><h3 class='product-name'><a href='{{url_path}}' " +
            "title='{{name}}'>{{name}}</a></h3><div class='price-box'>" +
            "<span class='regular-price' id='product-price-410-new'>" +
            "<span class='price'>{{price}}</span></span></div></li>{{/each}}" +
            "{{/each}}</ul></div>",
        target: '.main',
        priority: 1,
        consts: {
            title: {
                '': 'Upselling',
                en: 'Upselling'
            }
        },
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'up_selling'
    },
    related: {
        html_template: "<div><h2 class='subtitle'>{{{title}}}</h2><ul " +
            "class='products-grid products-grid--max-5-col'>" +
            "{{#each rows}}{{#each columns}}" +
            "<li class='rendered-related item last'><a href='{{url_path}}' " +
            "title='{{name}}' class='product-image'>" +
            "<img src='{{thumbnail}}' alt='{{name}}'>" +
            "</a><h3 class='product-name'><a href='{{url_path}}' " +
            "title='{{name}}'>{{name}}</a></h3><div class='price-box'>" +
            "<span class='regular-price' id='product-price-410-new'>" +
            "<span class='price'>{{price}}</span></span></div></li>{{/each}}" +
            "{{/each}}</ul></div>",
        target: '.main',
        priority: 0,
        consts: {
            title: {
                '': 'Related Products',
                en: 'Related Products'
            }
        },
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'related_products'
    },
    crossselling: {
        html_template: "<div><h2 class='subtitle'>{{{title}}}</h2><ul " +
            "class='products-grid products-grid--max-5-col'>" +
            "{{#each rows}}{{#each columns}}" +
            "<li class='rendered-crossselling item last'><a href='{{url_path}}' " +
            "title='{{name}}' class='product-image'>" +
            "<img src='{{thumbnail}}' alt='{{name}}'>" +
            "</a><h3 class='product-name'><a href='{{url_path}}' " +
            "title='{{name}}'>{{name}}</a></h3><div class='price-box'>" +
            "<span class='regular-price' id='product-price-410-new'>" +
            "<span class='price'>{{price}}</span></span></div></li>{{/each}}" +
            "{{/each}}</ul></div>",
        target: '.main',
        priority: 0,
        consts: {
            title: {
                '': 'Cross-Selling',
                en: 'Cross-Selling'
            }
        },
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'cross_selling'
    },
    category_page: {
        html_template: "<div><h2 class='subtitle'>{{{title}}}</h2><ul " +
            "class='products-grid products-grid--max-5-col'>" +
            "{{#each rows}}{{#each columns}}" +
            "<li class='rendered-category_page item last'><a href='{{url_path}}' " +
            "title='{{name}}' class='product-image'>" +
            "<img src='{{thumbnail}}' alt='{{name}}'>" +
            "</a><h3 class='product-name'><a href='{{url_path}}' " +
            "title='{{name}}'>{{name}}</a></h3><div class='price-box'>" +
            "<span class='regular-price' id='product-price-410-new'>" +
            "<span class='price'>{{price}}</span></span></div></li>{{/each}}" +
            "{{/each}}</ul></div>",
        target: '.main',
        priority: 0,
        consts: {
            title: {
                '': 'Category Bestseller',
                en: 'Category Bestseller'
            }
        },
        enabled: true,
        rows: 2,
        columns: 5,
        scenario: 'category_page'
    }
};

var YC_SEARCH_FIELDS = [{
    target: ["#search"]
}];

var YC_SEARCH_TEMPLATES = {
    ITEM: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
            "<a href='{{url}}'><img src='{{image}}' alt='{{{title}}}' title='{{{title}}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
            "</span></a></li>{{else}}<span class='yc-no-results'>No item results</span>{{/each}}</ul>",
        amount: 10,
        enabled: true,
        priority: 1,
        consts: {
            "title": {
                '': 'Recommended Products',
                'de': 'Empfohlene Produkte'
            }
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
            "title": {
                '': 'Recommended Category',
                'de': 'Empfohlene Category'
            }
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
            "title": {
                '': 'Recommended Vendor',
                'de': 'Empfohlene Vendor'
            }
        }
    }
};
