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
```javascript
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
## Store Locals Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset

### Call example
```php
    $params = array();
    $response = $client->get('ycstorelocals', $params);
```

### Response example
```json
{
    "data": [
        {
            "id": 1,
            "mainId": null,
            "categoryId": 39,
            "name": "English",
            "title": null,
            "position": 0,
            "host": null,
            "basePath": null,
            "baseUrl": null,
            "hosts": "",
            "secure": false,
            "alwaysSecure": false,
            "secureHost": null,
            "secureBasePath": null,
            "default": true,
            "active": true,
            "customerScope": false,
            "language": "English",
            "localeCode": "en_GB"
        },
        {
            "id": 2,
            "mainId": 1,
            "categoryId": 3,
            "name": "Deutsch",
            "title": "Deutsch",
            "position": 0,
            "host": null,
            "basePath": null,
            "baseUrl": null,
            "hosts": "",
            "secure": false,
            "alwaysSecure": false,
            "secureHost": null,
            "secureBasePath": null,
            "default": false,
            "active": true,
            "customerScope": false,
            "language": "German",
            "localeCode": "de_DE"
        }
    ],
    "total": 2,
    "success": true
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
        'limit' => 2,
        'language' => 'en_GB',
    );
    $response = $client->get('ycarticles', $params);
```

### Response example
```json
{  
   "data":[
      {
         "id":16,
         "name":"LEXINGTON",
         "description":"",
         "active":true,
         "categories":[
            "Berg-und-Tal\/Ausruestung\/Ski\/"
         ],
         "tags":[

         ],
         "price":587.39495798319,
         "url":"http:\/\/localhost\/shopware5\/Berg-und-Tal\/Ausruestung\/Ski\/16\/LEXINGTON",
         "image":"http:\/\/localhost\/shopware5\/media\/image\/SW10016.jpg",
         "image_size":"805x1500",
         "thumbnails":[
            {
               "image":"http:\/\/localhost\/shopware5\/media\/image\/thumbnail\/SW10016_200x200.jpg",
               "image_size":"200x200"
            },
            {
               "image":"http:\/\/localhost\/shopware5\/media\/image\/thumbnail\/SW10016_600x600.jpg",
               "image_size":"600x600"
            },
            {
               "image":"http:\/\/localhost\/shopware5\/media\/image\/thumbnail\/SW10016_1280x1280.jpg",
               "image_size":"1280x1280"
            }
         ]
      },
      {
         "id":17,
         "name":"PROVOKE",
         "description":"",
         "active":true,
         "categories":[
            "Berg-und-Tal\/Ausruestung\/Ski\/"
         ],
         "tags":[

         ],
         "price":335.29411764706,
         "url":"http:\/\/localhost\/shopware5\/Berg-und-Tal\/Ausruestung\/Ski\/17\/PROVOKE",
         "image":"http:\/\/localhost\/shopware5\/media\/image\/SW10017.jpg",
         "image_size":"800x1500",
         "thumbnails":[
            {
               "image":"http:\/\/localhost\/shopware5\/media\/image\/thumbnail\/SW10017_200x200.jpg",
               "image_size":"200x200"
            },
            {
               "image":"http:\/\/localhost\/shopware5\/media\/image\/thumbnail\/SW10017_600x600.jpg",
               "image_size":"600x600"
            },
            {
               "image":"http:\/\/localhost\/shopware5\/media\/image\/thumbnail\/SW10017_1280x1280.jpg",
               "image_size":"1280x1280"
            }
         ]
      }
   ],
   "total":166,
   "success":true
}
```
