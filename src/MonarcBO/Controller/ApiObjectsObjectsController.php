<?php

namespace MonarcBO\Controller;

use MonarcCore\Controller\AbstractController;
use Zend\View\Model\JsonModel;

/**
 * Api Objects Components Controller
 *
 * Class ApiObjectsObjectsController
 * @package MonarcBO\Controller
 */
class ApiObjectsObjectsController extends AbstractController
{
    protected $componentsService;

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
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

