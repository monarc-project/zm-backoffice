<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Vulnerabilities Controller
 *
 * Class ApiAssetsController
 * @package MonarcBO\Controller
 */
class ApiVulnerabilitiesController extends AbstractController
{
    protected $name = 'vulnerabilities';
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

        $vulnerabilities = $service->getList($page, $limit, $order, $filter);
        foreach($vulnerabilities as $key => $vulnerability){
            $vulnerability['models']->initialize();
            $models = $vulnerability['models']->getSnapshot();
            $vulnerabilities[$key]['models'] = array();
            foreach($models as $model){
                $vulnerabilities[$key]['models'][] = $model->getJsonArray();
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            $this->name => $vulnerabilities
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
        $vulnerability = $this->getService()->getEntity($id);
        $vulnerability['models']->initialize();
        $models = $vulnerability['models']->getSnapshot();
        $vulnerability['models'] = array();
        foreach($models as $model){
            $vulnerability['models'][] = $model->getJsonArray();
        }

        return new JsonModel($vulnerability);
    }


}

