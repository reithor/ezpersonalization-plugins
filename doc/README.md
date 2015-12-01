# JS-Tracking Documentation
For specific module use README.md files in module's directory.

## Log files can be found in

* Magento : /var/log/yoochoose.log
* Shopware: /logs/yoochoose.log
* OXID: /log/yoochoose.log
* WooCommerce: /wp-content/uploads/wc-logs/yoochoose.log

## Handlebars template property names
* rows - array of columns
* columns - array of products
* link - product link
* title - product title
* image - product thumbnail image
* price - formatted product price 
* every product has to have a class <b>'rendered-<i>box_key</i>'</b> so trackFollowEvent could be properly hooked on

### Example:
For box "related", in product li tag there is <b>rendered-related</b> class.

```js
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
            scenario: 'cross_rate'
        },
```