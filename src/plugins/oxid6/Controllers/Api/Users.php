<?php

namespace Yoochoose\Oxid\Controllers\Api;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Class Users
 * @package Yoochoose\Oxid\Controllers\Api
 */
class Users extends BaseApi
{
    /**
     * Initiates all components stored, executes \OxidEsales\Eshop\Core\Controller\BaseController::addGlobalParams.
     */
    public function init()
    {
        parent::init();

        try {
            $users = $this->getUsers();
            $this->sendResponse($users);
        } catch (\Exception $exc) {
            $this->sendResponse([], $exc->getMessage(), 400);
        }
    }

    /**
     * @return array
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    protected function getUsers()
    {
        $users = [];
        $shopId = $this->getShopId();
        $sql = "SELECT * FROM oxuser WHERE OXSHOPID='$shopId' " . $this->getLimitSQL();
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            $user = new User();
            $user->load($id);
            if ($user->hasAccount()) {
                $users[] = [
                    'id' => $id,
                    'user_name' => $val['OXUSERNAME'],
                    'first_name' => $val['OXFNAME'],
                    'last_name' => $val['OXLNAME'],
                    'groups' => $this->getGroups($user->getUserGroups()),
                    'subscribed' => $user->getNewsSubscription()->getOptInStatus() ? true : false,
                ];
            }
        }

        return $users;
    }

    protected function getGroups($groups)
    {
        $result = [];
        foreach ($groups as $val) {
            $result[] = $val->oxgroups__oxtitle->value;
        }

        return $result;
    }
}
