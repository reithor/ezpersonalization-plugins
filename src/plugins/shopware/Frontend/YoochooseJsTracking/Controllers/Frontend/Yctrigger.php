<?php

use Shopware\Components\YoochooseHelper;
use Shopware\Components\YoochooseLogger;


class Shopware_Controllers_Frontend_Yctrigger extends Enlight_Controller_Action
{
    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $em;

    /**
     * @var YoochooseHelper
     */
    private $helper;

    public function __construct(
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_Response $response
    )
    {
        parent::__construct($request, $response);
        $this->em = Shopware()->Models();
        $this->helper = new YoochooseHelper();
    }

    public function indexAction()
    {
        $helper = new YoochooseHelper();
        $response = array('success' => false);
        $repository = $this->em->getRepository('Shopware\Models\Yoochoose\Yoochoose');

        $limit = $this->Request()->getParam('limit');
        $callbackUrl = $this->Request()->getParam('webHook');
        $postPassword = $this->Request()->getParam('password');
        $transaction = $this->Request()->getParam('transaction');
        $storeData = json_decode($this->Request()->getParam('storeData'), true);
        $customerId = $this->Request()->getParam('mandator');
        $shopId = $this->helper->getDefaultShopId();

        $password = $this->helper->getConfigParam('password', $shopId);
        $licenceKey = $repository->findOneBy(array('name' => 'licenseKey', 'shop' => key($storeData)));

        if ($password === $postPassword) {
            $this->helper->saveConfigParam('enable_flag', 1, $shopId);

            ignore_user_abort(true);
            set_time_limit(0);
            try {
                YoochooseLogger::log(1, 'Export has started for all resources.', '', '', '');
                $postData = $helper->export($storeData, $transaction, $limit, $customerId);
                YoochooseLogger::log(1, 'Export has finished for all resources.', '', '', '');
                $this->setCallback($callbackUrl, $postData, $customerId, $licenceKey->getValue());
                $response['success'] = true;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            } finally {
                $this->helper->saveConfigParam('enable_flag', 0, $shopId);
            }
        } else {
            $response['message'] = 'Passwords do not match!';
        }

        header('Content-Type: application/json');
        exit(json_encode($response));
    }

    /**
     * Creates request and returns response
     *
     * @param string $url
     * @param string $post
     * @param string $customerId
     * @param string $licenceKey
     * @return array
     * @internal param mixed $params
     */
    private function setCallback($url, $post, $customerId, $licenceKey)
    {
        $postString = json_encode($post);

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cURL, CURLOPT_USERPWD, $customerId . ":" . $licenceKey);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, ['Content-Type: application/json',]);
        curl_setopt($cURL, CURLOPT_POST, 1);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_HEADER, true);

        $response = curl_exec($cURL);

        $header_size = curl_getinfo($cURL, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $header = str_replace("\r\n", '', $header);
        $body = substr($response, $header_size);
        YoochooseLogger::log(1, 'Callback header : ' . $header . ' Callback body : ' . $body, '', '', '');
        curl_close($cURL);

        return json_decode($response, true);
    }

}
