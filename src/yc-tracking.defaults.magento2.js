// {id} will be changed with current product id
var YC_HANDLEBARS_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.2/handlebars.min',
    YC_BREADCRUMBS_SELECTOR = '.breadcrumbs li[class^="item category"]',
    YC_BREADCRUMBS_VALUE = 'innerHTML',
    YC_ARTICLE_RATING_FORM_SELECTOR = '#review-form',
    YC_ARTICLE_RATING_FORM_RADIO_SELECTOR = '[name^="ratings"]:checked',
    YC_BASKET_FORMS_SELECTOR = '#product_addtocart_form .tocart',
    YC_BASKET_LINKS_SELECTOR = '.tocart',
    YC_RENDER_PRICE_FORMAT = '{currencySign}{price}',
    YC_DECIMAL_SEPARATOR = '.',
    YC_SEARCH_SELECTED_SELECTOR = ' .yc-hover', //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_SELECTED_SELECTOR)
    YC_SEARCH_ALL_RESULTS_SELECTOR = ' .yc-search-result-item'; //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_ALL_RESULTS_SELECTOR)