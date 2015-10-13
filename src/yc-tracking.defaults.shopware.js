
// {id} will be changed with current product id
var YC_BREADCRUMBS_SELECTOR = '#breadcrumb a[title], .breadcrumb--link',
    YC_BREADCRUMBS_VALUE = 'title',
    YC_BC_OFFSETS = {
        product: {
            left: 1, right: 0
        },
        category: {
            left: 0, right: 0
        }
    },
    YC_ADD_BASKET_BUTTON_SELECTOR = '[data-plenty="addBasketItemButton"]',
    YC_ARTICLE_RATING_FORM_SELECTOR = '#comments form, #detail--product-reviews form',
    YC_ARTICLE_BASKET_BUTTON = '#basketButton, [name="sAddToBasket"] .buybox--button',
    YC_CATEGORY_LIST_PRODUCTS = '[name="ArticleID"]',
    YC_PRODUCT_ATTRIBUTES = ['url', 'title', 'price', 'image'],
    YC_RENDER_PRICE_FORMAT = '{currencySign}{price}',
    YC_DECIMAL_SEPARATOR = '.',
    YC_SEARCH_SELECTED_SELECTOR = ' .yc-hover', //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_SELECTED_SELECTOR)
    YC_SEARCH_ALL_RESULTS_SELECTOR = ' .yc-search-result-item'; //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_ALL_RESULTS_SELECTOR)