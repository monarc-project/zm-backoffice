<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Assets Controller
 *
 * Class ApiAssetsController
 * @package MonarcBO\Controller
 */
class ApiAssetsController extends AbstractController
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

        $assets = $service->getList($page, $limit, $order, $filter);
        foreach($assets as $key => $asset){
            $asset['models']->initialize();
            $models = $asset['models']->getSnapshot();
            $assets[$key]['models'] = array();
            foreach($models as $model){
                $assets[$key]['models'][] = $model->getJsonArray();
            }
        }
        
        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            'assets' => $assets
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
        return new JsonModel($this->getService()->getEntity($id));
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
        $this->getService()->delete($id);

        return new JsonModel(array('status' => 'ok'));
    }

}

