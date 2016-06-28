<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiScalesCommentsController extends AbstractController
{
    protected $dependencies = ['scale', 'scaleTypeImpact'];
    protected $name = 'comments';

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
        $type = $this->params()->fromRoute('type');

        $comments = $this->getService()->getList($page, $limit, $order, $filter, ['anr' => $anrId, 'type' => $type]);
        foreach($comments as $key => $type){
            $this->formatDependencies($comments[$key], $this->dependencies);
        }

        return new JsonModel(array(
            'count' => count($comments),
            $this->name => $comments
        ));
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

