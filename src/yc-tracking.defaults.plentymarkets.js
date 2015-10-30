
// {id} will be changed with current product id
var YC_BREADCRUMBS_SELECTOR = '.breadcrumbs',
    YC_ADD_BASKET_BUTTON_SELECTOR = '[data-plenty="addBasketItemButton"]',
    YC_ARTICLE_ID_SELECTOR = 'ArticleID',
    YC_ARTICLE_PRICE_SELECTOR = '#price_dynamic_0_{id}',
    YC_ARTICLE_TITLE_SELECTOR = '.itemTitle',
    YC_ARTICLE_IMAGE_SELECTOR = '#plenty_xl_image_{id}_0',
    YC_CATEGORY_LIST_PRODUCTS = '[name="ArticleID"]',
    YC_RENDER_PRICE_FORMAT = '{price}&thinsp;{currencySign}',
    YC_DECIMAL_SEPARATOR = ',',
    YC_SEARCH_SELECTED_SELECTOR = ' .yc-hover', //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_SELECTED_SELECTOR)
    YC_SEARCH_ALL_RESULTS_SELECTOR = ' .yc-search-result-item'; //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_ALL_RESULTS_SELECTOR)