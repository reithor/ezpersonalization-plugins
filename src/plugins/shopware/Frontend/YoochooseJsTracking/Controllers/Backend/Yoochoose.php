<?php

use Shopware\Components\YoochooseHelper;

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
     *  @var $em \Shopware\Components\Model\ModelManager
     */
    private $em;

    /**
     *
     * @param Enlight_Controller_Request_Request $request
     * @param Enlight_Controller_Response_Response $response
     */
    public function __construct(Enlight_Controller_Request_Request $request, Enlight_Controller_Response_Response $response)
    {
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
     */
    public function getDataAction()
    {
        $data = array();
        /* @var $element \Shopware\Models\Yoochoose\Yoochoose */
        $elements = $this->em->getRepository('Shopware\Models\Yoochoose\Yoochoose')->findAll();
        foreach ($elements as $element) {
            $data[$element->getName()] = $element->getValue();
        }

        /* @var $shop \Shopware\Models\Shop\Shop */
        $shop = $this->em->getRepository('Shopware\Models\Shop\Shop');
        if (!$data['design']) {
            /* @var $template \Shopware\Models\Shop\Template */
            $template = $shop->getDefault()->getTemplate();
            $templateName = $template->getName();
            // if human readable template name is set and not generic then show system template name
            $data['design'] = $templateName && $templateName !== '__theme_name__' ? $templateName : $template->getTemplate();
        }

        if (!$data['locale']) {
            $data['locale'] = $shop->getDefault()->getLocale()->getLocale();
        }

        if (!$data['endpoint']) {
            $data['endpoint'] = Shopware()->Modules()->Core()->sRewriteLink();
            // $data['endpoint'] = Shopware()->Front()->Router()->assemble();
        }

        $this->View()->assign(array(
            'success' => true,
            'data' => array($data)
        ));
    }

    public function saveFormAction()
    {
        try {
            $form = $this->Request()->getParam('form');
            $data = array();
            /* @var $element \Shopware\Models\Yoochoose\Yoochoose */
            $repository = $this->em->getRepository('Shopware\Models\Yoochoose\Yoochoose');

            $parameters = json_decode($form, true);
            foreach ($parameters as $f) {
                $data[$f['name']] = trim($f['value']);
            }

            $userId = $_SESSION['Shopware']['Auth']->id;
            /* @var $user Shopware\Models\User\User */
            $user = $this->em->getRepository('Shopware\Models\User\User')->find($userId);
            if (!$user->getApiKey()) {
                $user->setApiKey(md5(time()));
                $this->em->persist($user);
            }

            $data['apiKey'] = $user->getApiKey();
            $data['username'] = $user->getUsername();

            $this->validateLicence($data);

            foreach ($data as $name => $value) {
                $element = $repository->findOneBy(array('name' => $name));
                // if not exist in config
                if (!$element) {
                    $element = new \Shopware\Models\Yoochoose\Yoochoose();
                    $element->setName($name);
                }

                $element->setValue($value);
                $this->em->persist($element);
            }

            $this->em->flush();

            $this->View()->assign(array('success' => true, 'message' => 'Configuration saved'));
        } catch (Exception $e) {
            error_log($e->getMessage() . "\n", 3, Shopware()->DocPath() . '/logs/yoochoose.log');
            $this->View()->assign(array('success' => false, 'message' => $e->getMessage()));
            $this->Response()->setHttpResponseCode(500);
        }
    }

    /**
     * Prepares URL, authentication credentials and request body for licence authentication
     *
     * @param array $data
     * @return void
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
                'appKey' => $data['username'],
                'appSecret' => $data['apiKey'],
            ),
            'frontend' => array(
                'design' => $data['design'],
            ),
            'search' => array(
                'design' => $data['design'],
            ),
        );

        $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/update?createIfNeeded=true&fallbackDesign=true';
        $response = $this->executeCall($url, $body, $customerId, $licenseKey);
        error_log("Registration finished successfully!\n", 3, Shopware()->DocPath() . '/logs/yoochoose.log');

        return $response;
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
    private function executeCall($url, $body, $customerId, $licenceKey)
    {
        $bodyString = json_encode($body);
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$customerId:$licenceKey",
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($bodyString),
            ),
            CURLOPT_POSTFIELDS => $bodyString
        );

        $cURL = curl_init();
        curl_setopt_array($cURL, $options);
        $response = curl_exec($cURL);
        $result = json_decode($response, true);

        error_log($response . "\n", 3, Shopware()->DocPath() . '/logs/yoochoose.log');
        $eno = curl_errno($cURL);
        if ($eno && $eno != 22) {
            $msg = 'I/O error requesting [' . $url . ']. Code: ' . $eno . ". " . curl_error($cURL);
            throw new Exception($msg);
        }

        $status = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
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
