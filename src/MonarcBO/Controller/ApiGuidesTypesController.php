<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

class ApiGuidesTypesController extends AbstractController
{
    protected $name = 'type';

    /**
     * Get List
     *
     * @return JsonModel
     */
    public function getList()
    {
        return new JsonModel(array(
            $this->name => $this->getService()->getTypes()
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

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }
}

