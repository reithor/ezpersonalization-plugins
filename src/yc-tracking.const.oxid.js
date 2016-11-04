var YC_CUSTOMER_ID = 908;

var YC_CONSTS = {
    "currency": 'EUR',
    "currencySign": '&euro;'
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: "<h3 class='lightHead sectionHead'>{{{const.title}}}</h3><ul class='gridView clear'>" +
        "{{#each rows}}{{#each columns}}<li class='productData rendered-personal'><a href='{{{link}}}' " +
        "class='titleBlock title fn' title='{{{title}}}'><span>{{{title}}}</span><div class='gridPicture'>" +
        "<img src='{{{image}}}' style='width: 80%;' alt='{{{title}}}'></div></a><div class='priceBlock'><strong>" +
        "{{{price}}}</strong></div></li>{{/each}}{{/each}}</ul>",
        target: '#content',
        priority: 0,
        consts: {
            title: {'': 'Personal', en: 'Personal'}
        },
        rows: 2,
        columns: 5,
        scenario: 'top_clicked',
        enabled: true
    },
    bestseller: {
        html_template: "<h3 class='lightHead sectionHead'>{{{const.title}}}</h3><ul class='gridView clear'>" +
        "{{#each rows}}{{#each columns}}<li class='productData rendered-bestseller'><a href='{{{link}}}' " +
        "class='titleBlock title fn' title='{{{title}}}'><span>{{{title}}}</span><div class='gridPicture'>" +
        "<img src='{{{image}}}' style='width: 80%;' alt='{{{title}}}'></div></a><div class='priceBlock'><strong>" +
        "{{{price}}}</strong></div></li>{{/each}}{{/each}}</ul>",
        target: '#content',
        priority: 1,
        rows: 2,
        consts: {
            title: {'': 'Bestseller', en: 'Bestseller'}
        },
        columns: 5,
        scenario: 'top_clicked',
        enabled: true
    },
    upselling: {
        html_template: "<h3 class='lightHead sectionHead'>{{{const.title}}}</h3><ul class='gridView clear'>" +
        "{{#each rows}}{{#each columns}}<li class='productData rendered-upselling'><a href='{{{link}}}' " +
        "class='titleBlock title fn' title='{{{title}}}'><span>{{{title}}}</span><div class='gridPicture'>" +
        "<img src='{{{image}}}' style='width: 80%;' alt='{{{title}}}'></div></a><div class='priceBlock'><strong>" +
        "{{{price}}}</strong></div></li>{{/each}}{{/each}}</ul>",
        target: '#content',
        priority: 1,
        rows: 2,
        consts: {
            title: {'': 'Upselling', en: 'Upselling'}
        },
        columns: 5,
        scenario: 'top_clicked',
        enabled: true
    },
    related: {
        html_template: "<h3 class='lightHead sectionHead'>{{{const.title}}}</h3><ul class='gridView clear'>" +
        "{{#each rows}}{{#each columns}}<li class='productData rendered-related'><a href='{{{link}}}' " +
        "class='titleBlock title fn' title='{{{title}}}'><span>{{{title}}}</span><div class='gridPicture'>" +
        "<img src='{{{image}}}' style='width: 80%;' alt='{{{title}}}'></div></a><div class='priceBlock'><strong>" +
        "{{{price}}}</strong></div></li>{{/each}}{{/each}}</ul>",
        target: '#content',
        consts: {
            title: {'': 'Related', en: 'Related'}
        },
        priority: 0,
        rows: 2,
        columns: 5,
        scenario: 'top_clicked',
        enabled: true
    },
    crossselling: {
        html_template: "<h3 class='lightHead sectionHead'>{{{const.title}}}</h3><ul class='gridView clear'>" +
        "{{#each rows}}{{#each columns}}<li class='productData rendered-crossselling'><a href='{{{link}}}' " +
        "class='titleBlock title fn' title='{{{title}}}'><span>{{{title}}}</span><div class='gridPicture'>" +
        "<img src='{{{image}}}' style='width: 80%;' alt='{{{title}}}'></div></a><div class='priceBlock'><strong>" +
        "{{{price}}}</strong></div></li>{{/each}}{{/each}}</ul>",
        target: '#content',
        priority: 0,
        rows: 2,
        consts: {
            title: {'': 'Cross Sell', en: 'Cross Sell'}
        },
        columns: 5,
        scenario: 'top_clicked',
        enabled: true
    },
    category_page: {
        html_template: "<h3 class='lightHead sectionHead'>{{{const.title}}}</h3><ul class='gridView clear'>" +
        "{{#each rows}}{{#each columns}}<li class='productData rendered-category_page'><a href='{{{link}}}' " +
        "class='titleBlock title fn' title='{{{title}}}'><span>{{{title}}}</span><div class='gridPicture'>" +
        "<img src='{{{image}}}' style='width: 80%;' alt='{{{title}}}'></div></a><div class='priceBlock'><strong>" +
        "{{{price}}}</strong></div></li>{{/each}}{{/each}}</ul>",
        target: '#content',
        priority: 0,
        rows: 2,
        consts: {
            title: {'': 'Category', en: 'Category'}
        },
        columns: 5,
        scenario: 'top_clicked',
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