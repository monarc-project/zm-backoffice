<?php

namespace MonarcBO\Controller;

use MonarcBO\Service\ServerService;
use MonarcBO\Controller\ApiAdminServersController;
use Zend\View\Model\JsonModel;

class ApiAdminServersGetController extends ApiAdminServersController
{
    public function create($data)
    {
        $this->methodNotAllowed();
    }
    public function delete($id){
        $this->methodNotAllowed();
    }
    public function deleteList($data){
        $this->methodNotAllowed();
    }
    public function update($id, $data){
        $this->methodNotAllowed();
    }
    public function patch($id, $data){
        $this->methodNotAllowed();
    }
}

