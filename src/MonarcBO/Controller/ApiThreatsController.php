<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Threats Controller
 *
 * Class ApiThreatsController
 * @package MonarcBO\Controller
 */
class ApiThreatsController extends AbstractController
{
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

        $service = $this->getService();
        $threats =  $service->getList($page, $limit, $order, $filter);
        foreach($threats as $key => $threat) {
            $threats[$key] = $threat->toArray();
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter),
            'threats' => $threats
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
        return new JsonModel($this->getService()->getEntity($id)->toArray());
    }

    /**
     * Create
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {
        $service = $this->getService();
        $id = $service->create($data);

        return new JsonModel(
            array(
                'status' => 'ok',
                'id' => $id,
            )
        );
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

    /**
     * Update
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function update($id, $data)
    {
        $service = $this->getService();
        $service->update($id, $data);

        return new JsonModel(array('status' => 'ok'));
    }

}

