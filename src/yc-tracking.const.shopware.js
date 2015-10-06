
var YC_CUSTOMER_ID = 907;

var YC_CONSTS = {
    "currency" : 'EUR',
    "currencySign" : '&euro;'
};

var YC_RECO_TEMPLATES = {
        related: {
            html_template: "<div><h2 class='heading'>{{{const.title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-related' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{link}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
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
            html_template: "<div><h2 class='heading'>{{{const.title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-crossselling' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{link}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
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
            html_template: "<div><h2 class='heading'>{{{const.title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-personal' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{link}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 0,
            consts: {
                title: {'': 'Personalized', de: 'Personalized'}
            },
            rows: 1,
            columns: 10,
            scenario: '.personalized',
            enabled : true
        },
        upselling: {
            html_template: "<div><h2 class='heading'>{{{const.title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-upselling' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{link}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 1,
            rows: 1,
            consts: {
                title: {'': 'Upselling', de: 'Upselling'}
            },
            columns: 10,
            scenario: 'ultimately_bought',
            enabled : true
        },
        bestseller: {
            html_template: "<div><h2 class='heading'>{{{const.title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-bestseller' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{link}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 1,
            rows: 1,
            consts: {
                title: {'': 'Bestseller', de: 'Bestseller'}
            },
            columns: 10,
            scenario: 'landing_page',
            enabled : true
        },
        category_page: {
            html_template: "<div><h2 class='heading'>{{{const.title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-category_page' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{link}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
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
        target: ["#searchfield"]
    }
];

var YC_SEARCH_TEMPLATES = {
    ITEM: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
        "<a href='{{url}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
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
        "<a href='{{url}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
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
        "<a href='{{url}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
        "</span></a></li>{{else}}<span class='yc-no-results'>No vendor results</span>{{/each}}</ul>",
        amount: 10,
        enabled: false,
        priority: 3,
        consts: {
            "title": {'': 'Recommended Vendor', 'de': 'Empfohlene Vendor'}
        }
    }
};