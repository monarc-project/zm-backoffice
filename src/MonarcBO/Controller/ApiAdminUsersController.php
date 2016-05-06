<?php

namespace MonarcBO\Controller;

use MonarcCore\Service\UserService;
use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminUsersController extends AbstractController
{
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');

        /** @var UserService $service */
        $service = $this->getService();
        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            'users' => $service->getList($page, $limit, $order, $filter)
        ));
    }

    public function get($id)
    {
        return new JsonModel($this->getService()->getEntity($id));
    }

    public function create($data)
    {
        /** @var UserService $service */
        $service = $this->getService();

        // Security: Don't allow changing role, password, status and history fields. To clean later.
        //if (isset($data['role'])) unset($data['role']);
        if (isset($data['salt'])) unset($data['salt']);
        if (isset($data['updatedAt'])) unset($data['updatedAt']);
        if (isset($data['updater'])) unset($data['updater']);
        if (isset($data['createdAt'])) unset($data['createdAt']);
        if (isset($data['creator'])) unset($data['creator']);
        if (isset($data['dateStart'])) unset($data['dateStart']);
        if (isset($data['dateEnd'])) unset($data['dateEnd']);

        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function update($id, $data)
    {
        /** @var UserService $service */
        $service = $this->getService();

        // Security: Don't allow changing role, password, status and history fields. To clean later.
        if (isset($data['status'])) unset($data['status']);
        if (isset($data['id'])) unset($data['id']);
        if (isset($data['salt'])) unset($data['salt']);
        if (isset($data['updatedAt'])) unset($data['updatedAt']);
        if (isset($data['updater'])) unset($data['updater']);
        if (isset($data['createdAt'])) unset($data['createdAt']);
        if (isset($data['creator'])) unset($data['creator']);
        if (isset($data['dateStart'])) unset($data['dateStart']);
        if (isset($data['dateEnd'])) unset($data['dateEnd']);

        $service->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    public function delete($id)
    {
        /** @var UserService $service */
        $service = $this->getService();

        $service->delete($id);
        return new JsonModel(array('status' => 'ok'));
    }
}

