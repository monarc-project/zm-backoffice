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
    protected $dependencies = ['theme'];
    protected $name = 'threats';

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

            $this->formatDependencies($threats[$key], $this->dependencies);
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            $this->name => $threats
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

        $this->formatDependencies($threat, $this->dependencies);

        return new JsonModel($threat);
    }
}

