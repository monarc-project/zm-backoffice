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
        foreach($assets as $k => $v){
            $v['models']->initialize();
            $mods = $v['models']->getSnapshot();
            $assets[$k]['models'] = array();
            foreach($mods as $m){
                $assets[$k]['models'][] = $m->getJsonArray();
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

