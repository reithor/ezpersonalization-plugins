# Magento API Client

## Product Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* store - (Integer) From store with this id (optional, if not sent default store view will be selected)

### Call example
```php
    $params = array(
        'limit' => 10,
        'offset' => 100,
        'store' => 1,
    );
    $response = $client->getShopData('yoochoose/products', 'GET', $params);
```

### Response example
```json
{  
   "products":[  
      {  
         "231":{
            "entity_id":"231",
            "name":"French Cuff Cotton Twill Oxford",
            "image":"http:\/\/localhost\/magento\/media\/catalog\/product\/m\/s\/msj000t_1.jpg",
            "description":"Button front. Long sleeves. Tapered collar, chest pocket, french cuffs.",
            "price":"190.0000",
            "is_salable":"1",
            "stock_item":{  
               "is_in_stock":"1"
            },
            "url":"http:\/\/localhost\/magento\/french-cuff-cotton-twill-oxford.html",
            "image_size":"600x900",
            "categories":[  
               "Men\/Shirts"
            ],
            "tags":[

            ]
         },
         "232":{
            "entity_id":"232",
            "name":"French Cuff Cotton Twill Oxford",
            "image":"http:\/\/localhost\/magento\/media\/catalog\/product\/m\/s\/msj000t_1.jpg",
            "description":"Button front. Long sleeves. Tapered collar, chest pocket, french cuffs.",
            "price":"190.0000",
            "is_salable":"1",
            "stock_item":{  
               "is_in_stock":"1"
            },
            "url":"http:\/\/localhost\/magento\/french-cuff-cotton-twill-oxford-563.html",
            "image_size":"600x900",
            "categories":[  
               "Men\/Shirts"
            ],
            "tags":[

            ]
         }
      }
   ]
}
```
## Subscribers Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset

### Call example
```php
    $params = array(
        'limit' => 10,
        'offset' => 100,
    );
    $response = $client->getShopData('yoochoose/customers', 'GET', $params);
```

### Response example
```json
{
    "subscribers":[  
      [  
         {  
            "id":"64",
            "subscriber_id":"23",
            "subscriber_code":"4qzouegjuw6o7ytu6n2g6lxm4to3wcmm"
         },
         {  
            "id":"137",
            "subscriber_id":"24",
            "subscriber_code":"1qw9p2z4k50kqeo3xqyzpfqmwtwvtd7r"
         }
      ]
   ]
}
```
## Store Views Export

### Call example
```php
    $response = $client->getShopData('yoochoose/storeviews', 'GET', array());
```

### Response example
```json
{
    "views":[  
      [  
         {  
            "id":"1",
            "name":"English",
            "item_type_id":"1",
            "languange":"en-US"
         },
         {  
            "id":"2",
            "name":"French",
            "item_type_id":"1",
            "languange":"en"
         },
         {  
            "id":"3",
            "name":"German",
            "item_type_id":"1",
            "languange":"en-US"
         }
      ]
   ]
}
```

## Categories Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* store - (Integer) From store with this id (optional, if not sent default store view will be selected)

### Call example
```php
    $params = array(
        'limit' => 3,
        'offset' => 5,
        'store' => 1,
    );
    $response = $client->getShopData('yoochoose/categories', 'GET', $params);
```

### Response example
```json
{
    "categories": [
        [
            {
                "id": "8",
                "path": "sale",
                "url": "http://localhost/magento/sale.html",
                "name": "Sale",
                "level": "2",
                "parentId": 2
            },
            {
                "id": "9",
                "path": "vip",
                "url": "http://localhost/magento/vip.html",
                "name": "VIP",
                "level": "2",
                "parentId": 2
            },
            {
                "id": "10",
                "path": "women/new-arrivals",
                "url": "http://localhost/magento/women/new-arrivals.html",
                "name": "New Arrivals",
                "level": "3",
                "parentId": 4
            }
        ]
    ]
}
```
