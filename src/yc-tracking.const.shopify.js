var YC_CUSTOMER_ID = 905,
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
            target: '#product',
            position: 'APPEND',
            priority: 0,
            rows: 2,
            columns: 2,
            scenario: 'cross-rate',
            title: 'CrossRate',
            display: 1
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
            target: '#product',
            position: 'APPEND',
            priority: 0,
            rows: 2,
            columns: 2,
            scenario: 'cross_sell',
            title: 'CrossSelling',
            display: 1
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
            target: '#content',
            position: 'APPEND',
            priority: 0,
            rows: 2,
            columns: 2,
            scenario: 'personalized',
            title: 'Personal',
            display: 1
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
            target: '#product',
            position: 'APPEND',
            priority: 1,
            rows: 2,
            columns: 2,
            scenario: 'ultimately_bought',
            title: 'Upselling',
            display: 1
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
            target: '#content',
            position: 'APPEND',
            priority: 1,
            rows: 2,
            columns: 2,
            scenario: 'landing_page',
            title: 'Bestseller',
            display: 1
        },
        category_page: {
            html_template: "<div class='products'><h2>{{{title}}}</h2><ul " +
            "class='products'>{{#each rows}}{{#each columns}}<li class='product" +
            " rendered-category_page type-product {{#if onsale}}sale{{/if}} last'>" + 
            "<a href='{{link}}' title='{{{title}}}'>{{#if onsale}}<span " + 
            "class='onsale'>Sale!</span>{{/if}}<img width='300' height='300' " + 
            "src='{{image}}' alt='{{{title}}}' class='attachment-shop_catalog " + 
            "wp-post-image'><h3>{{{title}}}</h3>{{{rating}}}<div class='price'>" + 
            "{{{price}}}</div></a></li>{{/each}}{{/each}}</ul></div>",
            target: '#product',
            position: 'APPEND',
            priority: 0,
            rows: 2,
            columns: 2,
            scenario: 'category_page',
            title: 'Category',
            display: 1
        }
    };