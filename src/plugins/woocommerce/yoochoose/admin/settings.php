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

    /**
     *  Static method that initializes yoochoose admin settings page
     */
    public static function run()
    {
        $settings = new YoochooseSettings();
        add_action('admin_menu', array($settings, 'adminMenu'));
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
     * Displays plugin settings page
     */
    public function adminSettingsnPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
            $this->saveOption('yc_trackingScript', filter_input(INPUT_POST, 'tackingScript', FILTER_VALIDATE_URL));
            $this->saveOption('yc_customerId', filter_input(INPUT_POST, 'customerId'));
            $this->saveOption('yc_licenceKey', filter_input(INPUT_POST, 'licenceKey'));
            $this->saveOption('yc_useCountryCode', filter_input(INPUT_POST, 'useCountryCode'));
            $this->saveOption('yc_boxes', filter_input(INPUT_POST, 'boxes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));

            $this->validateLicence();
        }

        $trackingScript = get_option('yc_trackingScript');
        $customerId = get_option('yc_customerId');
        $licenceKey = get_option('yc_licenceKey');
        $useCountryCode = get_option('yc_useCountryCode');
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

        require_once 'views/html-admin-settings.php';
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

        if (!$customerId && !$licenseKey) {
            return;
        }

        try {
            $body = array(
                'base' => array(
                    'type' => 'WOOCOMMERCE',
                    'pluginId' => null,
                    'endpoint' => site_url(),
                ),
                'frontend' => array(
                    'design' => get_current_theme(),
            ));

            $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/create?recheckType=true&fallbackDesign=true';

            return $this->executeCall($url, $body, $customerId, $licenseKey);
        } catch (Exception $ex) {
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
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_FAILONERROR => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ),
            CURLOPT_POSTFIELDS => json_encode($bodyString)
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $result = curl_exec($cURL);

        $eno = curl_errno($cURL);
        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            throw new Exception($msg);
        }

        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            $msg = 'Error requesting [' . $url . ']. Status: ' . $status . '.';
            throw new Exception($msg);
        }

        curl_close($cURL);

        return json_decode($result, true);
    }

}
