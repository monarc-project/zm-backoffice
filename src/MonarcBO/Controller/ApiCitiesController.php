<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Cities Controller
 *
 * Class ApiCitiesController
 * @package MonarcBO\Controller
 */
class ApiCitiesController extends AbstractController
{
    protected $name = 'cities';

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
        $country_id = $this->params()->fromQuery('country_id');

        $service = $this->getService();

        if (is_null($country_id)) {
            $entities = $service->getList($page, $limit, $order, $filter);
        }
        else {
            $entities = $service->getList($page, $limit, $order, $filter, array('country_id' => $country_id));
        }

        if (count($this->dependencies)) {
            foreach ($entities as $key => $entity) {
                $this->formatDependencies($entities[$key], $this->dependencies);
            }
        }

        return new JsonModel(array(
            'count' => $service->getFilteredCount($page, $limit, $order, $filter),
            $this->name => $entities
        ));
    }
}
