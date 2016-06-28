<?php

namespace MonarcBO\Controller;

use MonarcBO\Service\ServerService;
use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminServersController extends AbstractController
{
    protected $name = 'servers';

    public function create($data)
    {
        /** @var ServerService $service */
        $service = $this->getService();

        // Security: Don't allow changing role, password, status and history fields. To clean later.
        if (isset($data['updatedAt'])) unset($data['updatedAt']);
        if (isset($data['updater'])) unset($data['updater']);
        if (isset($data['createdAt'])) unset($data['createdAt']);
        if (isset($data['creator'])) unset($data['creator']);

        $service->create($data);

        return new JsonModel(array('status' => 'ok'));
    }
}

