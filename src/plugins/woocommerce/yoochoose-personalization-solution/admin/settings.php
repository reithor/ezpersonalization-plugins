<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('curl_version')) {
    exit('CURL is disabled and is necessary for this plugin, please enable it.'); // Exit if accessed directly
}

/**
 * YoochooseSettings class.
 */
class YoochooseSettings
{

    /**
     * Yoochoose licence validation URL
     */
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';
    const SCRIPT_URL_REGEX = "/^(https:\/\/|http:\/\/|\/\/)?([a-zA-Z][\w\-]*)((\.[a-zA-Z][\w\-]*)*)(:\d+)?((\/[a-zA-Z][\w\-]*){0,2})(\/)?$/";

    /**
     * Name of the log file
     */
    const YOOCHOOSE_LOG_FILE = 'yoochoose.log';

    const YOOCHOOSE_INFO_FORMAT = "%s [%s]:\nURL: %s\nCODE: %s\n\n";

    const YOOCHOOSE_DEBUG_FORMAT = "%s [%s]:\nURL: %s\nCODE: %s\nRESPONSE BODY: %s\nREQUEST HEADERS: %s\n";

    private $tossMessages = array();

    /**
     *  Static method that initializes yoochoose admin settings page
     */
    public static function run()
    {
        $settings = new YoochooseSettings();
        add_action('admin_menu', array($settings, 'adminMenu'));
        add_action('admin_head', array($settings, 'myCustomCss'));
    }

    /**
     * @param string $url
     * @param int $code
     * @param string $body
     * @param string $headers
     */
    public static function log($url, $code, $body, $headers)
    {
        $severity = get_option('yc_logSeverity');
        $ts = date(DATE_ISO8601);
        $type = ($severity == 2 ? 'DEBUG' : 'INFO');
        $format = ($severity == 2 ? self::YOOCHOOSE_DEBUG_FORMAT : self::YOOCHOOSE_INFO_FORMAT);
        $message = sprintf($format, $ts, $type, $url, $code, $body, $headers);

        $logger = new WC_Logger();
        $logger->add(self::YOOCHOOSE_LOG_FILE, $message);
    }

    /**
     * Adds Yoochoose menu item
     */
    public function adminMenu()
    {
        add_menu_page('Yoochoose Settings', 'Yoochoose', 'manage_options', 'yoochoose-config',
            array($this, 'adminSettingsnPage'), plugins_url('/assets/images/logo.png', __FILE__), 25);
    }

