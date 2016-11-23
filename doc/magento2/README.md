# Magento 2 API Client

## Product Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* storeId - (Integer) From store with this id (optional, if not sent default store view will be selected)

### Call example
```php
$params = [
    'limit' => 2,
    'offset' => 0,
    'storeId' => 1,
];
$data = $client->get('yoochoose/products', $params);
```

### Response example
```json
[
    {
        "id": "1",
        "name": "Joust Duffle Bag",
        "description": "<p>The sporty Joust Duffle Bag can't be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it's ideal for athletes with places to go.<p>\n<ul>\n<li>Dual top handles.</li>\n<li>Adjustable shoulder strap.</li>\n<li>Full-length zipper.</li>\n<li>L 29\" x W 13\" x H 11\".</li>\n</ul>",
        "price": "34.0000",
        "url": "http://127.0.0.1/Magento2/joust-duffle-bag.html",
        "image": "http://127.0.0.1/Magento2/pub/media/catalog/product/m/b/mb01-blue-0.jpg",
        "icon_image": "http://127.0.0.1/Magento2/pub/media/catalog/product/m/b/mb01-blue-0.jpg",
        "manufacturer": null,
        "categories": [
            "gear",
            "gear/bags"
        ],
        "activity": [
            "Yoga",
            "Gym"
        ],
        "style_bags": [
            "Exercise",
            "Tote"
        ],
        "strap_bags": [
            "Adjustable",
            "Cross Body",
            "Shoulder",
            "Single"
        ],
        "image_size": "1080x1340"
    },
    {
        "id": "2",
        "name": "Strive Shoulder Pack",
        "description": "<p>Convenience is next to nothing when your day is crammed with action. So whether you're heading to class, gym, or the unbeaten path, make sure you've got your Strive Shoulder Pack stuffed with all your essentials, and extras as well.</p>\r\n<ul>\r\n<li>Zippered main compartment.</li>\r\n<li>Front zippered pocket.</li>\r\n<li>Side mesh pocket.</li>\r\n<li>Cell phone pocket on strap.</li>\r\n<li>Adjustable shoulder strap and top carry handle.</li>\r\n</ul>",
        "price": "32.0000",
        "url": "http://127.0.0.1/Magento2/strive-shoulder-pack.html",
        "image": "http://127.0.0.1/Magento2/pub/media/catalog/product/m/b/mb04-black-0.jpg",
        "icon_image": "http://127.0.0.1/Magento2/pub/media/catalog/product/m/b/mb04-black-0_alt1.jpg",
        "manufacturer": null,
        "categories": [
            "gear",
            "gear/bags",
            "collections"
        ],
        "activity": [
            "Yoga",
            "Gym"
        ],
        "style_bags": [
            "Exercise",
            "Tote"
        ],
        "strap_bags": [
            "Adjustable",
            "Cross Body",
            "Shoulder",
            "Single"
        ],
        "image_size": "1080x1340"
    }
]
```
## Subscribers Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* storeId - (Integer) From store with this id (optional, if not sent default store view will be selected)

### Call example
```php
$params = [
    'limit' => 10,
    'offset' => 0,
];
$data = $client->get('yoochoose/subscribers', $params);
```

### Response example
```json
[
    {
        "id": "1",
        "email": "roni_cost@example.com",
        "name": "Veronica Costello",
        "group": "1",
        "gender": "0",
        "subscribed": false
    },
    {
        "id": "2",
        "email": "vojin.janevski@soprex.com",
        "name": "Vojin Janevski",
        "group": "1",
        "gender": null,
        "subscribed": true
    }
]
```
## Store Views Export

### Call example
```php
$data = $client->get('yoochoose/stores');
```

### Response example
```json
[
    {
        "id": "1",
        "name": "Default Store View",
        "item_type_id": "1",
        "language": "en-US"
    },
    {
        "id": "2",
        "name": "Engllish",
        "item_type_id": "3",
        "language": "sr-Cyrl-RS"
    }
]
```

## Categories Export

### Parameters
* limit - (Integer) Number of results
* offset - (Integer) Start from this offset
* storeId - (Integer) From store with this id (optional, if not sent default store view will be selected)

### Call example
```php
$params = [
    'limit' => 3,
    'offset' => 5,
];
$data = $client->get('yoochoose/categories', $params);
```

### Response example
```json
[
    {
        "id": "8",
        "path": "1/2/7/8",
        "url": "http://127.0.0.1/Magento2/collections/yoga-new.html",
        "name": "New Luma Yoga Collection",
        "level": "3",
        "parentId": "7"
    },
    {
        "id": "9",
        "path": "1/2/9",
        "url": "http://127.0.0.1/Magento2/training.html",
        "name": "Training",
        "level": "2",
        "parentId": "2"
    },
    {
        "id": "10",
        "path": "1/2/9/10",
        "url": "http://127.0.0.1/Magento2/training/training-video.html",
        "name": "Video Download",
        "level": "3",
        "parentId": "9"
    }
]
```
