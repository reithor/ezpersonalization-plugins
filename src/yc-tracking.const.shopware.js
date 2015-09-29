var YC_CUSTOMER_ID = 907,
    YC_RECO_TEMPLATES = {
        related: {
            html_template: "<div class='viewlast'><h2 class='heading'>{{{title}}}</h2><ul " +
                ">{{#each rows}}{{#each columns}}<li class='rendered-related lastview_rule_last'><a " + 
                "id='{{id}}' href='{{link}}' rel='nofollow' class='article_image' " +
                "style='background: #fff url({{image}}) no-repeat center center'></a>" + 
                "<a rel='nofollow' class='article_description' title='{{title}}' href='{{link}}'>" + 
                "{{title}}</a></li>{{/each}}{{/each}}</ul></div>",
            target: '.container_20',
            title: 'Cross Rate',
            priority: 0,
            rows: 1,
            columns: 10,
            scenario: 'cross_rate',
            enabled : true
        },
        crossselling: {
            html_template: "<div class='viewlast'><h2 class='heading'>{{{title}}}</h2><ul " +
                ">{{#each rows}}{{#each columns}}<li class='rendered-crossselling lastview_rule_last'><a " + 
                "id='{{id}}' href='{{link}}' rel='nofollow' class='article_image' " +
                "style='background: #fff url({{image}}) no-repeat center center'></a>" + 
                "<a rel='nofollow' class='article_description' title='{{title}}' href='{{link}}'>" + 
                "{{title}}</a></li>{{/each}}{{/each}}</ul></div>",
            target: '.container_20',
            priority: 0,
            rows: 1,
            title: 'Cross Sell',
            columns: 10,
            scenario: 'cross_sell',
            enabled : true
        },
        personal : {
            html_template: "<div class='viewlast'><h2 class='heading'>{{{title}}}</h2><ul " +
                ">{{#each rows}}{{#each columns}}<li class='rendered-personal lastview_rule_last'><a " + 
                "id='{{id}}' href='{{link}}' rel='nofollow' class='article_image' " +
                "style='background: #fff url({{image}}) no-repeat center center'></a>" + 
                "<a rel='nofollow' class='article_description' title='{{title}}' href='{{link}}'>" + 
                "{{title}}</a></li>{{/each}}{{/each}}</ul></div>",
            target: '.container_20',
            priority: 0,
            title: 'Personalized',
            rows: 1,
            columns: 10,
            scenario: '.personalized',
            enabled : true
        },
        upselling: {
            html_template:  "<div class='viewlast'><h2 class='heading'>{{{title}}}</h2><ul " +
                ">{{#each rows}}{{#each columns}}<li class='rendered-upselling lastview_rule_last'><a " + 
                "id='{{id}}' href='{{link}}' rel='nofollow' class='article_image' " +
                "style='background: #fff url({{image}}) no-repeat center center'></a>" + 
                "<a rel='nofollow' class='article_description' title='{{title}}' href='{{link}}'>" + 
                "{{title}}</a></li>{{/each}}{{/each}}</ul></div>",
            target: '.container_20',
            priority: 1,
            rows: 1,
            title: 'Upselling',
            columns: 10,
            scenario: 'ultimately_bought',
            enabled : true
        },
        bestseller: {
            html_template:  "<div class='viewlast'><h2 class='heading'>{{{title}}}</h2><ul " +
                ">{{#each rows}}{{#each columns}}<li class='rendered-bestseller lastview_rule_last'><a " + 
                "id='{{id}}' href='{{link}}' rel='nofollow' class='article_image' " +
                "style='background: #fff url({{image}}) no-repeat center center'></a>" + 
                "<a rel='nofollow' class='article_description' title='{{title}}' href='{{link}}'>" + 
                "{{title}}</a></li>{{/each}}{{/each}}</ul></div>",
            target: '.container_20',
            priority: 1,
            rows: 1,
            title: 'Bestseller',
            columns: 10,
            scenario: 'landing_page',
            enabled : true
        },
        category_page: {
            html_template:  "<div class='viewlast'><h2 class='heading'>{{{title}}}</h2><ul " +
                ">{{#each rows}}{{#each columns}}<li class='rendered-category_page lastview_rule_last'><a " + 
                "id='{{id}}' href='{{link}}' rel='nofollow' class='article_image' " +
                "style='background: #fff url({{image}}) no-repeat center center'></a>" + 
                "<a rel='nofollow' class='article_description' title='{{title}}' href='{{link}}'>" + 
                "{{title}}</a></li>{{/each}}{{/each}}</ul></div>",
            target: '.container_20',
            priority: 0,
            rows: 1,
            title: 'Category',
            columns: 10,
            scenario: 'category_page',
            enabled : true
        }
    };