var YC_CUSTOMER_ID = 908;

var YC_CONSTS = {
    "currency": 'EUR',
    "currencySign": '&euro;'
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 0,
        consts: {
            title: {'': 'Personal', en: 'Personal'},
            more_info: {'': 'More info', en: 'More info'}
        },
        rows: 1,
        columns: 5,
        scenario: 'personal',
        enabled: true
    },
    bestseller: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 1,
        rows: 1,
        consts: {
            title: {'': 'Bestseller', en: 'Bestseller'},
            more_info: {'': 'More info', en: 'More info'}
        },
        columns: 5,
        scenario: 'bestseller',
        enabled: true
    },
    upselling: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 1,
        rows: 1,
        consts: {
            title: {'': 'Upselling', en: 'Upselling'},
            more_info: {'': 'More info', en: 'More info'}
        },
        columns: 5,
        scenario: 'up_selling',
        enabled: true
    },
    related: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 0,
        rows: 2,
        consts: {
            title: {'': 'Related', en: 'Related'},
            more_info: {'': 'More info', en: 'More info'}
        },
        columns: 5,
        scenario: 'related_products',
        enabled: true
    },
    crossselling: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        consts: {
            title: {'': 'Cross Sell', en: 'Cross Sell'},
            more_info: {'': 'More info', en: 'More info'}
        },
        columns: 5,
        scenario: 'cross_selling',
        enabled: true
    },
    category_page: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        consts: {
            title: {'': 'Category', en: 'Category'},
            more_info: {'': 'More info', en: 'More info'}
        },
        columns: 5,
        scenario: 'category_page',
        enabled: true
    },
    bundle: {
        html_template: '<div class="boxwrapper" id="boxwrapper_cross"><div class="page-header"><h2>{{{const.title}}}</h2>' +
        '</div><div class="list-container" id="cross">{{#each rows}}<div class="row gridView newItems">{{#each columns}}' +
        '<div class="productData col-xs-12 col-sm-6 col-md-3 productBox"><div class="picture text-center">' +
        '<a href="{{{link}}}" title="{{{title}}}"><img src="{{{image}}}" data-src="{{{image}}}" alt="{{{title}}}" ' +
        'class="img-responsive"></a></div><div class="listDetails text-center"><div class="title"><a id="cross_5" ' +
        'href="{{{link}}}" class="title" title="{{{title}}}"><span>{{{title}}}</span></a></div><div class="price text-center">' +
        '<div class="content"><span class="lead text-nowrap">{{{price}}}</span></div></div><div class="actions text-center">' +
        '<div class="btn-group"><a class="btn btn-primary" href="{{{link}}}">{{{../../const.more_info}}}</a></div></div></div>' +
        '</div>{{/each}}</div>{{/each}}</div></div>',
        target: '#content',
        position: 'APPEND',
        priority: 0,
        rows: 1,
        consts: {
            title: {'': 'Wird oft zusammen gekauft', en: 'Frequently brought together'},
            more_info: {'': 'More info', en: 'More info'},
            bundle2: {'' : 'Beide Produkte in den Warenkorb', en : 'Add both items to cart'},
            bundle3: {'' : 'Alle drei Produkte in den Warenkorb', en : 'Add all three items to cart'}
        },
        columns: 5,
        scenario: 'bundle',
        enabled: true
    }
};

var YC_SEARCH_FIELDS = [
    {
        target: ["#searchParam"]
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