var YC_RECO_TEMPLATES = {
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
            rows: 2,
            columns: 5,
            scenario: 'cross-rate'
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
            rows: 2,
            columns: 5,
            scenario: 'cross_sell'
        },
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
            rows: 2,
            columns: 2,
            scenario: 'personalized'
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
            rows: 2,
            columns: 5,
            scenario: 'ultimately_bought'
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
            rows: 2,
            columns: 2,
            scenario: 'landing_page'
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
            target: '.category-products',
            priority: 0,
            rows: 2,
            columns: 5,
            scenario: 'category_page'
        }
    };