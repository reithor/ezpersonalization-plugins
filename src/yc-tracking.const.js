var YC_CUSTOMER_ID = 904;
var YC_RECO_TEMPLATES = {
            related : {
                html_template : "<section class='yc_recommendations'><h3></h3><div><span class='yc_id'>" +
                    "</span><img src='' /><a class='yc_title' href=''></a>" +
                    "<span class='yc_price'></span><span class='yc_currency'></span></div></section>",
                target : 'main',
                rows : 2,
                columns : 3,
                scenario : 'cross-rate'
            },
            crosssaling : {
                html_template : "<section class='yc_recommendations'><h3></h3><div><span class='yc_id'>" +
                    "</span><img src='' /><a class='yc_title' href=''></a>" +
                    "<span class='yc_price'></span><span class='yc_currency'></span></div></section>",
                target : 'main',
                rows : 2,
                columns : 3,
                scenario : 'cross_sell'
            },
            personal : {
                html_template : "<section class='yc_recommendations'><h3></h3><div><span class='yc_id'>" +
                    "</span><img src='' /><a class='yc_title' href=''></a>" +
                    "<span class='yc_price'></span><span class='yc_currency'></span></div></section>",
                target : 'main',
                rows : 2,
                columns : 3,
                scenario : 'personalized'
            },
            upselling : {
                html_template : "<section class='yc_recommendations'><h3></h3><div><span class='yc_id'>" +
                    "</span><img src='' /><a class='yc_title' href=''></a>" +
                    "<span class='yc_price'></span><span class='yc_currency'></span></div></section>",
                target : 'main',
                rows : 2,
                columns : 3,
                scenario : 'ultimately_bought'
            },
            bestseller : {
                html_template : "<section class='yc_recommendations'><h3></h3><div><span class='yc_id'>" +
                    "</span><img src='' /><a class='yc_title' href=''></a>" +
                    "<span class='yc_price'></span><span class='yc_currency'></span></div></section>",
                target : 'main',
                rows : 2,
                columns : 3,
                scenario : 'landing_page'
            }
        };