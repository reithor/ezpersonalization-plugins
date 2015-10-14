# WooCommerce API Client

### Requirements
* Change default permalink settings in WordPress (Settings -> Permalinks)
* Enable the REST API checkbox in WooCommerce settings (WooCommerce -> Settings)
* Generate WooCommerce API Keys (Consumer Key and Consumer Secret) in user settings

### WooCommerce REST API docs
* http://woothemes.github.io/woocommerce-rest-api-docs/

## Product Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset

### Call example
```php
    $params = array(
        'filter[limit]' => 2,
        'filter[offset]' => 10,
    );
    $response = $client->getProducts($params);
```

### Response example
```json
{  
   "products":[  
      {  
         "title":"Ship Your Idea",
         "id":67,
         "created_at":"2013-06-07T11:22:50Z",
         "updated_at":"2013-06-07T11:22:50Z",
         "type":"simple",
         "status":"publish",
         "downloadable":false,
         "virtual":false,
         "permalink":"http:\/\/localhost\/woocommerce\/product\/ship-your-idea-3\/",
         "sku":"",
         "price":"15.00",
         "regular_price":"15.00",
         "sale_price":null,
         "price_html":"£15.00<\/span>",
         "taxable":false,
         "tax_status":"taxable",
         "tax_class":"",
         "managing_stock":false,
         "stock_quantity":0,
         "in_stock":true,
         "backorders_allowed":false,
         "backordered":false,
         "sold_individually":false,
         "purchaseable":true,
         "featured":false,
         "visible":true,
         "catalog_visibility":"visible",
         "on_sale":false,
         "weight":null,
         "dimensions":{  
            "length":"",
            "width":"",
            "height":"",
            "unit":"cm"
         },
         "shipping_required":true,
         "shipping_taxable":true,
         "shipping_class":"",
         "shipping_class_id":null,
         "description":" 
Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.<\/p>\n",
         "short_description":" 

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.<\/p>\n",
         "reviews_allowed":true,
         "average_rating":"0.00",
         "rating_count":0,
         "related_ids":[  
            70,
            73,
            76,
            79
         ],
         "upsell_ids":[  
            22,
            40
         ],
         "cross_sell_ids":[  
            22,
            40
         ],
         "parent_id":0,
         "categories":[  
            "Posters"
         ],
         "tags":[  

         ],
         "images":[  
            {  
               "id":68,
               "created_at":"2013-06-07T11:21:34Z",
               "updated_at":"2013-06-07T11:21:34Z",
               "src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/poster_1_up.jpg",
               "title":"poster_1_up",
               "alt":"",
               "position":0
            },
            {  
               "id":69,
               "created_at":"2013-06-07T11:22:05Z",
               "updated_at":"2013-06-07T11:22:05Z",
               "src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/Poster_1_flat.jpg",
               "title":"Poster_1_flat",
               "alt":"",
               "position":1
            }
         ],
         "featured_src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/poster_1_up.jpg",
         "attributes":[  

         ],
         "downloads":[  

         ],
         "download_limit":0,
         "download_expiry":0,
         "download_type":"",
         "purchase_note":"",
         "total_sales":0,
         "variations":[  

         ],
         "parent":[  

         ]
      },
      {  
         "title":"Woo Logo",
         "id":60,
         "created_at":"2013-06-07T11:12:55Z",
         "updated_at":"2013-06-07T11:12:55Z",
         "type":"simple",
         "status":"publish",
         "downloadable":false,
         "virtual":false,
         "permalink":"http:\/\/localhost\/woocommerce\/product\/woo-logo-2\/",
         "sku":"",
         "price":"35.00",
         "regular_price":"35.00",
         "sale_price":null,
         "price_html":"£35.00<\/span>",
         "taxable":false,
         "tax_status":"taxable",
         "tax_class":"",
         "managing_stock":false,
         "stock_quantity":0,
         "in_stock":true,
         "backorders_allowed":false,
         "backordered":false,
         "sold_individually":false,
         "purchaseable":true,
         "featured":false,
         "visible":true,
         "catalog_visibility":"visible",
         "on_sale":false,
         "weight":null,
         "dimensions":{  
            "length":"",
            "width":"",
            "height":"",
            "unit":"cm"
         },
         "shipping_required":true,
         "shipping_taxable":true,
         "shipping_class":"",
         "shipping_class_id":null,
         "description":" 

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.<\/p>\n",
         "short_description":" 

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.<\/p>\n",
         "reviews_allowed":true,
         "average_rating":"4.00",
         "rating_count":2,
         "related_ids":[  
            19,
            22,
            31,
            34,
            37
         ],
         "upsell_ids":[  

         ],
         "cross_sell_ids":[  
            15
         ],
         "parent_id":0,
         "categories":[  
            "Clothing",
            "Hoodies"
         ],
         "tags":[  

         ],
         "images":[  
            {  
               "id":61,
               "created_at":"2013-06-07T11:12:02Z",
               "updated_at":"2013-06-07T11:12:02Z",
               "src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/hoodie_6_front.jpg",
               "title":"hoodie_6_front",
               "alt":"",
               "position":0
            },
            {  
               "id":62,
               "created_at":"2013-06-07T11:12:16Z",
               "updated_at":"2013-06-07T11:12:16Z",
               "src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/hoodie_6_back.jpg",
               "title":"hoodie_6_back",
               "alt":"",
               "position":1
            }
         ],
         "featured_src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/hoodie_6_front.jpg",
         "attributes":[  

         ],
         "downloads":[  

         ],
         "download_limit":0,
         "download_expiry":0,
         "download_type":"",
         "purchase_note":"",
         "total_sales":0,
         "variations":[  

         ],
         "parent":[  

         ]
      }
   ]
}
```
## Product export by product Id.

