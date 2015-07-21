# Shopware API Client

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
    $response = $client->get('ycsubscribers', $params);
```

### Response example
```json
{  
   "data":[  
      {  
         "id":4,
         "customerId":3,
         "newsletterGroupId":1
      },
      {  
         "id":5,
         "customerId":5,
         "newsletterGroupId":0
      }
   ],
   "total":3,
   "success":true
}
```
## Shops Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* sort - (String) Sorting order
* filter - (String) Filter parameters

### Call example
```php
    $params = array();
    $response = $client->get('shops', $params);
```

### Response example
```json
{  
   "data":[  
      {  
         "id":1,
         "mainId":null,
         "categoryId":39,
         "name":"English",
         "title":null,
         "position":0,
         "host":null,
         "basePath":null,
         "baseUrl":null,
         "hosts":"",
         "secure":false,
         "alwaysSecure":false,
         "secureHost":null,
         "secureBasePath":null,
         "default":true,
         "active":true,
         "customerScope":false
      },
      {  
         "id":2,
         "mainId":1,
         "categoryId":3,
         "name":"Deutsch",
         "title":"Deutsch",
         "position":0,
         "host":null,
         "basePath":null,
         "baseUrl":null,
         "hosts":"",
         "secure":false,
         "alwaysSecure":false,
         "secureHost":null,
         "secureBasePath":null,
         "default":false,
         "active":true,
         "customerScope":false
      }
   ],
   "total":2,
   "success":true
}
```
## Categories Export

### Parameters
* limit - (Integer) Number of results
* start - (Integer) Start from this offset

### Call example
```php
    $params = array(
        'start' => 15,
        'limit' => 3,
    );
    $response = $client->get('yccategories', $params);
```

### Response example
```json
{  
   "data":[  
      {  
         "id":20,
         "name":"Darstellung",
         "parentId":10,
         "pathIds":"|10|3|",
         "path":"Beispiele\/Darstellung\/",
         "link":"http:\/\/localhost\/shopware\/\/Beispiele\/Darstellung\/"
      },
      {  
         "id":21,
         "name":"Produktvergleiche & Filter",
         "parentId":10,
         "pathIds":"|10|3|",
         "path":"Beispiele\/Produktvergleiche-und-Filter\/",
         "link":"http:\/\/localhost\/shopware\/\/Beispiele\/Produktvergleiche-und-Filter\/"
      },
      {  
         "id":22,
         "name":"Konfiguratorartikel",
         "parentId":10,
         "pathIds":"|10|3|",
         "path":"Beispiele\/Konfiguratorartikel\/",
         "link":"http:\/\/localhost\/shopware\/\/Beispiele\/Konfiguratorartikel\/"
      }
   ],
   "total":63,
   "success":true
}
```
## Articles Export

### Parameters
* limit - (Integer) Number of results
* start - (Integer) Start from this offset
* language - (Integer | String) Id or locale of the locale to which article is translated to

### Call example
```php
    $params = array(
        'start' => 15,
        'limit' => 3,
        'language' => 'en_GB',
    );
    $response = $client->get('ycarticles', $params);
```

### Response example
```json
{  
   "data":[  
      {  
         "id":12,
         "name":"Kobra Vodka 37,5%",
         "description":"Refero Eluo fornax vos illa ora Nutus casus moderor hoc Fides, revolvo vox corium ne eo Decoro. Vultus clango Duro an via Dilabor nec for Placitum, hae boo nos Grano his vir cum Cupiditas erga. Illo apex copia Vexamen pute.",
         "active":true,
         "categories":[  
            "Genusswelten\/Edelbraende\/",
            "Beispiele\/Produktvergleiche-und-Filter\/",
            "Worlds-of-indulgence\/Brandies\/",
            "Examples\/Product-comparison\/"
         ],
         "tags":[  

         ],
         "price":8.3949579831933,
         "url":"http:\/\/localhost\/shopware\/Examples\/Product-comparison\/12\/Kobra-Vodka-37-5",
         "image":"http:\/\/localhost\/shopware\/media\/image\/KobraVodka.jpg",
         "image_size":"372x768"
      },
      {  
         "id":13,
         "name":"Pai Mu Tan tea white",
         "description":"Peragro fugo virus Res qui hic ira quatenus\/quatinus Perago tui Pronuntio per pio vel superstes sperno. Spero n.",
         "active":true,
         "categories":[  
            "Genusswelten\/Tees-und-Zubehoer\/Tees\/",
            "Worlds-of-indulgence\/Teas-and-Accessories\/Teas\/"
         ],
         "tags":[  

         ],
         "price":2.1008403361345,
         "url":"http:\/\/localhost\/shopware\/Worlds-of-indulgence\/Teas-and-Accessories\/Teas\/13\/Pai-Mu-Tan-white-tea",
         "image":"http:\/\/localhost\/shopware\/media\/image\/Tee-weiss-Pai-Mu-Tan.jpg",
         "image_size":"800x553"
      },
      {  
         "id":13,
         "name":"Pai Mu Tan tea white",
         "description":"Peragro fugo virus Res qui hic ira quatenus\/quatinus Perago tui Pronuntio per pio vel superstes sperno. Spero n.",
         "active":true,
         "categories":[  
            "Genusswelten\/Tees-und-Zubehoer\/Tees\/",
            "Worlds-of-indulgence\/Teas-and-Accessories\/Teas\/"
         ],
         "tags":[  

         ],
         "price":2.1008403361345,
         "url":"http:\/\/localhost\/shopware\/Worlds-of-indulgence\/Teas-and-Accessories\/Teas\/13\/Pai-Mu-Tan-white-tea",
         "image":"http:\/\/localhost\/shopware\/media\/image\/Tee-weiss-Pai-Mu-Tan.jpg",
         "image_size":"800x553"
      }
   ],
   "total":225,
   "success":true
}
```
