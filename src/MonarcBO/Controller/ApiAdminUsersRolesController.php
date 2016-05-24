<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminUsersRolesController extends AbstractController
{
    public function get($id)
    {
        $userRoles = $this->getService()->getByUserId($id);

        return new JsonModel(array(
            'count' => count($userRoles),
            'roles' => $userRoles
        ));
    }
}

