<?php

namespace Yoochoose\Oxid\Controllers;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use Yoochoose\Oxid\Controllers\Traits\RequestParameters;
use Yoochoose\Oxid\Helpers\Helper;
use Yoochoose\Oxid\Models\Yoochoose;

/**
 * Class Trigger
 * @package Yoochoose\Oxid\Controllers
 */
class Trigger extends FrontendController
{
    use RequestParameters;

    /**
     * @var Yoochoose
     */
    private $ycModel;

    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        $conf = $this->getConfig();
        $helper = new Helper();
        $this->ycModel = new Yoochoose();

        $limit = $this->getRequestParameter('limit');
        $callbackUrl = $this->getRequestParameter('webHook');
        $postPassword = $this->getRequestParameter('password');
        $transaction = $this->getRequestParameter('transaction');
        $shopData = $this->getRequestParameter('shopData');
        $customerId = $this->getRequestParameter('mandator');

        $password = $conf->getShopConfVar('ycPassword');
        $shopId = key($shopData[0]);
        $licenceKey = $conf->getShopConfVar('ycLicenseKey', $shopData[0][$shopId], 'module:yoochoose');
        $response = [];

        if ($password === $postPassword) {
            set_time_limit(0);
            $conf->saveShopConfVar('bool', 'ycEnableFlag', 1, $conf->getShopId(), 'module:yoochoose');
            try {
                $this->ycModel->log('Export has started for all resources.', '', '', '');
                $postData = $helper->export($shopData, $transaction, $limit, $customerId);

                $this->ycModel->log('Export has finished for all resources.', '', '', '');
                $this->setCallback($callbackUrl, $postData, $customerId, $licenceKey);
                $response['success'] = true;
            } catch (\Exception $exc) {
                $response['success'] = false;
                $response['message'] = $exc->getMessage();
                $this->ycModel->log('Export has failed ' . $exc->getMessage(), '', '', '');
            } finally {
                $conf->saveShopConfVar('bool', 'ycEnableFlag', 0, $conf->getShopId(), 'module:yoochoose');
            }
        } else {
            $response['message'] = 'Passwords do not match!';
        }

        return json_encode($response);
    }

    /**
     * Creates request and returns response
     *
     * @param string $url
     * @param array  $post
     * @param string $customerId
     * @param string $licenceKey
     *
     * @return array
     */
    private function setCallback($url, $post, $customerId, $licenceKey)
    {
        $postString = json_encode($post);

        $cURL = curl_init();
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cURL, CURLOPT_USERPWD, "$customerId:$licenceKey");
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
        $this->ycModel->log('Callback header : ' . $header . ' Callback body : ' . $body, '', '', '');
        curl_close($cURL);

        return json_decode($response, true);
    }
}
