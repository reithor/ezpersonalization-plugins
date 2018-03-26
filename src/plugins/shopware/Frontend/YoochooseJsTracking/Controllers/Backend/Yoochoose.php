<?php

use Shopware\Components\YoochooseHelper;
use Shopware\Components\YoochooseLogger;

class Shopware_Controllers_Backend_Yoochoose extends Shopware_Controllers_Backend_ExtJs
{

    /**
     * Yoochoose licence validation URL
     */
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    /**
     * Yoochoose registration URL
     */
    const YOOCHOOSE_REGISTER_URL = 'https://admin.yoochoose.net?customer_id=%s#plugin/configuration/%s';

    /**
     * @var $em \Shopware\Components\Model\ModelManager
     */
    private $em;

    /**
     *
     * @param Enlight_Controller_Request_Request $request
     * @param Enlight_Controller_Response_Response $response
     */
    public function __construct(
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_Response $response
    ) {
        parent::__construct($request, $response);
        $this->em = Shopware()->Models();
    }

    /**
     * default index action
     */
    public function indexAction()
    {
        $this->View()->loadTemplate('backend/yoochoose/app.js');
    }

    /**
     * Retrieves information for showing Yoochoose configuration window
     * @throws Exception
     */
    public function getDataAction()
    {
        $data = array();

        /* @var $element \Shopware\Models\Yoochoose\Yoochoose */
        $elements = $this->em->getRepository('Shopware\Models\Yoochoose\Yoochoose')->findAll();
        foreach ($elements as $element) {
            if ($element->getShop()) {
                $data[$element->getShop()->getid()][$element->getName()] = $element->getValue();
            }
        }

        /* @var $shop \Shopware\Models\Shop\Shop */
        $shops = $this->em->getRepository('Shopware\Models\Shop\Shop')->findAll();

        foreach ($shops as $shop) {
            $shopId = $shop->getId();
            if (!$data[$shopId]['design']) {
                $data[$shopId]['customerId'] = '';
                $data[$shopId]['licenseKey'] = '';
                $data[$shopId]['pluginId'] = '';
                $data[$shopId]['endpoint'] = '';
                $data[$shopId]['scriptUrl'] = '';
                if ($shop->getMain()) {
                    $main = $shop->getMain();
                    /* @var $template \Shopware\Models\Shop\Template */
                    $template = $main->getTemplate();
                } else {
                    /* @var $template \Shopware\Models\Shop\Template */
                    $template = $shop->getTemplate();
                }
                $templateName = $template->getName();
                // if human readable template name is set and not generic then show system template name
                $data[$shopId]['design'] = $templateName && $templateName !== '__theme_name__' ?
                    $templateName : $template->getTemplate();
            }

            if (!$data[$shopId]['locale']) {
                $data[$shopId]['locale'] = $shop->getLocale()->getLocale();
            }

            if (!$data[$shopId]['endpoint']) {
                YoochooseHelper::initShopContext($shopId);

                $data[$shopId]['endpoint'] = YoochooseHelper::getShopUrl($shopId);
            }

            $data[$shopId]['shop'] = $shop->getName();
            $data[$shopId]['shopId'] = $shopId;

            $this->View()->assign(
                array(
                    'success' => true,
                    'data' => array($data),
                )
            );
        }
    }

    public function saveFormAction()
    {
        try {
            $form = $this->Request()->getParam('shops');
            $data = array();
            /* @var $element \Shopware\Models\Yoochoose\Yoochoose */
            $repository = $this->em->getRepository('Shopware\Models\Yoochoose\Yoochoose');

            $shops = json_decode($form, true);

            foreach ($shops as $shop) {
                foreach ($shop['fields'] as $field) {
                    $data[$shop['shopId']][$field['name']] = trim($field['value']);
                }
            }

            foreach ($data as $key => $d) {
                $this->validateLicence($d);

                /** @var Shopware\Models\Shop\Shop $shop */
                $shop = Shopware()->Models()->find('\Shopware\Models\Shop\Shop', $key);

                foreach ($d as $name => $value) {
                    $element = $repository->findOneBy(array('name' => $name, 'shop' => $key));
                    // if not exist in config
                    if (!$element) {
                        $element = new \Shopware\Models\Yoochoose\Yoochoose();
                        $element->setName($name);
                        $element->setShop($shop);
                    }

                    $element->setValue($value);
                    $this->em->persist($element);
                }
            }

            $this->em->flush();

            $this->View()->assign(array('success' => true, 'message' => 'Configuration saved'));
        } catch (Exception $e) {
            $this->View()->assign(array('success' => false, 'message' => $e->getMessage()));
            $this->Response()->setHttpResponseCode(500);
        }
    }

    /**
     * Prepares URL, authentication credentials and request body for licence authentication
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    private function validateLicence($data = array())
    {
        $customerId = $data['customerId'];
        $licenseKey = $data['licenseKey'];

        if (!$customerId && !$licenseKey) {
            throw new Exception('');
        }

        $body = array(
            'base' => array(
                'type' => 'SHOPWARE',
                'pluginId' => $data['pluginId'],
                'endpoint' => $data['endpoint'],
                'appKey' => $customerId,
                'appSecret' => md5($licenseKey),
            ),
            'frontend' => array(
                'design' => $data['design'],
            ),
            'search' => array(
                'design' => $data['design'],
            ),
        );

        $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/update?createIfNeeded=true&fallbackDesign=true';

        return $this->executeCall($url, $body, $customerId, $licenseKey, $data['logSeverity']);
    }

    /**
     * Executes cURL call to yoochoose server
     *
     * @param string $url
     * @param array $body
     * @param string $customerId
     * @param string $licenceKey
     * @param string $severity
     *
     * @return array - yoochoose server response
     * @throws Exception
     */
    private function executeCall($url, $body, $customerId, $licenceKey, $severity)
    {
        $bodyString = json_encode($body);
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ),
            CURLOPT_POSTFIELDS => $bodyString,
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        $headers = curl_getinfo($cURL, CURLINFO_HEADER_OUT);
        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        YoochooseLogger::log($severity, $url, $status, $response, $headers);

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
                $msg = $result['faultMessage'] . 'With status code: ' . $status;
                throw new Exception($msg);
        }

        curl_close($cURL);

        return $result;
    }
}