### Call example
```php
    $data = $client->getProduct(50);
```

### Response example
```json
{  
   "product":{  
      "title":"Patient Ninja",
      "id":50,
      "created_at":"2013-06-07T11:03:56Z",
      "updated_at":"2013-06-07T11:03:56Z",
      "type":"simple",
      "status":"publish",
      "downloadable":false,
      "virtual":false,
      "permalink":"http:\/\/localhost\/woocommerce\/product\/patient-ninja\/",
      "sku":"",
      "price":"35.00",
      "regular_price":"35.00",
      "sale_price":null,
      "price_html":"£35.00<\/span>",
      "taxable":false,
      "tax_status":"taxable",
      "tax_class":"",
      "managing_stock":false,
      "stock_quantity":0,
      "in_stock":true,
      "backorders_allowed":false,
      "backordered":false,
      "sold_individually":false,
      "purchaseable":true,
      "featured":false,
      "visible":true,
      "catalog_visibility":"visible",
      "on_sale":false,
      "weight":null,
      "dimensions":{  
         "length":"",
         "width":"",
         "height":"",
         "unit":"cm"
      },
      "shipping_required":true,
      "shipping_taxable":true,
      "shipping_class":"",
      "shipping_class_id":null,
      "description":" 
Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.<\/p>\n",
      "short_description":" 

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.<\/p>\n",
      "reviews_allowed":true,
      "average_rating":"4.67",
      "rating_count":3,
      "related_ids":[  
         34,
         37,
         40,
         47,
         53
      ],
      "upsell_ids":[  

      ],
      "cross_sell_ids":[  
         53
      ],
      "parent_id":0,
      "categories":[  
         "Clothing",
         "Hoodies"
      ],
      "tags":[  

      ],
      "images":[  
         {  
            "id":51,
            "created_at":"2013-06-07T11:03:16Z",
            "updated_at":"2013-06-07T11:03:16Z",
            "src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/hoodie_3_front.jpg",
            "title":"hoodie_3_front",
            "alt":"",
            "position":0
         },
         {  
            "id":52,
            "created_at":"2013-06-07T11:03:50Z",
            "updated_at":"2013-06-07T11:03:50Z",
            "src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/hoodie_3_back.jpg",
            "title":"hoodie_3_back",
            "alt":"",
            "position":1
         }
      ],
      "featured_src":"http:\/\/localhost\/woocommerce\/wp-content\/uploads\/2013\/06\/hoodie_3_front.jpg",
      "attributes":[  

      ],
      "downloads":[  

      ],
      "download_limit":0,
      "download_expiry":0,
      "download_type":"",
      "purchase_note":"",
      "total_sales":0,
      "variations":[  

      ],
      "parent":[  

      ]
   }
}
```
## Categories export

### Call example
```php
    $response = $client->getCategories();
```

