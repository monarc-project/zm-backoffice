<?php

namespace MonarcBO\Controller;

use MonarcBO\Service\UserService;
use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminUsersController extends AbstractController {
    public function getList() {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');

        /** @var UserService $service */
        $service = $this->getService();
        return new JsonModel(array('count' => $service->getFilteredCount($page, $limit, $order, $filter),
            'users' => $service->getList($page, $limit, $order, $filter)));
    }

    public function get($id) {
        return new JsonModel($this->getService()->getEntity($id));
    }
}

