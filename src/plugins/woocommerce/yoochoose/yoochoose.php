<?php

/**
 * Plugin Name: Yoochoose
 * Plugin URI: http://www.yoochoose.net/
 * Description: An e-commerce tool-kit that tracks activity on the shop and creates recommendation boxes based on that data
 * Version: 1.0.0
 * Author: Yoochoose
 * Author URI: http://www.yoochoose.net/
 *
 * @package Yoochoose
 * @category Core
 * @author Yoochoose
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Yoochoose class.
 */
final class Yoochoose
{

    /**
     * @var Yoochoose - Instance of Yoochoose class
     */
    private static $_instance;

    /**
     * Ensures only one instance of Yoochoose can be loaded
     * 
     * @return Yoochoose - Main instance
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new Yoochoose();
        }

        return self::$_instance;
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Admin Locales are found in:
     * 		- WP_LANG_DIR/yoochoose/yoochoose-admin-LOCALE.mo
     * 		- WP_LANG_DIR/plugins/yoochoose-admin-LOCALE.mo
     *
     * Frontend/global Locales found in:
     * 		- WP_LANG_DIR/yoochoose/yoochoose-LOCALE.mo
     * 	 	- yoochoose/i18n/languages/yoochoose-LOCALE.mo (which if not found falls back to:)
     * 	 	- WP_LANG_DIR/plugins/yoochoose-LOCALE.mo
     */
    public function loadPluginTextDomain()
    {
        $locale = apply_filters('plugin_locale', get_locale(), 'yoochoose');

        if (is_admin()) {
            load_textdomain('yoochoose', WP_LANG_DIR . '/yoochoose/yoochoose-admin-' . $locale . '.mo');
            load_textdomain('yoochoose', WP_LANG_DIR . '/plugins/yoochoose-admin-' . $locale . '.mo');
        }

        load_textdomain('yoochoose', WP_LANG_DIR . '/yoochoose/yoochoose-' . $locale . '.mo');
        load_plugin_textdomain('yoochoose', false, plugin_basename(dirname(__FILE__)) . "/i18n/languages");
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function pluginUrl()
    {
        return untrailingslashit(plugins_url('/', __FILE__));
    }

    /**
     * Adds an javascript config object for jsTracking script
     */
    public function insertJsData()
    {
        $json = array();

        $json['lang'] = get_locale();
        if (!get_option('yc_useCountryCode')) {
            $json['lang'] = substr($json['lang'], 0, strpos($json['lang'], '_'));
        }
        $json['trackid'] = get_current_user_id();
        $json['currentPage'] = $this->getCurrentPage();
        $json['boxes'] = $this->getRecommendBoxes($json['currentPage']);
        $json['productIds'] = $this->getProductIds($json['currentPage']);
        if ($json['currentPage'] === 'buyout') {
            $json['orderData'] = $this->getOrderProducts();
        }

        echo '<script type="text/javascript">var yc_config_object = ' . json_encode($json) . ';</script>';
    }

    /**
     * Adds script files to frontend of the shop
     */
    public function enqueueScripts()
    {
        wp_enqueue_script('yoochoose-jstracking', get_option('yc_trackingScript'), false);
        wp_localize_script('yoochoose-jstracking', 'yoochoose_ajax_script', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    /**
     * Ajax endpoint for fetching product information by product ids
     */
    public function ajaxProductFetch()
    {
        $result = array();
        $productIds = explode(',', filter_input(INPUT_GET, 'productIds'));
        $productFactroty = new WC_Product_Factory();
        foreach ($productIds as $id) {
            /* @var $product WC_Product */
            $product = $productFactroty->get_product($id);
            if ($product) {
                $result[] = array(
                    'title' => $product->get_title(),
                    'price' => $product->get_price_html(),
                    'image' => wp_get_attachment_url($product->get_image_id()),
                    'link' => $product->get_permalink(),
                    'rating' => $product->get_rating_html(),
                    'onsale' => $product->is_on_sale(),
                    'id' => $product->id,
                );
            }
        }

        header('Content-Type: application/json;');
        echo json_encode($result);
        die;
    }

    /**
     * Yoochoose constructor
     */
    private function __construct()
    {
        $this->loadPluginTextDomain();

        require_once '/admin/settings.php';
        YoochooseSettings::run();

        add_action('wp_head', array($this, 'insertJsData'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('wp_ajax_yoochoose_products', array($this, 'ajaxProductFetch'));
        add_action('wp_ajax_nopriv_yoochoose_products', array($this, 'ajaxProductFetch'));
    }

    /**
     * Returns name of current page, false if page isn't of interest
     * 
     * @return boolean|string
     */
    private function getCurrentPage()
    {
        if (is_shop()) {
            return 'home';
        } else if (is_product_category()) {
            return 'category';
        } else if (is_product()) {
            return 'product';
        } else if (is_cart()) {
            return 'cart';
        } else if (is_order_received_page()) {
            return 'buyout';
        }

        return false;
    }

    /**
     * Returns comma separated product ids of the product(s) that are shown on current page
     * 
     * @global type $post
     * @param string $page
     * @return string
     */
    private function getProductIds($page)
    {
        global $post;
        $result = array();

        if ($page === 'product') {
            $result[] = $post->ID;
        } else if ($page === 'cart') {
            foreach (WC()->cart->get_cart() as $product) {
                $result[] = $product['product_id'];
            }
        }

        return implode(',', $result);
    }

    /**
     * Fetches order products
     * 
     * @global type $wp
     * @return array - list of order products
     */
    private function getOrderProducts()
    {
        global $wp;

        $orderId = $wp->query_vars['order-received'];
        $order = new WC_Order($orderId);
        $result = array();
        $currency = $order->get_order_currency();
        foreach ($order->get_items() as $product) {
            $result[] = array(
                'id' => $product['product_id'],
                'price' => $product['line_total'],
                'qty' => $product['qty'],
                'currency' => $currency,
            );
        }

        return $result;
    }

    /**
     * Fetches recommendation boxes for provided page ($page)
     * 
     * @param string $page
     * @return array - list of recommendation boxes
     */
    private function getRecommendBoxes($page = false)
    {
        if (!$page) {
            return false;
        }

        $result = array();
        $boxes = get_option('yc_boxes');
        switch ($page) {
            case 'product':
                $result[] = array(
                    'id' => 'upselling',
                    'title' => $boxes['upselling']['title'],
                    'display' => $boxes['upselling']['render'],
                );
                $result[] = array(
                    'id' => 'related',
                    'title' => $boxes['related']['title'],
                    'display' => $boxes['related']['render'],
                );
                break;
            case 'home':
                $result[] = array(
                    'id' => 'bestseller',
                    'title' => $boxes['bestseller']['title'],
                    'display' => $boxes['bestseller']['render'],
                );
                $result[] = array(
                    'id' => 'personal',
                    'title' => $boxes['personal']['title'],
                    'display' => $boxes['personal']['render'],
                );
                break;
            case 'cart':
                $result[] = array(
                    'id' => 'crossselling',
                    'title' => $boxes['crossselling']['title'],
                    'display' => $boxes['crossselling']['render'],
                );
                break;
            case 'category':
                $result[] = array(
                    'id' => 'category_page',
                    'title' => $boxes['category_page']['title'],
                    'display' => $boxes['category_page']['render'],
                );
                break;
        }

        return $result;
    }

}

/**
 * Returns the main instance of Yoochoose to prevent the need to use globals.
 *
 * @return Yoochoose
 */
function YC()
{
    return Yoochoose::instance();
}

// Global for backwards compatibility.
$GLOBALS['yoochoose'] = YC();
