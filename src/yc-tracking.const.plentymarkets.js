
var YC_CUSTOMER_ID = 906;

var YC_CONSTS = {
    "currency" : 'EUR',
    "currencySign" : '&euro;',
    "details" : {'': 'View details', de: ' Artikel ansehen'}
};

var YC_RECO_TEMPLATES = {
    personal: {
        html_template: "<div class='col-lg-12'><h3>{{{const.title}}}</h3><div class='categoryView isGridView'>" +
        "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-personal margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
        "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
        "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
        "<a href='{{url}}'><img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
        " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></a></div></div>" +
        "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
        "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
        "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
        "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{{../../const.details}}}</a>" +
        "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.container .row .categoryView',
        priority: 0,
        rows: 2,
        columns: 5,
        scenario: 'personalized',
        consts: {
            title: {'': 'Personal', de: 'Personal'}
        },
        enabled: true
    },
    bestseller: {
        html_template: "<div class='col-lg-12'><h3>{{{const.title}}}</h3><div class='categoryView isGridView'>" +
        "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-bestseller margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
        "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
        "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
        "<a href='{{url}}'><img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
        " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></a></div></div>" +
        "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
        "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
        "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
        "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{{../../const.details}}}</a>" +
        "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.container .row .categoryView',
        priority: 1,
        rows: 2,
        columns: 5,
        scenario: 'landing_page',
        consts: {
            title: {'': 'Bestseller', de: 'Bestseller'}
        },
        enabled: true
    },
    related: {
        html_template: "<div class='col-lg-12'><h3>{{{const.title}}}</h3><div class='categoryView isGridView'>" +
        "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-related margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
        "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
        "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
        "<a href='{{url}}'><img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
        " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></a></div></div>" +
        "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
        "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
        "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
        "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{{../../const.details}}}</a>" +
        "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.wrapper .singleItemView .row',
        priority: 0,
        rows: 2,
        columns: 5,
        scenario: 'cross-rate',
        consts: {
            title: {'': 'CrossRate', de: 'CrossRate'}
        },
        enabled: true
    },
    upselling: {
        html_template: "<div class='col-lg-12'><h3>{{{const.title}}}</h3><div class='categoryView isGridView'>" +
        "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-upselling margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
        "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
        "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
        "<a href='{{url}}'><img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
        " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></a></div></div>" +
        "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
        "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
        "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
        "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{{../../const.details}}}</a>" +
        "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.wrapper .singleItemView .row',
        priority: 1,
        rows: 2,
        columns: 5,
        scenario: 'ultimately_bought',
        consts: {
            title: {'': 'Upselling', de: 'Upselling'}
        },
        enabled: true
    },
    crossselling: {
        html_template: "<div class='col-lg-12'><h3>{{{const.title}}}</h3><div class='categoryView isGridView'>" +
        "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-crossselling margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
        "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
        "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
        "<a href='{{url}}'><img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
        " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></a></div></div>" +
        "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
        "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
        "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
        "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{{../../const.details}}}</a>" +
        "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.container.checkoutDetails.formControlWrapper',
        priority: 0,
        rows: 2,
        columns: 5,
        scenario: 'cross_sell',
        consts: {
            title: {'': 'CrossSelling', de: 'CrossSelling'}
        },
        enabled: true
    },
    category_page: {
        html_template: "<div class='col-lg-12'><h3>{{{const.title}}}</h3><div class='categoryView isGridView'>" +
        "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
        "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-category_page margin-bottom-2 center itemBox tileView" +
        " onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'><div class='itemBoxInner'><div id='previewImages-{{itemId}}'" +
        " class='h-lg-2 h-md-3 h-sm-2 h-xs-6'><div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'><a href='{{url}}'>" +
        "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
        " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></a></div></div><a class='name block' " +
        "href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
        "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
        "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
        "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{{../../const.details}}}</a>" +
        "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
        target: '.container.categoryView .row',
        priority: 0,
        rows: 2,
        columns: 5,
        scenario: 'category_page',
        consts: {
            title: {'': 'Category', de: 'Category'}
        },
        enabled: true
    }
};

var YC_SEARCH_FIELDS = [
    {
        target: ["#LiveSearchParam"]
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