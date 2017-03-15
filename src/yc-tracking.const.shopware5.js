
var YC_CUSTOMER_ID = 907;

var YC_CONSTS = {
    "currency" : 'EUR',
    "currencySign" : '&euro;'
};

var YC_RECO_TEMPLATES = {
        related: {
            html_template: "<div class='panel has--border' style='float:left;width: 100%;'><div class='topseller--title panel--title is--underline'>" +
            "{{{const.title}}}{{#if const.category_path}} in {{{const.category_path}}}{{/if}}</div><div class='topseller--content panel--body product-slider' data-product-slider-yc='true'>" +
            "<div class='product-slider--container is--horizontal'>{{#each rows}}{{#each columns}}<div class='product-slider--item " +
            "rendered-related'><div class='product--box box--slider'><div class='box--content'><div class='product--info'>" +
            "<a href='{{link}}' class='product--image' title='{{{title}}}'><span class='image--element'><span class='image--media'>" +
            "<img src='{{image}}' alt='{{{title}}}'></span></span></a><a href='{{link}}' title='{{{title}}}' class='product--title'>" +
            "{{{title}}}</a><div class='product--price-info'><div class='price--unit'></div><div class='product--price'>" +
            "<span class='price--default is--nowrap'>{{{price}}}</span></div></div></div></div></div></div>{{/each}}" +
            "{{/each}}</div></div></div>",
            target: '.content-main--inner',
            position: 'APPEND',
            consts: {
                title: {'': 'Personal', de: 'Personal'}
            },
            priority: 0,
            rows: 1,
            columns: 10,
            scenario: 'cross_rate',
            enabled : true
        },
        crossselling: {
            html_template: "<div class='panel has--border' style='float:left;width: 100%;'><div class='topseller--title panel--title is--underline'>" +
            "{{{const.title}}}{{#if const.category_path}} in {{{const.category_path}}}{{/if}}</div><div class='topseller--content panel--body product-slider' data-product-slider-yc='true'>" +
            "<div class='product-slider--container is--horizontal'>{{#each rows}}{{#each columns}}<div class='product-slider--item " +
            "rendered-crossselling'><div class='product--box box--slider'><div class='box--content'><div class='product--info'>" +
            "<a href='{{link}}' class='product--image' title='{{{title}}}'><span class='image--element'><span class='image--media'>" +
            "<img src='{{image}}' alt='{{{title}}}'></span></span></a><a href='{{link}}' title='{{{title}}}' class='product--title'>" +
            "{{{title}}}</a><div class='product--price-info'><div class='price--unit'></div><div class='product--price'>" +
            "<span class='price--default is--nowrap'>{{{price}}}</span></div></div></div></div></div></div>{{/each}}" +
            "{{/each}}</div></div></div>",
            target: '.container_20',
            position: 'APPEND',
            priority: 0,
            rows: 1,
            consts: {
                title: {'': 'Cross Sell', de: 'Cross Sell'}
            },
            columns: 10,
            scenario: 'cross_sell',
            enabled : true
        },
        personal : {
            html_template: "<div class='panel has--border' style='float:left;width: 100%;'><div class='topseller--title panel--title is--underline'>" +
            "{{{const.title}}}{{#if const.category_path}} in {{{const.category_path}}}{{/if}}</div><div class='topseller--content panel--body product-slider' data-product-slider-yc='true'>" +
            "<div class='product-slider--container is--horizontal'>{{#each rows}}{{#each columns}}<div class='product-slider--item " +
            "rendered-personal'><div class='product--box box--slider'><div class='box--content'><div class='product--info'>" +
            "<a href='{{link}}' class='product--image' title='{{{title}}}'><span class='image--element'><span class='image--media'>" +
            "<img src='{{image}}' alt='{{{title}}}'></span></span></a><a href='{{link}}' title='{{{title}}}' class='product--title'>" +
            "{{{title}}}</a><div class='product--price-info'><div class='price--unit'></div><div class='product--price'>" +
            "<span class='price--default is--nowrap'>{{{price}}}</span></div></div></div></div></div></div>{{/each}}" +
            "{{/each}}</div></div></div>",
            target: '.content--wrapper',
            position: 'APPEND',
            priority: 0,
            consts: {
                title: {'': 'Personalized', de: 'Personalized'}
            },
            rows: 1,
            columns: 10,
            scenario: 'personalized',
            enabled : true
        },
        upselling: {
            html_template: "<div class='panel has--border' style='float:left;width: 100%;'><div class='topseller--title panel--title is--underline'>" +
            "{{{const.title}}}{{#if const.category_path}} in {{{const.category_path}}}{{/if}}</div><div class='topseller--content panel--body product-slider' data-product-slider-yc='true'>" +
            "<div class='product-slider--container is--horizontal'>{{#each rows}}{{#each columns}}<div class='product-slider--item " +
            "rendered-upselling'><div class='product--box box--slider'><div class='box--content'><div class='product--info'>" +
            "<a href='{{link}}' class='product--image' title='{{{title}}}'><span class='image--element'><span class='image--media'>" +
            "<img src='{{image}}' alt='{{{title}}}'></span></span></a><a href='{{link}}' title='{{{title}}}' class='product--title'>" +
            "{{{title}}}</a><div class='product--price-info'><div class='price--unit'></div><div class='product--price'>" +
            "<span class='price--default is--nowrap'>{{{price}}}</span></div></div></div></div></div></div>{{/each}}" +
            "{{/each}}</div></div></div>",
            target: '.content-main--inner',
            position: 'APPEND',
            priority: 1,
            rows: 2,
            consts: {
                title: {'': 'Upselling', de: 'Upselling'}
            },
            columns: 5,
            scenario: 'ultimately_bought',
            enabled : true
        },
        bestseller: {
            html_template: "<div class='panel has--border' style='float:left;width: 100%;'><div class='topseller--title panel--title is--underline'>" +
            "{{{const.title}}}{{#if const.category_path}} in {{{const.category_path}}}{{/if}}</div><div class='topseller--content panel--body product-slider' data-product-slider-yc='true'>" +
            "<div class='product-slider--container is--horizontal'>{{#each rows}}{{#each columns}}<div class='product-slider--item " +
            "rendered-bestseller'><div class='product--box box--slider'><div class='box--content'><div class='product--info'>" +
            "<a href='{{link}}' class='product--image' title='{{{title}}}'><span class='image--element'><span class='image--media'>" +
            "<img src='{{image}}' alt='{{{title}}}'></span></span></a><a href='{{link}}' title='{{{title}}}' class='product--title'>" +
            "{{{title}}}</a><div class='product--price-info'><div class='price--unit'></div><div class='product--price'>" +
            "<span class='price--default is--nowrap'>{{{price}}}</span></div></div></div></div></div></div>{{/each}}" +
            "{{/each}}</div></div></div>",
            target: '.content--wrapper',
            position: 'APPEND',
            priority: 1,
            rows: 2,
            consts: {
                title: {'': 'Bestseller', de: 'Bestseller'}
            },
            columns: 5,
            scenario: 'landing_page',
            enabled : true
        },
        category_page: {
            html_template: "<div class='panel has--border' style='float:left;width: 100%;'><div class='topseller--title panel--title is--underline'>" +
            "{{{const.title}}}{{#if const.category_name}} in {{{const.category_name}}}{{/if}}</div><div class='topseller--content panel--body product-slider' data-product-slider-yc='true'>" +
            "<div class='product-slider--container is--horizontal'>{{#each rows}}{{#each columns}}<div class='product-slider--item " +
            "rendered-category_page'><div class='product--box box--slider'><div class='box--content'><div class='product--info'>" +
            "<a href='{{link}}' class='product--image' title='{{{title}}}'><span class='image--element'><span class='image--media'>" +
            "<img src='{{image}}' alt='{{{title}}}'></span></span></a><a href='{{link}}' title='{{{title}}}' class='product--title'>" +
            "{{{title}}}</a><div class='product--price-info'><div class='price--unit'></div><div class='product--price'>" +
            "<span class='price--default is--nowrap'>{{{price}}}</span></div></div></div></div></div></div>{{/each}}" +
            "{{/each}}</div></div></div>",
            target: '.content.listing--content',
            position: 'APPEND',
            priority: 0,
            rows: 1,
            consts: {
                title: {'': 'Category', de: 'Category'}
            },
            columns: 10,
            scenario: 'category_page',
            enabled : true
        }
    };

var YC_SEARCH_FIELDS = [
    {
        target: ["[name='sSearch']"]
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