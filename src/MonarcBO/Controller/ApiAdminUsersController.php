<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminUsersController extends AbstractController {
    public function getList() {
        return new JsonModel($this->getService()->getList());
    }

    public function get($id) {
        return new JsonModel($this->getService()->getEntity($id));
    }
}

