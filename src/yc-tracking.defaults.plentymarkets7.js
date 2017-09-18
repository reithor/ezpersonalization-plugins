
// {id} will be changed with current product id
var YC_BREADCRUMBS_SELECTOR = '#twig-rendered-breadcrumbs .breadcrumb a, #twig-rendered-breadcrumbs .breadcrumb li.breadcrumb-item.active span',
    YC_CATEGORY_BASKET_SELECTOR = '.product-list article.cmp button',
    YC_BASKET_FORMS_SELECTOR = 'div.single button.btn-block.btn-primary',
    YC_RENDER_PRICE_FORMAT = '{price}&thinsp;{currencySign}',
    YC_DECIMAL_SEPARATOR = ',',
    YC_SEARCH_SELECTED_SELECTOR = ' .yc-hover', //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_SELECTED_SELECTOR)
    YC_SEARCH_ALL_RESULTS_SELECTOR = ' .yc-search-result-item'; //it has prefix of the result tag id e.g ('#ycSearchResult0' + YC_SEARCH_ALL_RESULTS_SELECTOR)