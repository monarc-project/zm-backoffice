<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Risks Controller
 *
 * Class ApiObjectsRisksController
 * @package MonarcBO\Controller
 */
class ApiObjectsRisksController extends AbstractController
{
    protected $dependencies = ['object', 'amv', 'asset', 'threat', 'vulnerability'];

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

        $risks = $this->getService()->getList($page, $limit, $order, $filter);
        foreach($risks as $key => $risk){
            $this->formatDependencies($risks[$key], $this->dependencies);
        }

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter),
            'risks' => $risks
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
        $objectRisk = $this->getService()->getEntity($id);

        $this->formatDependencies($objectRisk, $this->dependencies);

        return new JsonModel($objectRisk);
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

