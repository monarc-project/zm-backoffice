<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Controller
 *
 * Class ApiObjectsController
 * @package MonarcBO\Controller
 */
class ApiObjectsController extends AbstractController
{
    protected $dependencies = ['category', 'asset', 'rolfTag'];

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
        $filter = (int) $this->params()->fromQuery('filter');

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter),
            'objects' => $this->getService()->getList($page, $limit, $order, $filter)
        ));
    }

    /**
     * Get
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function get($id)
    {
        $objectCategory = $this->getService()->getEntity($id);

        $this->formatDependencies($objectCategory, $this->dependencies);

        return new JsonModel($objectCategory);
    }

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {
        $id = $this->getService()->create($data);

        return new JsonModel(
            array(
                'status' => 'ok',
                'id' => $id,
            )
        );
    }

    /**
     * Update
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function update($id, $data)
    {
        $this->getService()->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

    /**
     * Delete
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function delete($id)
    {
        $service = $this->getService();
        $service->delete($id);

        return new JsonModel(array('status' => 'ok'));
    }
}