    /**
     * Adds css settings
     */
    public function myCustomCss()
    {
        echo '<style>
            #adminmenu .wp-menu-image img {
              opacity: 100;
            } 
          </style>';
    }

    /**
     * Displays plugin settings page
     */
    public function adminSettingsnPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
            $this->saveOption('yc_cdnSource', filter_input(INPUT_POST, 'cdnSource', FILTER_VALIDATE_INT));
            $this->saveOption('yc_scriptOverwrite', filter_input(INPUT_POST, 'scriptOverwrite',
                FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => self::SCRIPT_URL_REGEX))));
            $this->saveOption('yc_customerId', filter_input(INPUT_POST, 'customerId', FILTER_VALIDATE_INT));
            $this->saveOption('yc_licenceKey', filter_input(INPUT_POST, 'licenceKey'));
            $this->saveOption('yc_useCountryCode', filter_input(INPUT_POST, 'useCountryCode'));
            $this->saveOption('yc_design', filter_input(INPUT_POST, 'design'));
            $this->saveOption('yc_endpoint', filter_input(INPUT_POST, 'endpoint'));
            $this->saveOption('yc_overrideDesign', filter_input(INPUT_POST, 'overrideDesign'));
            $this->saveOption('yc_logSeverity', filter_input(INPUT_POST, 'logSeverity'));
            $this->saveOption('yc_boxes', filter_input(INPUT_POST, 'boxes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));

            $this->validateLicence();
        }

        $cdnSource = get_option('yc_cdnSource');
        $scriptOverwrite = get_option('yc_scriptOverwrite');
        $customerId = get_option('yc_customerId');
        $licenceKey = get_option('yc_licenceKey');
        $useCountryCode = get_option('yc_useCountryCode');
        $overrideDesign = get_option('yc_overrideDesign');
        $logSeverity = get_option('yc_logSeverity');
        $endpoint = $overrideDesign ? get_option('yc_endpoint') : site_url();
        $wpTheme = wp_get_theme();
        $design = $overrideDesign ? get_option('yc_design') : $wpTheme->get('Name');
        $boxes = get_option('yc_boxes');

        $data = array();
        $lang = get_locale();
        $userId = get_current_user_id();
        /* @var $product WP_User */
        $admin = get_user_by('id', $userId);
        $data['account.firstName'] = $data['billing.firstName'] = $admin->user_firstname;
        $data['account.lastName'] = $data['billing.lastName'] = $admin->user_firstname;
        $data['account.email'] = $data['billing.email'] = $admin->user_email;
        $data['booking.website'] = site_url();
        $data['booking.timeZone'] = get_option('timezone_string');
        $data['booking.lang'] = substr($lang, 0, strpos($lang, '_'));
        $data['billing.countryCode'] = substr($lang, strpos($lang, '_') + 1);

        $registrationInfo = json_encode($data);
        $registrationLink = 'https://admin.yoochoose.net/login.html?product=woocommerce_Direct&lang=' . substr($lang, 0, strpos($lang, '_'));
        $ycAdminLink = 'https://admin.yoochoose.net?customer_id=' . $customerId . '#plugin/configuration';

        require_once 'views/html-admin-settings.php';
        $this->tossMessages = array();
    }

    /**
     * Helper method for saving plugin configuration
     *
     * @param string $id
     * @param mixed $value
     */
    private function saveOption($id, $value)
    {
        (get_option($id, null) !== null) ? update_option($id, $value) : add_option($id, $value);
    }

    /**
     * Prepares URL, authentication credentials and request body for licence authentication
     *
     * @return array - yoochoose server response
     */
    private function validateLicence()
    {
        $customerId = get_option('yc_customerId');
        $licenseKey = get_option('yc_licenceKey');
        $overrideDesign = get_option('yc_overrideDesign');
        $wpTheme = wp_get_theme();

        if (!$customerId && !$licenseKey) {
            return;
        }

        try {
            $body = array(
                'base'     => array(
                    'type'     => 'WOOCOMMERCE',
                    'pluginId' => null,
                    'endpoint' => $overrideDesign ? get_option('yc_endpoint') : site_url(),
                ),
                'frontend' => array(
                    'design' => $overrideDesign ? get_option('yc_design') : $wpTheme->get('Name'),
                ));

            $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/create?recheckType=true&fallbackDesign=true';
            $this->executeCall($url, $body, $customerId, $licenseKey);
            $this->tossMessages[] = array(
                'message' => 'Plugin registered successfully',
                'type'    => 'success',
            );
        } catch (Exception $ex) {
            $this->tossMessages[] = array(
                'message' => $ex->getMessage(),
                'type'    => 'error',
            );
        }
    }

    /**
     * Executes cURL call to yoochoose server
     *
     * @param string $url
     * @param array $body
     * @param string $customerId
     * @param string $licenceKey
     * @return array - yoochoose server response
     * @throws Exception
     */
    public function executeCall($url, $body, $customerId, $licenceKey)
    {
        $bodyString = json_encode($body);
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => 0,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ),
            CURLOPT_POSTFIELDS     => $bodyString,
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        $header = curl_getinfo($cURL, CURLINFO_HEADER_OUT);
        self::log($url, $status, $response, $header);

        $eno = curl_errno($cURL);
        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            throw new Exception($msg);
        }

        switch ($status) {
            case 200:
                break;
            case 409:
                if ($result['faultCode'] === 'pluginAlreadyExistsFault') {
                    break;
                }

            default:
                $msg = $result['faultMessage'] . ' With status code: ' . $status;
                throw new Exception($msg);
        }

        curl_close($cURL);

        return $result;
    }

}
