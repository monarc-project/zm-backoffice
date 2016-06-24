<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiScalesController extends AbstractController
{
    protected $dependencies = ['anr'];

    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        $page = $this->params()->fromQuery('page');
        $limit = $this->params()->fromQuery('limit');
        $order = $this->params()->fromQuery('order');
        $filter = $this->params()->fromQuery('filter');
        $anrId = (int) $this->params()->fromRoute('anrId');

        $scales = $this->getService()->getList($page, $limit, $order, $filter, ['anr' => $anrId]);
        foreach($scales as $key => $scale){
            $this->formatDependencies($scales[$key], $this->dependencies);
        }

        return new JsonModel(array(
            'count' => $this->getService()->getFilteredCount($page, $limit, $order, $filter, ['anr' => $anrId]),
            'scales' => $scales
        ));
    }
}

