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

        $threats = $service->getList($page, $limit, $order, $filter);
        foreach($threats as $key => $threat){
            $threat['models']->initialize();
            $models = $threat['models']->getSnapshot();
            $threats[$key]['models'] = array();
            foreach($models as $model){
                $threats[$key]['models'][] = $model->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
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
        $threat = $this->getService()->getEntity($id);

        $threat['models']->initialize();
        $models = $threat['models']->getSnapshot();
        $threat['models'] = array();
        foreach($models as $model){
            $threat['models'][] = $model->getJsonArray();
        }

        /*
        $threat['theme']->initialize();
        $theme = $threat['theme']->getSnapshot();
        $threat['theme'] = $theme->getJsonArray();
        */

        return new JsonModel($threat);
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

