<?php

namespace MonarcBO\Controller;

use MonarcBO\Service\ServerService;
use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiAdminServersController extends AbstractController
{
    protected $name = 'servers';

    /**
     * Get list
     *
     * @return JsonModel
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $status = $this->params()->fromQuery('status');
        if (is_null($status)) {
            $status = 1;
        }
        $filterAnd = ($status == "all") ? null : ['status' => (int) $status] ;

        $service = $this->getService();

        $entities = $service->getList($page, $limit, $order, $filter, $filterAnd);
        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter, $filterAnd),
            $this->name => $entities
        ));
    }

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

