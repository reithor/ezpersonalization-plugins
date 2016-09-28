# OXID API Client

## Product Export

### Parameters
* appSecret - (String) App secret used for authentication (md5 hashed value of Licence Key)
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* shop - (String) Shop Id. (optional, if not set, default shop will be used)
* lang - (Integer) Language Id. (optional, if not set, default language will be used)

## Shops Export

### Call example
```php
    $data = $apiClient->execute('Yoochoose/Shops/');
```

### Response example
```json
{
    "success": true,
    "data": [
        {
            "id": "oxbaseshop",
             "name": "OXID eShop 4",
             "languages": {
                 "en": {
                     "id": 0,
                     "name": "English"
                 },
                 "de": {
                     "id": 1,
                     "name": "Deutsch"
                 }
             }
         }
     ]
 }
```

## Product Export

### Call example
```php
    $params = array(
        'limit' => 2,
        'offset' => 10,
        'lang' => 1,
        'shop' => 'oxbaseshop',
    );
    $data = $apiClient->execute('Yoochoose/Articles/', $params);
```

### Response example
```json
{
    {
        "success": true,
        "data": [
            {
                "id": "6b6ac464656c16c90d671721c93dc6ba",
                "name": "Stewart+Brown Lace Scoop Neck Tee",
                "description": "<div style=\"font: 12px 'Lucida Grande',Lucida,Verdana,sans-serif\">Raffiniertes Langarm-Shirt aus Pima-Baumwolle. Am Ausschnitt ist der weiche Stoff leicht gerafft und mit zwei verspielten Knöpfen versehen. Das Shirt kommt aus der exklusiven Stewart+Brown Organic Cotton Linie und verspricht die Verwendung feinster Pima-Baumwolle. Pima Baumwolle besteht aus besonders langstapeligen Fasern, die die hohe Qualität ausmachen.<br />\r\n<br /><strong>100% ökologisch angebaute Baumwolle, 100% Fair Trade.</strong><br />\r\n<br /><font face=\"Times New Roman\" size=\"3\"><strong><span></span></strong></font>\r\n</div>",
                "price": "59.9",
                "url": "http://localhost/oxid/de/Bekleidung/Fashion/Fuer-Sie/Shirts-Co/Stewart-Brown-Lace-Scoop-Neck-Tee.html",
                "image": "http://localhost/oxid/out/pictures/generated/product/1/380_340_75/front_z1(16)_z1.jpg",
                "image_size": "340x340",
                "icon_image": "http://localhost/oxid/out/pictures/generated/product/1/87_87_75/front_z1(16)_z1.jpg",
                "manufacturer": null,
                "categories": [
                    "Bekleidung/Fashion/Fuer-Sie/Shirts-Co/",
                    "Bekleidung/",
                    "Kiteboarding/Trapeze/",
                    "Bekleidung/Fashion/Fuer-Sie/Jeans/",
                    "Bekleidung/Fashion/Fuer-Ihn/Jeans/"
                ],
                "tags": "shirt,pima,langarm"
            },
            {
                "id": "6b6b09a02f3c78adb5771bce215ec265",
                "name": "Kuyichi Longsleeve Lani",
                "description": "<div style=\"font: 12px 'Lucida Grande',Lucida,Verdana,sans-serif\">\r\n<p><span>Kuyichi Stretch-Jersey Longsleeve mit modischem Kuyichi Front-Druck.</span></p>\r\n<p><span><br />\r\n<strong>100% ökologisch angebaute Baumwolle, 100% Fair Trade.</strong></span></p>\r\n</div>",
                "price": "29.9",
                "url": "http://localhost/oxid/de/Bekleidung/Fashion/Fuer-Sie/Shirts-Co/Kuyichi-Longsleeve-Lani.html",
                "image": "http://localhost/oxid/out/pictures/generated/product/1/380_340_75/front_z1(14)_z1.jpg",
                "image_size": "340x340",
                "icon_image": "http://localhost/oxid/out/pictures/generated/product/1/87_87_75/front_z1(14)_z1.jpg",
                "manufacturer": "Kuyichi",
                "categories": [
                    "Bekleidung/Fashion/Fuer-Sie/Shirts-Co/",
                    "Kiteboarding/Zubehoer/",
                    "Bekleidung/Sportswear/NeoprAnzuege/",
                    "Bekleidung/Sportswear/"
                ],
                "tags": "kuyichi,shirt,pima,langarm,longsleeve"
            }
        ]
    }
}
```

## Categories Export

### Call example
```php
    $params = array(
        'limit' => 2,
        'offset' => 10,
        'lang' => 0,
    );
    $data = $apiClient->execute('Yoochoose/Categories/', $params);
```

### Response example
```json
{
    "success": true,
    "data": [
        {
            "id": "d86d90e4b441aa3f0004dcda5ba5bb38",
            "url": "http://localhost/oxid/Wakeboarding-oxid/Sets/",
            "name": "Sets",
            "level": 1,
            "parentId": "943173edecf6d6870a0f357b8ac84d32",
            "path": "Wakeboarding-oxid/Sets/"
        },
        {
            "id": "943173edecf6d6870a0f357b8ac84d32",
            "url": "http://localhost/oxid/Wakeboarding-oxid/",
            "name": "Wakeboarding",
            "level": 0,
            "parentId": null,
            "path": "Wakeboarding-oxid/"
        }
    ]
}
```

## Customers Export

### Call example
```php
    $data = $apiClient->execute('Yoochoose/Users/');
```

### Response example
```json
{
    "success": true,
    "data": [
        {
            "id": "oxdefaultadmin",
            "user_name": "admin@soprex.com",
            "first_name": "John",
            "last_name": "Doe",
            "groups": [
                "Store Administrator",
                "Foreign Customer",
                "Customer",
                "Huge Turnover"
            ],
            "subscribed": true
        },
        {
            "id": "e7af1c3b786fd02906ccd75698f4e6b9",
            "user_name": "info@oxid-esales.com",
            "first_name": "Marc",
            "last_name": "Muster",
            "groups": [
                "Domestic Customer",
                "Customer",
                "Huge Turnover",
                "Medium Turnover"
            ],
            "subscribed": false
        }
    ]
}
```