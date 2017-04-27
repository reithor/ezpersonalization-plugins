<?php

namespace Yoochoose\Tracking\Model\Observer;

use Magento\Authorization\Model\Role;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\User\Model\User;
use Yoochoose\Tracking\Helper\Data;
use Magento\Framework\Validator\Exception;
use Magento\Framework\Phrase;
use Magento\Integration\Model\Oauth\Token;
use Magento\Authorization\Setup\AuthorizationFactory;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Request\Http;

class ConfigUpdate implements ObserverInterface
{
    const YOOCHOOSE_LICENSE_URL = 'https://admin.yoochoose.net/api/v4/';

    /** @var ScopeConfigInterface */
    private $config;

    /** @var Data */
    private $helper;

    /** @var ObjectManagerInterface */
    private $om;

    /** @var AuthorizationFactory */
    private $authFactory;

    /** @var  Http*/
    private $request;


    public function __construct(ScopeConfigInterface $scope, Data $dataHelper, ObjectManagerInterface $om, AuthorizationFactory $authFactory,
        Http $request)
    {
        $this->config = $scope;
        $this->helper = $dataHelper;
        $this->om = $om;
        $this->authFactory = $authFactory;
        $this->request = $request;
    }

    /**
     * @param Observer| $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $ycApiRules = [
            'Magento_Customer::customer',
            'Magento_Catalog::products',
            'Magento_Backend::stores',
            'Magento_Catalog::categories'
        ];

        $postData = $observer->getEvent()->getData();
        if (empty($postData['store']) && $postData['website']) {
            $scopeId = $postData['website'];
            $scopeName = ScopeInterface::SCOPE_WEBSITES;
        } else if ($postData['store']) {
            $scopeId = $postData['store'];
            $scopeName = ScopeInterface::SCOPE_STORES;
        } else {
            return;
        }

        $customerId = $this->config->getValue('yoochoose/general/customer_id', $scopeName, $scopeId);
        $licenseKey = $this->config->getValue('yoochoose/general/license_key', $scopeName, $scopeId);

        $hasRole = false;

        if (!$customerId && !$licenseKey) {
            return;
        }

        $token = $this->config->getValue('yoochoose/auth/auth_token');
        if (!$token) {
            throw new Exception(new Phrase("Reset current Authorization token because token must not be empty!"));
        }

        $resource = $this->om->get('Magento\User\Model\ResourceModel\User');
        $userData = $resource->loadByUsername('Yoochoose-Consumer');
        if (empty($userData)) {
            $userData = $this->createUser($resource);
        }

        $user = $this->om->create('Magento\User\Model\User');
        $user->load($userData['user_id']);

        $roleCollection = $this->om->create('Magento\Authorization\Model\ResourceModel\Role\Collection');
        $roleData = $roleCollection->getItems();
        if (!empty($roleData)) {
            /** @var Role $role */
            foreach ($roleData as $role){
                $roleOptions = $role->getData();
                if($roleOptions['role_name'] === 'Yoochoose' && $roleOptions['user_id'] == $userData['user_id']){
                    $hasRole = true;
                }
            }
        }

        if(!$hasRole){
            $ycRole = $this->authFactory->createRole()->setData([
                'parent_id' => 0,
                'tree_level' => 1,
                'sort_order' => 1,
                'role_type' => RoleGroup::ROLE_TYPE,
                'user_id' => $userData['user_id'],
                'user_type' => UserContextInterface::USER_TYPE_ADMIN,
                'role_name' => 'Yoochoose',
            ])->save();

            $rulesCollection = $this->authFactory->createRulesCollection()
                ->addFieldToFilter('role_id', $ycRole->getId())
                ->addFieldToFilter('resource_id', 'all');

            if ($rulesCollection->count() == 0) {
                $this->createRules($ycRole, $ycApiRules);
            }
        }

        $tokenModel = $this->getToken($token);
        if (!$tokenModel->getId()) {
            $this->createNewToken($token, $userData['user_id']);
            $this->revokePreviousTokens($token);
        } else if ($tokenModel->getRevoked()) {
            throw new Exception(new Phrase("Reset current Authorization token because token ($token) is revoked!"));
        }

        $design = $this->config->getValue('yoochoose/general/design');
        $body = [
            'base' => [
                'type' => 'MAGENTO2',
                'pluginId' => $this->config->getValue('yoochoose/general/plugin_id'),
                'endpoint' => $this->config->getValue('yoochoose/general/endpoint'),
                'appKey' => '',
                'appSecret' => $token,
            ],
            'frontend' => [
                'design' => $design,
            ],
            'search' => [
                'design' => $design,
            ],
        ];

        $url = self::YOOCHOOSE_LICENSE_URL . $customerId . '/plugin/update?createIfNeeded=true&fallbackDesign=true';
        $this->helper->getHttpPage($url, $body, $customerId, $licenseKey);
    }


    /**
     * Returns admin token that is used for api authentication.
     * Token is either fetched if it exists and is not revoked or new token is created.
     *
     * @param $currentToken
     * @return Token
     */
    protected function getToken($currentToken)
    {
        /** @var Token $tokenModel */
        $tokenModel = $this->om->get('Magento\Integration\Model\Oauth\Token');

        return $tokenModel->loadByToken($currentToken);
    }

    /**
     * Creates new token
     * @param $token
     * @param $adminId
     */
    protected function createNewToken($token, $adminId)
    {

        /** @var Token $tokenModel */
        $tokenModel = $this->om->create('Magento\Integration\Model\Oauth\Token');
        $tokenModel->createAdminToken($adminId);
        $tokenModel->setToken($token);
        $tokenModel->save();
    }

    /**
     * Revokes all previous tokens
     * @param $activeToken
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function revokePreviousTokens($activeToken)
    {
        /** @var \Magento\Integration\Model\ResourceModel\Oauth\Token $resource */
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $resource = $this->om->get('Magento\Integration\Model\ResourceModel\Oauth\Token');
        $connection = $resource->getConnection();
        if (!$connection) {
            throw new Exception(new Phrase('Unable to fetch db connection!'));
        }

        $where = "token != '$activeToken' AND revoked = 0";

        return $connection->update($resource->getMainTable(), ['revoked' => 1], $where);
    }

    /**
     * Creates rules
     * @param Role $role
     * @param array $rules
     */
    private function createRules($role, $rules = array()){
        foreach ($rules as $rule) {
            $this->authFactory->createRules()->setData(
                [   'role_id' => $role->getId(),
                    'resource_id' => $rule,
                    'privileges' => null,
                    'permission' => 'allow',
                ]
            )->save();
        }
    }

    /**
     * Creates new admin user
     * @param \Magento\User\Model\ResourceModel\User $resource
     * @return User $user
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createUser($resource){

        $connection = $resource->getConnection();
        if (!$connection) {
            throw new Exception(new Phrase('Unable to fetch db connection!'));
        }
        $data = array(
            'username'  => 'Yoochoose-Consumer',
            'firstname' => 'Yoochoose',
            'lastname'  => 'Consumer',
            'email'     => 'test@yoochoose.net',
            'password'  => '3lP4aTY3orre',
            'is_active' => 1
        );

        $connection->insert($resource->getMainTable(), $data);

        $user = $resource->loadByUsername('Yoochoose-Consumer');

        return $user;
    }
}