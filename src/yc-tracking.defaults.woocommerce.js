
// {id} will be changed with current product id
var YC_BREADCRUMBS_SELECTOR = '.woocommerce-breadcrumb a',
    YC_BREADCRUMBS_VALUE = 'text',
    YC_BC_OFFSETS = {
        product: {
            left: 1, right: 0
        },
        category: {
            left: 1, right: 0
        }
    },
    YC_ADD_BASKET_BUTTON_SELECTOR = '.add_to_cart_button',
    YC_ADD_BASKET_BUTTON_ITEMID = 'data-product_id',
    YC_ARTICLE_RATING_FORM_SELECTOR = '#commentform',
    YC_ARTICLE_BASKET_BUTTON = '.single_add_to_cart_button',
    YC_RENDER_PRICE_FORMAT = '{currencySign}{price}',
    YC_DECIMAL_SEPARATOR = '.',
    YC_SEARCH_SELECTED_SELECTOR = ' .yc-hover', //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_SELECTED_SELECTOR)
    YC_SEARCH_ALL_RESULTS_SELECTOR = ' .yc-search-result-item'; //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_ALL_RESULTS_SELECTOR)