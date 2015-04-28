# Magento API Client Documentation

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
            "image":"http:\/\/testshops.localhost\/magento\/media\/catalog\/product\/m\/s\/msj000t_1.jpg",
            "description":"Button front. Long sleeves. Tapered collar, chest pocket, french cuffs.",
            "price":"190.0000",
            "is_salable":"1",
            "stock_item":{  
               "is_in_stock":"1"
            },
            "url":"http:\/\/testshops.localhost\/magento\/french-cuff-cotton-twill-oxford.html",
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
            "image":"http:\/\/testshops.localhost\/magento\/media\/catalog\/product\/m\/s\/msj000t_1.jpg",
            "description":"Button front. Long sleeves. Tapered collar, chest pocket, french cuffs.",
            "price":"190.0000",
            "is_salable":"1",
            "stock_item":{  
               "is_in_stock":"1"
            },
            "url":"http:\/\/testshops.localhost\/magento\/french-cuff-cotton-twill-oxford-563.html",
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
      {  
         "id":"64",
         "subscriber_id":"23",
         "subscriber_code":"4qzouegjuw6o7ytu6n2g6lxm4to3wcmm"
      }
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
      {  
         "id":"1",
         "name":"English",
         "item_type_id":"1",
         "languange":"en_US"
      },
      {  
         "id":"2",
         "name":"French",
         "item_type_id":"2",
         "languange":"sr_RS"
      }
   ]
}
```
