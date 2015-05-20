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
   "231":{  
      "entity_id":"231",
      "image":"http:\/\/localhost\/magento\/media\/catalog\/product\/m\/s\/msj000t_1.jpg",
      "description":"Button front. Long sleeves. Tapered collar, chest pocket, french cuffs.",
      "price":"190.0000",
      "title":"French Cuff Cotton Twill Oxford",
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
      "image":"http:\/\/localhost\/magento\/media\/catalog\/product\/m\/s\/msj000t_1.jpg",
      "description":"Button front. Long sleeves. Tapered collar, chest pocket, french cuffs.",
      "price":"190.0000",
      "title":"French Cuff Cotton Twill Oxford",
      "url":"http:\/\/localhost\/magento\/french-cuff-cotton-twill-oxford-563.html",
      "image_size":"600x900",
      "categories":[  
         "Men\/Shirts"
      ],
      "tags":[  

      ]
   }
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
```
## Store Views Export

### Call example
```php
    $response = $client->getShopData('yoochoose/storeviews', 'GET', array());
```

### Response example
```json
[  
   {  
      "id":"1",
      "name":"English",
      "item_type_id":"2",
      "languange":"it_IT"
   },
   {  
      "id":"2",
      "name":"French",
      "item_type_id":"2",
      "languange":"sr_RS"
   }
]
```
