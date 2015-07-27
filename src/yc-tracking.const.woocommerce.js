var YC_CUSTOMER_ID = 904,
    YC_RECO_TEMPLATES = {
        product_crosssell: {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-product_crosssell type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
            "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
            "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
            " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
            "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
            "{{/each}}</ul></div>",
            target: '#main',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'cross-rate'
        },
        basket_crosssell: {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-basket_crosssell type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
            "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
            "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
            " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
            "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
            "{{/each}}</ul></div>",
            target: '#main',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'cross_sell'
        },
        home_personal : {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-home_personal type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
            "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
            "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
            " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
            "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
            "{{/each}}</ul></div>",
            target: '#main',
            priority: 0,
            rows: 1,
            columns: 1,
            scenario: 'landing_page'
        },
        product_upsell: {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-product_upsell type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
            "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
            "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
            " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
            "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
            "{{/each}}</ul></div>",
            target: '#main',
            priority: 1,
            rows: 2,
            columns: 5,
            scenario: 'ultimately_bought'
        },
        home_bestseller: {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-home_bestseller type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
            "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
            "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
            " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
            "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
            "{{/each}}</ul></div>",
            target: '#main',
            priority: 1,
            rows: 2,
            columns: 5,
            scenario: 'landing_page'
        },
        category_bestseller: {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-category_bestseller type-product {{#if onsale}}sale{{/if}} last'><a href='{{link}}' " +
            "title='{{{title}}}'>{{#if onsale}}<span class='onsale'>Sale!</span>" +
            "{{/if}}<img width='300' height='300' src='{{image}}' alt='{{{title}}}'" +
            " class='attachment-shop_catalog wp-post-image'><h3>{{{title}}}</h3>" +
            "{{{rating}}}<div class='price'>{{{price}}}</div></a></li>{{/each}}" +
            "{{/each}}</ul></div>",
            target: '#main',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'category_page'
        }
    };