### Response example
```json
{  
   "product_categories":[  
      {  
         "id":15,
         "name":"Albums",
         "slug":"albums",
         "parent":11,
         "description":"",
         "count":4
      },
      {  
         "id":9,
         "name":"Clothing",
         "slug":"clothing",
         "parent":0,
         "description":"",
         "count":13
      },
      ...
   ]
}
```

## Category export by category Id.

### Call example
```php
    $response = $client->getCategory(11);
```

### Response example
```json
{  
   "product_category":{  
      "id":11,
      "name":"Music",
      "slug":"music",
      "parent":0,
      "description":"",
      "count":6
   }
}
```

## Customers export

### Call example
```php
    $params = array(
        'filter[limit]' => 5,
        'filter[offset]' => 0,
    );
    $response = $client->getCustomers($params);
```

### Response example
```json
{  
   "customers":[  
      {  
         "id":2,
         "created_at":"2015-04-27T11:59:57Z",
         "email":"demo@demo.com",
         "first_name":"",
         "last_name":"",
         "username":"demo1",
         "role":"customer",
         "last_order_id":null,
         "last_order_date":null,
         "orders_count":0,
         "total_spent":"0.00",
         "avatar_url":"http:\/\/2.gravatar.com\/avatar\/?s=96",
         "billing_address":{  
            "first_name":"",
            "last_name":"",
            "company":"",
            "address_1":"",
            "address_2":"",
            "city":"",
            "state":"",
            "postcode":"",
            "country":"",
            "email":"",
            "phone":""
         },
         "shipping_address":{  
            "first_name":"",
            "last_name":"",
            "company":"",
            "address_1":"",
            "address_2":"",
            "city":"",
            "state":"",
            "postcode":"",
            "country":""
         }
      },
      {  
         "id":3,
         "created_at":"2015-04-30T07:37:34Z",
         "email":"demo@demod1.com",
         "first_name":"",
         "last_name":"",
         "username":"demo",
         "role":"customer",
         "last_order_id":"110",
         "last_order_date":"2015-05-04T07:36:44Z",
         "orders_count":2,
         "total_spent":"0.00",
         "avatar_url":"http:\/\/2.gravatar.com\/avatar\/?s=96",
         "billing_address":{  
            "first_name":"Vojin",
            "last_name":"Jdoianfja",
            "company":"apfa",
            "address_1":"pamfpiamgia",
            "address_2":"35",
            "city":"Ffasfaf",
            "state":"Asdaf",
            "postcode":"11001",
            "country":"RS",
            "email":"demo@demod1.com",
            "phone":"51568615"
         },
         "shipping_address":{  
            "first_name":"Vojin",
            "last_name":"Jdoianfja",
            "company":"apfa",
            "address_1":"pamfpiamgia",
            "address_2":"35",
            "city":"Ffasfaf",
            "state":"Asdaf",
            "postcode":"11001",
            "country":"RS"
         }
      }
      ...
   ]
}
```

## Customer export by customer Id.

### Call example
```php
    $response = $client->getCustomer(3);
```

### Response example
```json
{  
   "customer":{  
      "id":3,
      "created_at":"2015-04-30T07:37:34Z",
      "email":"demo@demod1.com",
      "first_name":"",
      "last_name":"",
      "username":"demo",
      "role":"customer",
      "last_order_id":"110",
      "last_order_date":"2015-05-04T07:36:44Z",
      "orders_count":2,
      "total_spent":"0.00",
      "avatar_url":"http:\/\/2.gravatar.com\/avatar\/?s=96",
      "billing_address":{  
         "first_name":"Vojin",
         "last_name":"Jdoianfja",
         "company":"apfa",
         "address_1":"pamfpiamgia",
         "address_2":"35",
         "city":"Ffasfaf",
         "state":"Asdaf",
         "postcode":"11001",
         "country":"RS",
         "email":"demo@demod1.com",
         "phone":"51568615"
      },
      "shipping_address":{  
         "first_name":"Vojin",
         "last_name":"Jdoianfja",
         "company":"apfa",
         "address_1":"pamfpiamgia",
         "address_2":"35",
         "city":"Ffasfaf",
         "state":"Asdaf",
         "postcode":"11001",
         "country":"RS"
      }
   }
}
```
