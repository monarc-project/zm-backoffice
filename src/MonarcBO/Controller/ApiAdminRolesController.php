<?php
namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminRolesController extends AbstractController
{
    public function getList()
    {
        /** @var UserService $service */
        $service = $this->getService();
        return new JsonModel(array('count' => $service->getFilteredCount(),
            'roles' => $service->getList()));
    }
}

