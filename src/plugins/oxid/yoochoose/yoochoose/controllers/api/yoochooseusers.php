<?php

class Yoochooseusers extends Yoochooseapi
{

    public function init()
    {
        parent::init();

        try {
            $users = $this->getUsers();
            $this->sendResponse($users);
        } catch (Exception $exc) {
            $this->sendResponse(array(), $exc->getMessage(), 400);
        }
    }

    protected function getUsers()
    {
        $users = array();
        $shopId = $this->getShopId();
        $sql = "SELECT * FROM oxuser WHERE OXSHOPID='$shopId' " . $this->getLimitSQL();
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sql);

        foreach ($result as $val) {
            $id = $val['OXID'];
            /** @var oxUser $user */
            $user = oxNew('oxuser');
            $user->load($id);
            if ($user->hasAccount()) {
                $users[] = array(
                    'id' => $id,
                    'user_name' => $val['OXUSERNAME'],
                    'first_name' => $val['OXFNAME'],
                    'last_name' => $val['OXLNAME'],
                    'groups' => $this->getGroups($user->getUserGroups()),
                    'subscribed' => $user->getNewsSubscription()->getOptInStatus() ? true : false,
                );
            }
        }

        return $users;
    }

    protected function getGroups($groups)
    {
        $result = array();
        foreach ($groups as $val) {
            $result[] = $val->oxgroups__oxtitle->value;
        }

        return $result;
    }

}
