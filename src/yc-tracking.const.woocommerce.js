var YC_CUSTOMER_ID = 904,
    YC_RECO_EVENT_HOST = '//event.yoochoose.net/api/',
    YC_RECO_RECOM_HOST = '//reco.yoochoose.net/api/',
    YC_RECO_TEMPLATES = {
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
            rows: 2,
            columns: 5,
            scenario: 'category_page'
        }
    };