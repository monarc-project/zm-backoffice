<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminUsersRolesController extends AbstractController
{
    public function getList() {

        $request = $this->getRequest();
        $token = $request->getHeader('token');

        $currentUserRoles = $this->getService()->getByUserToken($token);

        return new JsonModel(array(
            'count' => count($currentUserRoles),
            'roles' => $currentUserRoles
        ));
    }

    public function get($id)
    {
        $userRoles = $this->getService()->getByUserId($id);

        return new JsonModel(array(
            'count' => count($userRoles),
            'roles' => $userRoles
        ));
    }
}

