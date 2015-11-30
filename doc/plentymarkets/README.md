# Upload script to Webspace
Upload javascript and css files to Webspace in js directory of your layout e.g. layout / stonepattern / js

Webspace can be found in CMS menu.

# Loading script on page

Go to Web design and in Layout / PageDesign directory add provided script to following pages:

* PageDesignContent
* PageDesignCheckout
* PageDesignCustom
* PageDesignCustom2
* PageDesignCustom3

Web design page can be found in CMS menu.

<strong>NOTE: You may need to add it to more design pages depending on your layout theme. </strong>

```
{% if $CurrentCategoryId[Level1] %}{% $_CurrentPage = 'category' %}{% endif %}
{% if $Request_ToShow == "SingleArticle" %}{% $_CurrentPage = 'product' %}{% endif %}
{% if $IsWelcomePage %}{% $_CurrentPage = 'home' %}{% endif %}
{% if $CheckoutCurrentStep %}{% $_CurrentPage = 'cart' %}{% endif %}
{% if $SCRIPT_URL contains "/-OrderShowQQMakeOrder/" %}{% $_CurrentPage = 'buyout' %}{% endif %}
{% if $CustomerID %}{% $_TrackId = $CustomerID %}{% else %}{% $_TrackId = 0 %}{% endif %}
<script>
    yc_config_object = {
        'trackid' : $_TrackId,
        'lang' : '$Lang',
        'page' : '$_CurrentPage',
        'currency' : '$Currency',
        'currencySign' : '$CurrencySign'
    };
</script>
<script src="/layout/<your_theme_name>/js/yc-tracking.js" type="text/javascript"></script>
<link href="/layout/callisto/css/yc-tracking.css" rel="stylesheet" type="text/css"></link>
```

# Item View Page

Add this at the beginning of ItemViewSingleItem template
Enter your licence key to $_licenceKey

```
{% $_licenceKey = '1234-12345-12345' %}
{% $_fullCatPath = $CategoryName[Level1] . "\t" . $CategoryName[Level2] . "\t" . $CategoryName[Level3] . "\t"
		. $CategoryName[Level4] . "\t" . $CategoryName[Level5] . "\t" . $CategoryName[Level6] %}
{% $_currentTimestamp = date('c') %}
{% $_itemPrice = str_replace(',', '.', str_replace('.', '', $BasePrice)) . $Currency %}

{% $_DataToSign = '1&' . $ID . '&' . $_licenceKey . '&categorypath=' . str_replace("\t", '/', trim($_fullCatPath)) . '&image=' . substr($BaseURL4Links, 0, -1)
		. $MiddleSizeImageURL[1] . '&lang=' . $Lang . '&overridetimestamp=' . $_currentTimestamp . '&price=' . $_itemPrice . '&title=' . $Name[1] . '&url=' . Link_Item($ID) %}

<script>
	yc_config_object.timestamp = '$_currentTimestamp';
	yc_config_object.signature = '{% md5($_DataToSign) %}';
</script>
```

# Search Suggestion Template Recommendation

Item search settings can be found in Settings > Client > Standard > Item search.
The problem is that search is hardcoded in template onkeyup event so it has to be removed in javascript.

You should use this classes and attributes so the script would work properly
* yc-data-title : attribute for every search suggestion item
* yc-hover : class when search suggestions are navigated with keyboard arrows (up, down)

```
ITEM: {
        html_template: "<h2 class='heading'>{{const.title}}</h2><ul>{{#each results}}<li class='yc-search-result-item' yc-data-title='{{{title}}}'>" +
        "<a href='{{url}}'><span class='yc-search-title'>{{{title}}}</span><span class='yc-search-price'>{{{price}}}" +
        "</span></a></li>{{else}}<span class='yc-no-results'>No item results</span>{{/each}}</ul>",
        amount: 10,
        enabled: true,
        priority: 1,
        consts: {
            "title": {'': 'Recommended Products', 'de': 'Empfohlene Produkte'}
        }
    }
```

