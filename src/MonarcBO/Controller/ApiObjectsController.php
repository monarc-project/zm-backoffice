<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Controller
 *
 * Class ApiObjectsController
 * @package MonarcBO\Controller
 */
class ApiObjectsController extends AbstractController
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

        $objects = $this->getService()->getList($page, $limit, $order, $filter);

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter),
            'objects' => $objects
        ));
    }

}

