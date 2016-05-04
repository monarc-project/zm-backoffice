<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\JsonModel;

/**
 * Api Models Controller
 *
 * Class ApiModelsController
 * @package MonarcBO\Controller
 */
class ApiModelsController extends AbstractController
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
        $models =  $service->getList($page, $limit, $order, $filter);
        foreach($models as $key => $model) {
            $models[$key] = $model->toArray();
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($filter),
            'models' => $models
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
        $service->create($data);

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

