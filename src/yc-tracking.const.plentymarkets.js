// {id} will be changed with current product id

var YC_CUSTOMER_ID = 906,
    YC_RECO_TEMPLATES = {
        personal: {
            html_template: "<div class='col-lg-12'><h3>{{title}}</h3><div class='categoryView isGridView'>" +
            "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
            "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-personal margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
            "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
            "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
            "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
            " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></div></div>" +
            "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
            "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
            "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
            "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{title}}</a>" +
            "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
            target: '.container .row .categoryView',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'personalized',
            title: 'Personal',
            display: 1
        },
        bestseller: {
            html_template: "<div class='col-lg-12'><h3>{{title}}</h3><div class='categoryView isGridView'>" +
            "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
            "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-bestseller margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
            "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
            "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
            "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
            " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></div></div>" +
            "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
            "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
            "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
            "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{title}}</a>" +
            "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
            target: '.container .row .categoryView',
            priority: 1,
            rows: 2,
            columns: 5,
            scenario: 'landing_page',
            title: 'Bestseller',
            display: 1
        },
        related: {
            html_template: "<div class='col-lg-12'><h3>{{title}}</h3><div class='categoryView isGridView'>" +
            "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
            "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-related margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
            "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
            "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
            "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
            " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></div></div>" +
            "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
            "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
            "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
            "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{title}}</a>" +
            "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
            target: '.wrapper .singleItemView .row',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'cross-rate',
            title: 'CrossRate',
            display: 1
        },
        upselling: {
            html_template: "<div class='col-lg-12'><h3>{{title}}</h3><div class='categoryView isGridView'>" +
            "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
            "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-upselling margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
            "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
            "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
            "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
            " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></div></div>" +
            "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
            "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
            "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
            "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{title}}</a>" +
            "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
            target: '.wrapper .singleItemView .row',
            priority: 1,
            rows: 2,
            columns: 5,
            scenario: 'ultimately_bought',
            title: 'Upselling',
            display: 1
        },
        crossselling: {
            html_template: "<div class='col-lg-12'><h3>{{title}}</h3><div class='categoryView isGridView'>" +
            "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
            "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-crossselling margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
            "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
            "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
            "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
            " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></div></div>" +
            "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
            "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
            "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
            "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{title}}</a>" +
            "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
            target: '.container.checkoutDetails.formControlWrapper',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'cross_sell',
            title: 'CrossSelling',
            display: 1
        },
        category_page: {
            html_template: "<div class='col-lg-12'><h3>{{title}}</h3><div class='categoryView isGridView'>" +
            "<ul class='row categoryDetails isGridView' data-plenty-details='categoryDetails'>{{#each rows}}{{#each columns}}" +
            "<li class='col-xs-12 col-sm-4 col-md-3 col-lg-2 rendered-category_page margin-bottom-2 center itemBox tileView onHover action-2' data-plenty='item' data-plenty-id='{{itemId}}'>" +
            "<div class='itemBoxInner'><div id='previewImages-{{itemId}}' class='h-lg-2 h-md-3 h-sm-2 h-xs-6'>" +
            "<div class='imageBox h-lg-2 h-md-3 h-sm-2 h-xs-6 adapt-line-height'>" +
            "<img class='center img-responsive id-{{itemId}}' src='{{image}}' data-original='{{image}}' data-plenty-link='item-{{itemId}}'" +
            " data-plenty-lazyload='fadeIn' style='display: inline-block;' ></div></div>" +
            "<a class='name block' href='{{url}}' data-plenty-href='item-{{itemId}}'>{{title}}</a><p class='price bold' data-plenty-link='item-{{itemId}}'>" +
            "<span class='large linkToItem'>{{{price}}}</span></p><p class='small' data-plenty-link='item-{{itemId}}'>&nbsp;</p>" +
            "<div class='visible-hover'><div class='basketButtonContainer clearfix'><div class='buttonBox isViewItem'>" +
            "<a class='btn btn-primary' href='{{url}}'><span class='glyphicon glyphicon-eye-open'></span>{{title}}</a>" +
            "</div></div></div></div></li>{{/each}}{{/each}}</ul></div></div>",
            target: '.container.categoryView .row',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'category_page',
            title: 'Category',
            display: 1
        }
    };