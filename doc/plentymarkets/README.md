# Upload script to Webspace
Upload javascript file to Webspace in js directory of your layout e.g. layout / stonepattern / js

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
```