<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Themes Controller
 *
 * Class ApiThemesController
 * @package MonarcBO\Controller
 */
class ApiThemesController extends AbstractController
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
        $themes =  $service->getList($page, $limit, $order, $filter);

        foreach($themes as $key => $theme) {
            $themes[$key] = $theme->toArray();
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            'themes' => $themes
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

