var YC_CUSTOMER_ID = 907,
    YC_RECO_TEMPLATES = {
        related: {
            html_template: "<div><h2 class='heading'>{{{title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-related' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{url}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            title: 'Cross Rate',
            priority: 0,
            rows: 1,
            columns: 10,
            scenario: 'cross_rate',
            enabled : true
        },
        crossselling: {
            html_template: "<div><h2 class='heading'>{{{title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-crossselling' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{url}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 0,
            rows: 1,
            title: 'Cross Sell',
            columns: 10,
            scenario: 'cross_sell',
            enabled : true
        },
        personal : {
            html_template: "<div><h2 class='heading'>{{{title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-personal' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{url}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 0,
            title: 'Personalized',
            rows: 1,
            columns: 10,
            scenario: '.personalized',
            enabled : true
        },
        upselling: {
            html_template: "<div><h2 class='heading'>{{{title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-upselling' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{url}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 1,
            rows: 1,
            title: 'Upselling',
            columns: 10,
            scenario: 'ultimately_bought',
            enabled : true
        },
        bestseller: {
            html_template: "<div><h2 class='heading'>{{{title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-bestseller' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{url}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 1,
            rows: 1,
            title: 'Bestseller',
            columns: 10,
            scenario: 'landing_page',
            enabled : true
        },
        category_page: {
            html_template: "<div><h2 class='heading'>{{{title}}}</h2>{{#each rows}}{{#each columns}}" +
            "<div class='article-element'><div class='artbox rendered-category_page' style='height: 250px;'><div class='inner'>" +
            "<a href='{{link}}' title='{{{title}}}' style='text-align: center;'><img src='{{image}}' width='157' height='160' />" +
            "</a><a href='{{url}}' class='title' title='{{{title}}}' style='width: 157px;'>{{{title}}}</a><p>" +
            "<span class='price'>{{{price}}}</span></p><div class='actions'><a href='{{link}}' class='more'>See details</a></div>" +
            "</div></div></div>{{/each}}{{/each}}</div>",
            target: '.container_20',
            priority: 0,
            rows: 1,
            title: 'Category',
            columns: 10,
            scenario: 'category_page',
            enabled : true
        }
    };